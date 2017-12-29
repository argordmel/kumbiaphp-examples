<?php

/**
 * Clase para obtener un parámetro dentro de un array
 *
 * @category Kumbia
 * @package Controller
 * @author kumbiaPHP Team
 */
class Params {

    protected static $_data = [];

    public static function load($params=[]) {
        self::$_data = [];
        foreach($params AS $key => $value) {
            if(is_array($value)) {
                self::$_data = array_merge($value, self::$_data);
            } else {
                self::$_data[$key]  = $value;
            }
        }
    }

    public static function get($var='') {
        return self::getFilter(self::$_data, $var);
    }

    /**
     * Devuelve el valor dentro de un array con clave en formato uno.dos.tres
     * @param Array array que contiene la variable
     * @param string $str clave a usar
     * @return mixed
     */
    protected static function getFilter(Array $var, $str){
        if(empty($str)) {
            return filter_var_array($var);
        }
        $arr = explode('.', $str);
        $value = $var;
        foreach ($arr as $key) {
            if(isset($value[$key])){
                $value = $value[$key];
            } else{
                $value = NULL;
                break;
            }
        }
        return is_array($value) ? filter_var_array($value) : (is_bool($value) || is_null($value) ? $value : filter_var($value));
    }

    public static function validate($list, $toCheck) {

        $valid  = false;
        foreach ($toCheck AS $field) {
            $parts  = explode('.', $field);
            if(!in_array(Filter::get(end($parts), 'trim'), $list)) {
                $valid  = false;
                break;
            }
            $valid  = true;
        }
        return ($valid) ? $toCheck : [];

    }

}
