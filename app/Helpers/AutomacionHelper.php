<?php
namespace app\Helpers;

use app\Core\Database;

class AutomacionHelper {
    
    public static function ejecutarTareas() {
        // Solo ejecutar si ha pasado al menos 1 minuto desde el último control global
        $temp_file = sys_get_temp_dir() . '/sicap_last_check.txt';
        $ahora = time();
        
        if (file_exists($temp_file)) {
            $ultimo_check = (int)file_get_contents($temp_file);
            if (($ahora - $ultimo_check) < 60) { // 1 minuto de intervalo
                return;
            }
        }
        
        // Actualizar el tiempo del último check
        file_put_contents($temp_file, $ahora);
        
        self::forzarEjecucion();
    }

    public static function ejecutarTareasCron() {
        self::forzarEjecucion();
    }

    private static function forzarEjecucion() {
        // Ejecutar tareas
        self::detectarPermisosHoy();
        self::detectarInasistencias();
        self::detectarAlertasAsistencia();
    }

    private static function detectarAlertasAsistencia() {
        $db = new Database();
        $fecha_hoy = date('Y-m-d');
        
        // Obtener marcaciones de hoy
        $db->query("SELECT a.*, u.nombre 
                    FROM asistencia a 
                    JOIN usuarios u ON a.usuario_id = u.id 
                    WHERE DATE(a.registrado_en) = :fecha");
        $db->bind(':fecha', $fecha_hoy);
        $marcaciones = $db->resultSet();

        $alertaModel = new \app\Models\Alerta();
        
        foreach ($marcaciones as $m) {
            $fecha_str = date('Y-m-d', strtotime($m->registrado_en));
            $hora_str = date('H:i:s', strtotime($m->registrado_en));
            $fecha_format = date('d/m/Y', strtotime($m->registrado_en));
            
            $dia_semana = (int)date('N', strtotime($fecha_str));
            
            // Obtener horario
            $db->query("SELECT ch.hora_entrada, ch.hora_salida 
                        FROM configuracion_horarios ch 
                        JOIN usuarios u ON u.tipo_personal = ch.tipo_personal
                        WHERE u.id = :uid AND ch.dia_semana = :dia AND ch.activo = 1");
            $db->bind(':uid', $m->usuario_id);
            $db->bind(':dia', $dia_semana);
            $horario = $db->single();

            if (!$horario) continue;

            // Verificar si tiene permiso
            $db->query("SELECT COUNT(*) as total FROM solicitudes_permiso 
                        WHERE usuario_id = :uid AND estado = 'aprobada' AND DATE(fecha_permiso) = :fecha");
            $db->bind(':uid', $m->usuario_id);
            $db->bind(':fecha', $fecha_str);
            $tienePermiso = $db->single()->total > 0;

            if ($tienePermiso) continue;

            $hora_marcacion = strtotime($hora_str);
            $hora_entrada = strtotime($horario->hora_entrada);
            $hora_salida = strtotime($horario->hora_salida);
            $tolerancia = 600; // 10 minutos

            $hora_marcacion_fmt = date('h:i A', strtotime($m->registrado_en));
            $hora_entrada_prog = date('h:i A', strtotime($horario->hora_entrada));
            $hora_salida_prog = date('h:i A', strtotime($horario->hora_salida));

            // Identificadores únicos basados en el ID de la marcación física
            if ($m->tipo == 'entrada') {
                if ($hora_str > $horario->hora_entrada) {
                    $identificador = "asistencia_id_" . $m->id;
                    if (!$alertaModel->alertaYaExiste($m->usuario_id, 'Llegada Tarde', $identificador)) {
                        $alertaModel->registrarAlerta(
                            $m->usuario_id, 
                            'Llegada Tarde', 
                            "El empleado {$m->nombre} registró su ENTRADA tarde a las {$hora_marcacion_fmt} (su horario era a las {$hora_entrada_prog}) el día {$fecha_format}.",
                            $identificador,
                            $m->registrado_en
                        );
                    }
                }
            } elseif ($m->tipo == 'salida') {
                $identificador = "asistencia_id_" . $m->id;
                if ($hora_str < $horario->hora_salida) {
                    if (!$alertaModel->alertaYaExiste($m->usuario_id, 'Salida Anticipada', $identificador)) {
                        $alertaModel->registrarAlerta(
                            $m->usuario_id, 
                            'Salida Anticipada', 
                            "El empleado {$m->nombre} registró su SALIDA antes de tiempo a las {$hora_marcacion_fmt} (su horario de salida es a las {$hora_salida_prog}) el día {$fecha_format}.",
                            $identificador,
                            $m->registrado_en
                        );
                    }
                } elseif ($hora_marcacion > ($hora_salida + $tolerancia)) {
                    if (!$alertaModel->alertaYaExiste($m->usuario_id, 'Tardanza en Salir', $identificador)) {
                        $alertaModel->registrarAlerta(
                            $m->usuario_id, 
                            'Tardanza en Salir', 
                            "El empleado {$m->nombre} registró su SALIDA tarde a las {$hora_marcacion_fmt} (su horario de salida era a las {$hora_salida_prog}) el día {$fecha_format}.",
                            $identificador,
                            $m->registrado_en
                        );
                    }
                }
            }
        }
    }

    private static function detectarPermisosHoy() {
        $db = new Database();
        $db->query("SELECT p.*, u.nombre, m.nombre as motivo_nombre 
                    FROM solicitudes_permiso p 
                    JOIN usuarios u ON p.usuario_id = u.id 
                    JOIN motivos_permiso m ON p.motivo_id = m.id
                    WHERE p.estado = 'aprobada' AND DATE(p.fecha_permiso) = CURDATE()");
        $permisosHoy = $db->resultSet();
        
        $alertaModel = new \app\Models\Alerta();
        foreach ($permisosHoy as $p) {
            $identificador = "permiso_id_" . $p->id;
            if ($alertaModel->alertaYaExiste($p->usuario_id, 'Permiso Laboral', $identificador)) continue;

            $hora_fmt = date('h:i A', strtotime($p->hora_permiso));
            $descripcion = "El empleado {$p->nombre} tiene un permiso de {$p->motivo_nombre} aprobado para hoy a las {$hora_fmt} por {$p->horas_solicitadas} horas.";
            
            $alertaModel->registrarAlerta(
                $p->usuario_id, 
                'Permiso Laboral', 
                $descripcion,
                $identificador
            );
        }
    }

    private static function detectarInasistencias() {
        $db = new Database();
        $dia_semana_hoy = (int)date('N');
        $fecha_hoy = date('Y-m-d');
        
        $db->query("SELECT u.id, u.nombre, ch.hora_entrada 
                    FROM usuarios u 
                    JOIN configuracion_horarios ch ON u.tipo_personal = ch.tipo_personal 
                    WHERE u.activo = 1 
                    AND ch.dia_semana = :dia AND ch.activo = 1");
        $db->bind(':dia', $dia_semana_hoy);
        $quienesDebenTrabajar = $db->resultSet();

        $alertaModel = new \app\Models\Alerta();
        $hora_actual = date('H:i:s');

        foreach ($quienesDebenTrabajar as $emp) {
            // Solo alertar si ya pasó la hora de entrada configurada
            if ($hora_actual <= $emp->hora_entrada) continue;

            // Verificar si marcó entrada hoy
            $db->query("SELECT COUNT(*) as total FROM asistencia WHERE usuario_id = :uid AND DATE(registrado_en) = :fecha AND tipo = 'entrada'");
            $db->bind(':uid', $emp->id);
            $db->bind(':fecha', $fecha_hoy);
            if ($db->single()->total > 0) continue;

            // Verificar si tiene permiso aprobado para hoy
            $db->query("SELECT COUNT(*) as total FROM solicitudes_permiso WHERE usuario_id = :uid AND estado = 'aprobada' AND DATE(fecha_permiso) = :fecha");
            $db->bind(':uid', $emp->id);
            $db->bind(':fecha', $fecha_hoy);
            if ($db->single()->total > 0) continue;

            // Si llegamos aquí, es inasistencia
            $identificador = "inasistencia_fecha_" . $fecha_hoy;
            if (!$alertaModel->alertaYaExiste($emp->id, 'Inasistencia', $identificador)) {
                $alertaModel->registrarAlerta(
                    $emp->id, 
                    'Inasistencia', 
                    "El empleado {$emp->nombre} no ha registrado su entrada el día de hoy.",
                    $identificador
                );
            }
        }
    }
}
