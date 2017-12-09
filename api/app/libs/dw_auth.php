<?php
/**
 *
 * Clase que se utiliza para autenticar los usuarios
 *
 * @category    Sistema
 * @package     Libs
 */

class DwAuth {

    /**
     * Mensaje de Error
     *
     * @var String
     */
    protected static $_error = null;

    /**
    * Método para iniciar Sesion
    *
    * @param $fieldUser mixed Array con el nombre del campo en la bd del usuario y el valor
    * @param $fieldPass mixed Array con el nombre del campo en la bd de la contraseña y el valor
    * @return true/false
    */
    public static function login($fieldUser, $fieldPass) {

        //Verifico si envía el array array('usuario'=>'admin') o string 'usuario'
        $keyUser = (is_array($fieldUser))   ? @array_shift(array_keys($fieldUser))          : NULL;
        $keyPass = (is_array($fieldPass))   ? @array_shift(array_keys($fieldPass))          : NULL;
        $valUser = ($keyUser)               ? Filter::get($fieldUser[$keyUser], 'string')   : NULL;
        $valPass = ($keyPass)               ? Filter::get($fieldPass[$keyPass], 'string')   : NULL;

        if(empty($valUser) OR empty($valPass)) {
            self::setError("Ingresa el usuario y/o la contraseña");
            return false;
        }

        // TODO: revisar seguridad
        $password   = hash('sha1', $valPass);
        //$username = addslashes($username);
        $username   = filter_var($valUser, FILTER_SANITIZE_MAGIC_QUOTES);

        $Model = Load::model('usuario');
        $conditions = "email = '$username' AND password = '$password'";
        if ($user = $Model->find_first($conditions)) {
            return $user;
        }
        self::setError('El email y/o la contraseña son incorrectos.');
        return false;

    }

    /**
    * @return string
    */
    public static function getError() {
        return self::$_error;
    }

    /**
    * @param string $error
    */
    public static function setError($error) {
        self::$_error = $error;
    }

}