<?php
/**
 * KumbiaPHP web & app Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://wiki.kumbiaphp.com/Licencia
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@kumbiaphp.com so we can send you a copy immediately.
 *
 * @category   Kumbia
 * @package    Controller
 * @copyright  Copyright (c) 2005 - 2017 Kumbia Team (http://www.kumbiaphp.com)
 * @license    http://wiki.kumbiaphp.com/Licencia     New BSD License
 */

require_once CORE_PATH . 'kumbia/controller.php';

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
