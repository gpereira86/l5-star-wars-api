<?php

namespace system\controller;

use system\model\LogModel;
use system\model\SwApiPy4E;

class MoviesController
{
    public function allFilms()
    {
        $app = new SwApiPy4E();
        $films = $app->getFilmsData();
        echo json_encode($films, JSON_PRETTY_PRINT);

    }

    public function filmsDetails(){
        $app = new SwApiPy4E();

        $films = $app->standardizeFilmsData();
        echo json_encode($films, JSON_PRETTY_PRINT);

    }

    public function filmsDetailsById(String $id){
        $app = new SwApiPy4E();

        $films = $app->getFilmDetailById($id);
        echo json_encode($films, JSON_PRETTY_PRINT);
    }


}