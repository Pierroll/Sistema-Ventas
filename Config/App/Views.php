<?php
// Config/App/Views.php

// Importa excepción (ya lo tienes)
use App\Config\Exceptions\ViewNotFoundException;

// FIX: Importa base de vistas si es común (opcional)
use App\Views\BaseView;  // Si creas una clase base para vistas

class Views {
    // FIX: Removida propiedad no usada ($viewsDir)

    public function getView(string $ruta, string $vista, array $data = []): void {  // Tipado para Sonar
        // Validar entradas (ya lo tienes, bien)
        if (empty($ruta) || empty($vista) || !is_string($ruta) || !is_string($vista)) {
            throw new InvalidArgumentException('Ruta y vista deben ser strings no vacíos.');
        }

        // Sanitizar (ya lo tienes)
        $ruta = preg_replace('/[^a-zA-Z0-9\/_-]/', '', $ruta);
        $vista = preg_replace('/[^a-zA-Z0-9_-]/', '', $vista);

        if ($ruta == 'home') {
            $view = "Views/index.php";
        } else {
            $view = "Views/" . $ruta . "/" . $vista . ".php";
        }
        if (file_exists($view)) {
            require_once $view;
        } else {
            echo "Error, no existe la vista: " . $view;
        }

    }
}
