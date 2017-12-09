<?php

class UploadController extends RestController {

    /**
     * Metodo para subir un archivo o imagen
     */
    public function post() {

        $this->run(function() {

            $name = Input::get('name');
            $type = Input::get('type');

            $upload = new DwUpload($name, 'files');

            if($type === 'video') {
                $upload->setAllowedTypes('mkv|flv|ogg|ogv|avi|mov|wmv|mp4|mpeg|mpg|3gp?p|m4v|x-msvideo|quicktime');
            } else if($type === 'file') {
                $upload->setAllowedTypes('pdf|otd|ods|xls?x|doc?x|ppt?x|txt|cvs|sql|zip');
            } else {
                $upload->setAllowedTypes('png|jpg|gif|jpeg');
                $upload->setEncryptName(TRUE);
                $upload->setSize('3MB', 640, 480);
            }

            // Upload
            $data = $upload->save();
            if(!$data) {
                throw new RestException($upload->getError());
            }

            return $data;

        }, 201);

    }

}
