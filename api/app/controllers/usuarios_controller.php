<?php

Load::models('usuario');

class UsuariosController extends RestController {

    protected function before_filter() {

    }

    public function getAll() {

        $this->run(function() {

            // Mezclamos los parametros pueden llegar por url
            $params = array_merge(Input::get(), ['page'=>'all']);
            return (new Usuario())->getAllUsuarios($params);
        });

    }

}
