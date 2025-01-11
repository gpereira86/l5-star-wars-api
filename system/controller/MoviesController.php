<?php

namespace system\controller;

use system\model\SwApiPy4E;

/**
 * MoviesController is responsible for handling requests related to films.
 *
 * It includes methods for fetching all films, getting detailed information
 * about films, and retrieving a specific film's details by ID.
 */
class MoviesController
{
    /**
     * Fetches data for all films.
     *
     * This method calls the `getFilmsData()` method from the SwApiPy4E model
     * to retrieve data about all films and outputs it in a formatted JSON response.
     */
    public function allFilms()
    {
        $app = new SwApiPy4E();  // Create instance of SwApiPy4E
        $films = $app->getFilmsData();  // Retrieve films data
        echo json_encode($films, JSON_PRETTY_PRINT);  // Output films data in JSON format
    }

    /**
     * Fetches and standardizes data for all films.
     *
     * This method calls the `standardizeFilmsData()` method from the SwApiPy4E model
     * to standardize the data format for all films and outputs it as a formatted JSON response.
     */
    public function filmsDetails()
    {
        $app = new SwApiPy4E();  // Create instance of SwApiPy4E

        $films = $app->standardizeFilmsData();  // Standardize films data
        echo json_encode($films, JSON_PRETTY_PRINT);  // Output standardized films data in JSON format
    }

    /**
     * Fetches details of a specific film by its ID.
     *
     * This method calls the `getFilmDetailById()` method from the SwApiPy4E model
     * to retrieve detailed information for a specific film, using the given film ID,
     * and outputs the details in a formatted JSON response.
     *
     * @param string $id The ID of the film for which details are to be fetched.
     */
    public function filmsDetailsById(String $id)
    {
        $app = new SwApiPy4E();  // Create instance of SwApiPy4E

        $films = $app->getFilmDetailById($id);  // Retrieve film details by ID
        echo json_encode($films, JSON_PRETTY_PRINT);  // Output film details in JSON format
    }
}
