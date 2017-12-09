<?php

/**
 * Controlador para manejar peticiones REST
 *
 * Por defecto cada acción se llama como el método usado por el cliente
 * (GET, POST, PUT, DELETE...)
 * ademas se puede añadir mas acciones colocando delante el nombre del método
 * seguido del nombre de la acción put_cancel, post_reset...
 *
 * @category Kumbia
 * @package Controller
 * @author kumbiaPHP Team
 */

// Load excel
Load::lib('excel/dw_excel');

require_once APP_PATH . 'libs/rest.php';
class RestController extends Rest {

    /**
     * Modelo usado para el scaffold
     * @var string
     */
    protected $_model;

    /**
     * Remap Active Record Method
     */
    protected $_remap   = [];

    /**
     * Default output
     * @var string
     */
    protected $_fOutput = 'json';

    /**
     * Inicialización de la petición
     * ****************************************
     * Aqui debe ir la autenticación de la API
     * ****************************************
     */
    final protected function initialize() {

        if($this->module_name === 'auth' || $this->controller_name === 'auth') {
            return true;
        }

        // VALIDACIÓN DEL TOKEN
        $token  = Input::get('token') ? Input::get('token') : Input::server('HTTP_X_TOKEN_AUTH');
        $auth   = DwJwt::decode(empty($token) ? '' : $token);
        if(empty($auth->id)) {
            $this->data     = $this->error('Token inválido', 401);
            return false; // STOP EXCECUTION
        }


    }

    /**
     * Retorna un registro a través de su $id
     * metodo get objeto/:id
     */
    public function get($id) {

        if(empty($this->_model)) {
            $this->data = $this->error('Object not found', 404);
            return;
        }

        $this->run(function() use ($id) {
            $method = empty($this->_remap['get']) ? 'find_first' : $this->_remap['get'];
            return Load::model($this->_model)->$method((int) $id);
        });

    }

    /**
     * Lista los registros
     * metodo get objeto/
     */
    public function getAll() {

        if(empty($this->_model)) {
            $this->data = $this->error('Object not found', 404);
            return;
        }

        $this->run(function() {
            $page = Input::get('page');
            $per_page = Input::get('per_page');
            $method = empty($this->_remap['getAll']) ? 'find' : $this->_remap['getAll'];
            $order = Input::get('order');
            $model = Load::model($this->_model);
            if(empty($order)) {
                $order  = 'id ASC';
            } else {
                $order = explode('|', $order);
                $col = empty($order[0]) ? 'id'   : $order[0];
                $type = empty($order[1]) ? 'ASC'  : strtoupper($order[1]);
                if($type !== 'ASC' && $type !== 'DESC') {
                    $type   = 'ASC';
                }
                if(!in_array($col, $model->fields)) {
                    $col    = 'id';
                }
                $order  = "$col $type";
            }
            if(!empty($this->_remap['getAll'])) {
                return $model->$method(Input::get());
            } else if($page !== 'all') {
                return $model->paginated("order: $order", "page: $page", "per_page: $per_page");
            } else {
                return $model->$method("order: $order");
            }
        });

    }

    /**
     * Crea un nuevo registro
     * metodo post objeto/
     */
    public function post() {

        if(empty($this->_model) || empty($this->param())) {
            $this->data = $this->error('Object not found', 404);
            return;
        }

        $this->run(function() {
            $obj = Load::model($this->_model);
            $obj->dump_result_self($this->param());
            return ($obj->save()) ? $obj : [];
        }, 201);

    }

    /**
     * Modifica un registro por $id
     * metodo put objeto/:id
     */
    public function put($id) {

        if(empty($this->_model) || empty($this->param())) {
            $this->data = $this->error('Object not found', 404);
            return;
        }

        $this->run(function() use ($id) {
            $obj = Load::model($this->_model);
            $obj->find_first((int) $id);
            $obj->dump_result_self($this->param());
            return ($obj->update()) ? $obj : [];
        }, 202);

    }

    /**
     * Elimina un registro por $id
     * metodo delete objeto/:id
     */
    public function delete($id) {

        if(empty($this->_model)) {
            $this->data = $this->error('Object not found', 404);
            return;
        }

        $this->run(function() use ($id) {
            $id  = (int) $id;
            $obj = Load::model($this->_model);
            if(in_array('activo', $obj->fields)) { // Check if has a "soft delete"
                $obj->find_first("id = $id AND activo = 1", "order: id DESC");
                $obj->activo    = 0;
                $obj->update();
            } else if(in_array('active', $obj->fields)) { // Check if has a "soft delete"
                $obj->find_first("id = $id AND active = 1", "order: id DESC");
                $obj->active    = 0;
                $obj->update();
            } else { // "hard delete"
                $obj->delete($id);
            }
            return [];
        }, 202);

    }

    public function run(Closure $req, $statusCode=200) {

        try {
            $result = $req();
            $this->data = $this->response($result, $statusCode);
        } catch (Exception $ex) {
            $this->data = $this->exception($ex);
        }

    }


    final protected function finalize() {

    }

}