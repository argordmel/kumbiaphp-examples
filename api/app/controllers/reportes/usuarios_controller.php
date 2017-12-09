<?php

Load::models('usuario');

class UsuariosController extends RestController {

    protected function before_filter() {

        View::response('xls');

    }

    public function getAll() {

        View::select('usuarios', NULL);

        $this->run(function() {

            // Mezclamos los parametros pueden llegar por url
            $params = array_merge(Input::get(), ['page'=>'all']);
            return (new Usuario())->getAllUsuarios($params);
        });

    }

}
