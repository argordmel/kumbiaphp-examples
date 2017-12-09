<?php

/**
 * JSON Web Token implementation, based on this spec:
 * http://tools.ietf.org/html/draft-ietf-oauth-json-web-token-06
 *
 * PHP version 5
 *
 * @category Authentication
 * @package  Authentication_JWT
 * @author   Neuman Vong <neuman@twilio.com>
 * @author   Anant Narayanan <anant@php.net>
 * @author   Iván Meléndez <argordmel@gmail.com>
 * @license  http://opensource.org/licenses/BSD-3-Clause 3-clause BSD
 * @link     https://github.com/firebase/php-jwt
 * @link     https://coderwall.com/p/8wrxfw/goodbye-php-sessions-hello-json-web-tokens
 * @link     https://github.com/rmcdaniel/angular-codeigniter-seed
 *
 * Usage:
 *
 * 1) Instalation
 *    * composer require firebase/php-jwt
 *    * Edit the file public/index.php and uncomment/enable vendors:
 *    require_once("../../vendor/autoload.php");
 *
 * 2)  Define your KEY
 * define('JWT_KEY', 'aklasdfñ!sdljsdk45654@!');
 *
 * 3) Generate Token:
 *
 *   // Private Info
 *   $token = [];
 *   $token['id'] = 1;
 *   $token['var'] = 'value';
 *
 *   // Public Info
 *   $output['name'] = 'Iván Meléndez';
 *   $output['email'] = 'argordmel@gmail.com';
 *   $output['token'] = DwJwt::encode($token);
 *
 *   echo json_encode($output);
 *
 * 4) Validate Token in your controller (api_controller.php, rest_controller.php, app_controller.php, etc)
 *
 * Example: rest_controller.php
 *
 * public function before_initialize() {
 *
 *      // You can get the token via URL ?token=abc or via header: x-token-auth: 'abc'
 *      $token = Input::get('token') ? Input::get('token') : Input::server('HTTP_X_TOKEN_AUTH');
 *      $auth = DwJwt::decode(empty($token) ? '' : $token);
 *      if(empty($auth->id)) {
 *          $this->data     = $this->error('Token inválido', 401);
 *          return false; // STOP EXCECUTION
 *      }
 *  }
 *
 *
 */

use Firebase\JWT\JWT;

//
// Llave para cifrar el token.
// https://api.wordpress.org/secret-key/1.1/salt/
//

define('JWT_KEY', 'c-A@|+Hx}}S|A^pe@/Kl1}&q6Z-U)G.j@W$R8e7O1~|!!!U#B@$+bAc?nMjrgVs2');

class DwJwt {

    /**
     * Varriable para almacenar los datos descifrados
     */
    protected static $_data = [];


    /**
     * Get decode variable
     * @param type $var
     * @return type
     */
    public static function get($var) {

        return isset(self::$_data[$var]) ? self::$_data[$var] : NULL;

    }

    /**
     *
     * @param type $token
     * @return type
     */
    public static function decode($token) {

        try {
            $payload = JWT::decode($token, JWT_KEY, array('HS256'));
            self::$_data = (array) $payload;
            return $payload;
        } catch (Exception $e) {
            self::$_data = [];
            return [];
        }
    }

    /**
     *
     * @param array $token
     * @return type
     */
    public static function encode($token) {
        return JWT::encode($token, JWT_KEY, 'HS256');
    }


}
