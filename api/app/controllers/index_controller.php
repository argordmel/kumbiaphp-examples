<?php

/**
 * Controller por defecto si no se usa el routes
 *
 */
class IndexController extends AppController {

    public function index() {

        View::select(NULL, '404');

    }

}
