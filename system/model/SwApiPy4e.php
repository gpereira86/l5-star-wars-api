<?php

namespace system\model;

use system\core\SwApiModel;

/**
 * Represents the SwApiPy4e class.
 *
 * An extension of the SwApiModel that provides interaction with the Star Wars API (SWAPI py4e).
 * Implements the ApiInterface to ensure compatibility with API standards.
 */
class SwApiPy4e extends SwApiModel implements ApiInterface
{
    protected string $baseUrl;
    protected string $filmsEndpoint = "films/";
    protected string $peopleEndpoint = "people/";
    protected bool $adjustKeysParam = false;

    /**
     * SwApiPy4E constructor.
     *
     * Initializes the class by setting the base URL for the Star Wars API (SWAPI py4e).
     * Allows for a custom base URL, defaulting to 'https://swapi.py4e.com/api/'.
     *
     */
    public function __construct()
    {
        $this->baseUrl = $baseUrl ?? 'https://swapi.py4e.com/api/';
    }
}
