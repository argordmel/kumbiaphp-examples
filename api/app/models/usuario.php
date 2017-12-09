<?php


class Usuario extends ActiveRecord {

    /**
     * Usuario Activo
     */
    const ACTIVO = 1;

    /**
     * Usuario Inactivo
     */
    const INACTIVO = 0;


    /**
     * Método para abrir sesion
     * @param type $opt
     * @return boolean
     */
    public static function auth($user, $pass, $mode='auth') {

        // Autentico mediante la lib
        $usuario  = DwAuth::login(['login'=>$user], ['password'=>$pass]);
        if (!$usuario || empty($usuario->id)) {
            throw new RestException(DwAuth::getError(), 400);
        }

        // Si existe verifico toda la información
        $obj = new Usuario();
        $obj->getUsuario($usuario->id);

        if( (int) $obj->activo !== Usuario::ACTIVO) {
            throw new RestException("La cuenta actualmente se encuentra suspendida ($obj->activo)", 400);
        }

        //
        // Se arma el token para el cliente
        //

        // Private Info
        $token = [];
        $token['id'] = $obj->id;
        $token['email'] = $obj->email;

        // Public Info
        $output['nombre']= $obj->nombre;
        $output['email'] = $obj->email;
        $output['fotografia'] = $obj->fotografia;
        $output['token'] = DwJwt::encode($token);

        return $output;

    }

    /**
     * Callback que se ejecuta antes de crear/actualizar un usuario
     * @throws RestException
     */
    public function before_save() {

        // Se filtran los campos (libs/active_record)
        $this->_filter_fields();

        if($this->getUsuarioRegistrado()) {
            throw new RestException('El usuario ya se encuentra registrado.');
        }

    }

    /**
     * Método para revisar si existe un usuario con un nombre y apellido
     * @return int
     */
    public function getUsuarioRegistrado() {

        $condicion = "email = $this->email";
        $condicion.= (isset($this->id)) ? " AND id != $this->id" : '';
        return $this->count("conditions: $condicion");

    }

    /**
     *
     * @param string $method
     * @param type $data
     * @param type $optData
     * @return boolean
     */
    public static function setUsuario($method, $data, $optData=array()) {

        $usuario = new Usuario($data);
        if($optData) {
            $usuario->dump_result_self($optData);
        }

        // Para validar si se cambia o no la contraseña
        $cambiaPassword = true;

        // Verifico si se va a registrar el usuario
        if($method === 'update') {

            $obj = (new Usuario())->find_first($usuario->id);
            if(!$obj) {
                throw new RestException('Se ha producido un error en la verificación de los datos. Por favor intenta nuevamente.');
            }

            // Si actualiza la contraseña
            $oldpassword    = (!empty($usuario->oldpassword)) ? sha1($usuario->oldpassword) : null;

            if( $oldpassword && ($oldpassword != $obj->password) )  {
                throw new RestException("La contraseña anterior no coincide con la registrada. Verifica los datos e intenta nuevamente");
            }

            // Si no cambia la contraseña y para que pase la validación
            if(empty($usuario->password)) {
                $usuario->password = $obj->password;
                $usuario->repassword = $obj->password;
                $cambiaPassword = false;
            }

        }

        if($cambiaPassword && !empty($usuario->password) && !empty($usuario->repassword)) {
            if($usuario->password !== $usuario->repassword) {
                throw new RestException('Las contraseñas no coindiden');
            }
            $usuario->password  = sha1($usuario->password);
        }

        // Ejecuta el metodo create or update
        $result = $usuario->$method();
        return ($result) ? $usuario->getUsuario($usuario->id) : false;

    }

    /**
     * Retorna la información de un usuario
     * @param type $id
     * @return type
     */
    public function getUsuario($id) {

        $columns    = 'usuario.*';
        $conditions = "usuario.id = $id";

        return $this->find_first("columns: $columns", "conditions: $conditions");

    }

    /**
     * Método para obtener la información de los usuarios
     * @return object
     */
    public function getAllUsuarios() {

        // Cargo los parámetros recibios
        Params::load(func_get_args());

        // Parametros de paginación
        $page = Params::get('page');
        $per_page = Params::get('per_page');

        // Para las consultas
        $search = Params::get('s');

        $columns    = 'usuario.*';
        $order      = "usuario.nombre ASC";

        if (!empty($search)) {
            $conditions .= " AND (nombre LIKE '%$search%' OR email LIKE '%$search%')";
        }

        if ($page === 'all') {
            return $this->find("columns: $columns", "join: $join", "conditions: $conditions", "order: $order");
        }

        return $this->paginated("columns: $columns", "join: $join", "conditions: $conditions", "order: $order", "page: $page", "per_page: $per_page");

    }

}
