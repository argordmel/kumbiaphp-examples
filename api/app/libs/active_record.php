<?php

/**
 * @see KumbiaActiveRecord
 */
require_once CORE_PATH . 'libs/kumbia_active_record/kumbia_active_record.php';

/**
 * ActiveRecord
 *
 * Clase padre ActiveRecord para añadir tus métodos propios
 *
 * @category Kumbia
 * @package Db
 * @subpackage ActiveRecord
 */
class ActiveRecord extends KumbiaActiveRecord {

    // Se indica que se creen los logs
    protected $logger = true;

    /**
     * Método constructor
     * @param array $data
     */
    public function __construct($data = null) {
        $this->_changeDB();
        parent::__construct($data);
    }

    /**
     * Método para cambiar la bd
     */
    protected function _changeDB() {

        // Utilidad para cambiar la base de datos
        // y conexión dependiendo del host
        $currentHost = Input::server('HTTP_HOST');
        if (strpos($currentHost, "ivanmel") !== FALSE) {
            $this->set_database('development');
        } else if (strpos($currentHost, "staging") !== FALSE) {
            $this->set_database('staging');
        } else if (strpos($currentHost, "domain.com") !== FALSE) {
            $this->set_database('production');
        }

    }

    /**
     * Método para indicar en que sistema operativo se utiliza la base de datos
     * @param boolean $restore
     * @return string
     */
    protected function _getSystem($restore = false) {
        $sql = $this->sql("SHOW variables WHERE variable_name= 'basedir'");
        $result = mysqli_fetch_row($sql);
        $base = $result[1];
        $raiz = substr($base, 0, 1);
        if ($restore) { //Para restarurar
            $system = ($raiz == '/') ? 'mysql' : $base . '\bin\mysql';
        } else { //Para crear backup
            $system = ($raiz == '/') ? 'mysqldump' : $base . '\bin\mysqldump';
        }
        return $system;
    }

    /**
     * Método para obtener la configuración de conexión que depende del database utilizado
     * @return array
     */
    protected function _getConfig($source) {
        $database = Config::read('databases'); //Leo las conexiones existentes
        $config = $database[$source]; //Extraigo la conexion de la base de datos de la aplicacion
        return $config;
    }

    /**
     * Callback que se ejecuta antes de crear/actualizar una persona
     * @throws Exception
     */
    public function before_save() {

        // Filter fields into ActiveRecord
        $this->_filter_fields();
    }

    /**
     * Filter Fields
     */
    protected function _filter_fields() {

        // Recorro los tipos de campo de la tabla para hacer un filtrado de datos
        foreach ($this->_data_type as $field => $type) {

            // Si no está el campo en el objeto no lo filtre
            if (empty($this->$field)) {
                continue;
            }

            if (strpos($type, 'varchar') !== false) {
                $this->$field = Filter::get($this->$field, 'string');
            } else if (strpos($type, 'int') !== false) {
                $this->$field = Filter::get($this->$field, 'int');
            } else if (strpos($type, 'date') !== false) {
                $this->$field = Filter::get($this->$field, strpos($type, 'datetime') !== false ? 'string' : 'date');
            } else if (strpos($type, 'text') !== false) {
                $this->$field = Filter::get($this->$field, 'trim');
            } else if (strpos($type, 'decimal') !== false) {
                $this->$field = Filter::get($this->$field, 'numeric');
            }
        }
    }

}
