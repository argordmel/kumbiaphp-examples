<?php

Load::models('usuario');

class LoginController extends RestController {

    /**
     * Metodo para loguearse en el sistema
     */
    public function post() {

        $this->run(function() {

            $input  = $this->param();
            if(!empty($input['email']) && !empty($input['password'])) {

                $token  = Usuario::auth($input['email'], $input['password']);
                return $token;

            }

            throw new RestException('No se ha podido establecer tu email y/o contraseña', 400);

        });

    }

}
