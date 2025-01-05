<?php

namespace system\controller;

class SiteConroller
{

    public function index(){
        $this->renderHTML('./front-end/view/base.html');
    }

    public function renderHTML($file, $data = []) {
        if (file_exists($file)) {
            extract($data);

            ob_start();
            include($file);
            $content = ob_get_clean();

//            include('header.php'); // Cabeçalho
            echo $content; // Exibe o conteúdo principal
//            include('footer.php'); // Rodapé
        } else {
            echo "Arquivo HTML não encontrado!";
        }
    }

}