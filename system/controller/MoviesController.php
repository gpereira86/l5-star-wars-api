<?php

namespace system\controller;

use system\model\LogModel;
use system\model\SwapiPy4e;

class MoviesController
{
    public function allFilms()
    {
        $app = new SwapiPy4e();
        $films = $app->getFilmsData();
        echo json_encode($films, JSON_PRETTY_PRINT);

    }

    public function filmsDetails(){
        $app = new SwapiPy4e();

        $films = $app->standardizeFilmsData();
        echo json_encode($films, JSON_PRETTY_PRINT);

    }

    public function filmsDetailsById(String $id){
        $app = new SwapiPy4e();

        $films = $app->getFilmDetailById($id);
        echo json_encode($films, JSON_PRETTY_PRINT);
    }


}