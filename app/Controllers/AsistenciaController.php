<?php
namespace app\Controllers;

use app\Core\Controller;
use app\Helpers\SesionHelper;

class AsistenciaController extends Controller {
    private $asistenciaModel;

    public function __construct() {
        SesionHelper::protegerRuta();
        $this->asistenciaModel = $this->model('Asistencia');
    }

    public function index() {
        // Capturar filtros desde GET
        $filtros = [
            'usuario_id' => $_GET['usuario_id'] ?? '',
            'fecha_inicio' => $_GET['fecha_inicio'] ?? '',
            'fecha_fin' => $_GET['fecha_fin'] ?? ''
        ];

        $marcaciones = $this->asistenciaModel->obtenerMarcaciones($filtros);
        
        // Cargar lista de usuarios para el selector del filtro
        $db = new \app\Core\Database();
        $conn = new \PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
        $usuarios = $conn->query("SELECT id, nombre, cedula FROM usuarios WHERE activo = 1 ORDER BY nombre ASC")->fetchAll(\PDO::FETCH_OBJ);

        $data = [
            'titulo' => 'Módulo de Asistencia',
            'marcaciones' => $marcaciones,
            'usuarios' => $usuarios,
            'filtros' => $filtros
        ];

        $this->view('asistencia/index', $data);
    }
}
