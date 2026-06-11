<?php
namespace app\Core;

class Controller {
    // Cargar modelo
    public function model($model) {
        $modelClass = "\\app\\Models\\" . $model;
        return new $modelClass();
    }

    // Cargar vista
    public function view($view, $data = []) {
        if (file_exists('../views/' . $view . '.php')) {
            require_once '../views/' . $view . '.php';
        } else {
            die("La vista no existe.");
        }
    }
}
