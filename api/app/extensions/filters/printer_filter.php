<?php
/**
 * Dailyscript - Web | App | Media
 *
 * Filtra para caracteres latin en impresoras
 *
 * @category    Extensions
 * @author      Iván D. Meléndez
 * @package     Filters
 * @copyright   Copyright (c) 2010 Dailyscript Team (http://www.dailyscript.co)
 * @version     1.0
 */

class PrinterFilter implements FilterInterface {

    /**
     * Ejecuta el filtro para los string
     *
     * @param string $s
     * @param array $options
     * @return string
     */
    public static function execute ($s, $options) {
        $find = array('á', 'é', 'í', 'ó', 'ú',
                      'Á', 'É', 'Í', 'Ó', 'Ú',
                      'Ü', 'ü', 'Ñ','ñ', 'N°', "'");
        $replace = array('a', 'e', 'i', 'o', 'u',
                         'A', 'E', 'I', 'O', 'U',
                         'U', 'u', 'N', 'n', '#', '');
        $string = str_replace($find, $replace, $s);
        return $string;
    }

}
?>
