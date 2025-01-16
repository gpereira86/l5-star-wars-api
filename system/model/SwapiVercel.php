<?php

namespace system\model;

use system\core\ApiInterface;
use system\core\SwApiModel;

class SwapiVercel extends SwApiModel implements ApiInterface
{

    protected string $baseUrl;
    protected string $filmsEndpoint = "films";
    protected string $peopleEndpoint = "people";

    /**
     * SwApiPy4E constructor.
     *
     * Initializes the class by setting the base URL for the Star Wars API (SWAPI py4e).
     * The constructor then calls the parent constructor to set up the base URL.
     *
     * @return void
     */
    public function __construct()
    {
        $this->baseUrl = 'https://swapi-node.now.sh/api/';
    }



}