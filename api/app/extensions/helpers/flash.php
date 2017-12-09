<?php
/**
 *
 * Extension para el manejo de mensajes para servicios REST API
 *
 * @category    Flash
 * @package     Helpers
 *  
 */

class Flash {      

    /**
     * Setea un mensaje de error para el cliente REST
     *     
     * @param string $msg Mensaje a mostrar
     * @param boolean $statusCode Almacena el código de error del mensaje para ser utilizado en las cabeceras
     */
    public static function set($msg, $statusCode = 400) {
        throw new RestException($msg, $statusCode);
    }    
    
    /**
     * Se mantiene por legacy pero no se usa
     */
    public static function output() {
        return;
    }   

    /**
     * Muestra el mensaje de error
     *
     * @param string $msg Mensaje a mostrar
     * @param boolean $statusCode Código de error en la cabecera
     */
    public static function error($msg, $statusCode = 400) {
        self::set($msg, $statusCode);
    }

    /**
     * Mensaje de advertencia
     * Se mantiene por compatibilidad pero no aplica para los servicios REST
     */
    public static function warning($msg) {
        self::error($msg);
    }
    
    /**
     * Mensaje de información
     * Se mantiene por compatibilidad pero no aplica para los servicios REST
     */
    public static function info($msg) {
        return;
    }

    /**
     * Mensaje válido
     * Se mantiene por compatibilidad pero no aplica para los servicios REST
     */
    public static function valid($msg) {
        return;
    }

}
