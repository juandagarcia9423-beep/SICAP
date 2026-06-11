<?php
namespace app\Core;

class Router {
    protected $currentController = 'AuthController';
    protected $currentMethod = 'login';
    protected $params = [];

    public function __construct() {
        $url = $this->getUrl();

        // Buscar en controladores el primer valor de la URL
        if (isset($url[0])) {
            $controllerName = ucwords($url[0]) . 'Controller';
            if (file_exists('../app/Controllers/' . $controllerName . '.php')) {
                $this->currentController = $controllerName;
                $this->currentMethod = 'index'; // Reiniciar método al cambiar controlador
                unset($url[0]);
            }
        }

        // Requerir el controlador
        $controllerClass = "\\app\\Controllers\\" . $this->currentController;
        $this->currentController = new $controllerClass;

        // Verificar la segunda parte de la URL (el método)
        if (isset($url[1])) {
            if (method_exists($this->currentController, $url[1])) {
                $this->currentMethod = $url[1];
                unset($url[1]);
            }
        }

        // Obtener parámetros
        $this->params = $url ? array_values($url) : [];

        // Llamar al método del controlador con los parámetros
        call_user_func_array([$this->currentController, $this->currentMethod], $this->params);
    }

    public function getUrl() {
        if (isset($_GET['url'])) {
            $url = rtrim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            $url = explode('/', $url);
            return $url;
        }
        return [];
    }
}
