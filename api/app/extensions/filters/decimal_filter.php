<?php

/**
 * Dailyscript - Web | App | Media
 *
 * Filtro para convertir numeros en monedas
 *
 * @category    Extensions
 * @author      Iván D. Meléndez
 * @package     Filters
 * @copyright   Copyright (c) 2011 Dailyscript Team (http://www.dailyscript.co)
 * @version     1.0
 */
class DecimalFilter implements FilterInterface {

    /**
     * Ejecuta el filtro
     *
     * @param string $s
     * @param array $options
     * @return string
     */
    public static function execute($s, $options) {
        return number_format(empty($s) ? 0 : $s, 2, '.', ',');
    }

}