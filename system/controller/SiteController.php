<?php

namespace system\controller;

class SiteController
{

    public function index(){
        $this->renderHTML('./front-end/view/index.html');
    }

    public function movieDetailPage(string $movieName){

        $this->renderHTML('./front-end/view/movie-details.html');
    }

    public function errorPage(){

        $this->renderHTML('./front-end/view/error.html');
    }


    public function renderHTML($file, $data = []) {
        if (file_exists($file)) {
            extract($data);

            ob_start();
            include($file);
            $content = ob_get_clean();

            echo $content;
        } else {
            echo "Arquivo HTML não encontrado!";
        }
    }

}