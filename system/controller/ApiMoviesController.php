<?php

namespace system\controller;

use Exception;
use system\model\SwApiPy4E;
use system\core\Helpers;

/**
 * ApiMoviesController is responsible for handling requests related to films.
 *
 * It includes methods for fetching all films, getting detailed information
 * about films, and retrieving a specific film's details by ID.
 */
class ApiMoviesController
{
    // Instance of SwApiPy4E to be used by all methods
    private $app;
    private $log;

    /**
     * Constructor for ApiMoviesController
     * Initializes the SwApiPy4E instance.
     */
    public function __construct()
    {
        $this->app = new SwApiPy4E();  // Create instance of SwApiPy4E
        $this->log = new DbRegisterController(); // Create instance of LogModel
    }

    /**
     * Fetches data for all films.
     *
     * This method calls the `getFilmsData()` method from the SwApiPy4E model
     * to retrieve data about all films and outputs it in a formatted JSON response.
     */
    public function allFilms()
    {
        try {
            $films = $this->app->getFilmsData();  // Retrieve films data
            $responseCode = $films['responseCode'];

            Helpers::sendResponse($films);  // Output films data in JSON format
        } catch (Exception $e) {
            // Handle error, returning a response with status 500 and error message
            $responseCode = 500;
            Helpers::sendResponse(['error' => 'An unexpected error occurred.', 'details' => $e->getMessage()], 500);
        } finally {

            $this->log->saveLogRegister([
                'request_method' => 'GET',
                'endpoint' => '/api/films',
                'response_code' => $responseCode
            ]);

        }
    }

    /**
     * Fetches and standardizes data for all character names by IDs.
     *
     * This method calls the `getCharacterNamesByIds()` method from the SwApiPy4E model
     * to standardize the data format for character names and outputs it as a formatted JSON response.
     */
    public function allCharacterNamesByIds()
    {
        // Verify if the request method is POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Helpers::sendResponse(['error' => 'Method not allowed.'], 405);
        }

        // Get and decode the input data
        $data = json_decode(file_get_contents('php://input'), true);

        // Validate input data
        if (empty($data) || !is_array($data)) {
            Helpers::sendResponse(['error' => 'At least one valid ID is required to proceed.'], 400);
        }

        try {
            // Fetch character names using the provided IDs
            $names = $this->app->getCharacterNamesByIds($data);

            if ($names) {
                $responseCode = 200;
                Helpers::sendResponse([
                    'message' => 'Request for names successfully completed.',
                    'charactersnames' => $names
                ],200);
            } else {
                $responseCode = 404;
                Helpers::sendResponse(['error' => 'Failed to fetch character names.'], 404);
            }
        } catch (Exception $e) {
            // Handle exception with a response
            $responseCode = 500;
            Helpers::sendResponse(['error' => 'An unexpected error occurred.', 'details' => $e->getMessage()], 500);
        } finally {

            $this->log->saveLogRegister([
                'request_method' => 'POST',
                'endpoint' => '/api/characters-names',
                'response_code' => $responseCode
            ]);
        }
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
    public function filmsDetailsById(string $id)
    {
        try {
            // Retrieve film details by ID
            $filmDetails = $this->app->getFilmDetailById($id);
            $responseCode = $filmDetails['responseCode'];
            Helpers::sendResponse($filmDetails);  // Output film details in JSON format
        } catch (Exception $e) {
            // Handle exception for fetching film details
            $responseCode = 500;
            Helpers::sendResponse(['error' => 'An unexpected error occurred.', 'details' => $e->getMessage()], 500);
        } finally {

            $this->log->saveLogRegister([
                'request_method' => 'GET',
                'endpoint' => '/api/films/details/'.$id,
                'response_code' => $responseCode
            ]);
        }
    }



    public function getMoviePosterByName(string $movieName)
    {
        try {
            $poster = $this->app->getPosterByMovieName($movieName);

            if ($poster) {
                $responseCode = $poster['responseCode'];
                Helpers::sendResponse($poster);
            } else {
                $responseCode = 404;
                Helpers::sendResponse(['error' => 'Failed to fetch movie name.'], 404);
            }

        } catch (Exception $e) {
            $responseCode = 500;
            Helpers::sendResponse(['error' => 'An unexpected error occurred.', 'details' => $e->getMessage()], 500);
        } finally {

            $this->log->saveLogRegister([
                'request_method' => 'GET',
                'endpoint' => '/api/movie/'.$movieName,
                'response_code' => (string)$responseCode
            ]);
        }

    }

    public function errorLogRegister(array $data) {
        $this->log->saveLogRegister([
            'request_method' => $data['request_method'],
            'endpoint' => $data['endpoint'],
            'response_code' => $data['response_code']
        ]);
    }


}
