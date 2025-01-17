<?php

namespace system\controller;

use Exception;
use system\model\SwApiPy4e;
use system\model\SwapiVercel;
use system\core\Helpers;

/**
 * ApiMoviesController is responsible for handling movie-related requests.
 *
 * It includes methods to retrieve all movies, obtain detailed information about movies,
 * and get details of a specific movie by its ID.
 */
class ApiMoviesController
{
    private $app;
    private $log;
    private $userIP;

    /**
     * ApiMoviesController constructor.
     * Initializes the necessary objects to handle movie data and logging.
     */
    public function __construct()
    {

        // Software Scalability: It is possible to connect other external APIs with a simple adaptation
        // of some fields using polymorphism (when necessary) and by replacing the instantiation.
        // $this->app = new SwapiVercel(); // Example: uncomment this line and comment the next one.
        $this->app = new SwApiPy4e();

        $this->log = new DbRegisterController();
        $this->userIP = $this->getUserIP();

    }

    /**
     * Retrieves data for all movies.
     *
     * This method calls the `getFilmsData()` method from the `SwApiPy4e` model to fetch data about all movies
     * and returns it in JSON format. If an error occurs, an error message is returned with the appropriate response code.
     */
    public function allFilms()
    {
        try {
            $films = $this->app->getFilmsData();
            $responseCode = $films['responseCode'];

            Helpers::sendResponse($films);
        } catch (Exception $e) {
            $responseCode = 500;
            Helpers::sendResponse(['error' => 'An unexpected error occurred.', 'details' => $e->getMessage()], 500);
        } finally {
            $this->log->saveLogRegister([
                'request_method' => 'GET',
                'endpoint' => '/api/films',
                'response_code' => $responseCode,
                'user_ip' => $this->userIP ?? 'N/A'
            ]);
        }
    }

    /**
     * Retrieves and standardizes data for character names based on provided IDs.
     *
     * This method requires a POST request with a body containing an array of valid IDs.
     * It calls the `getCharacterNamesByIds()` method from the `SwApiPy4e` model to get the character names
     * corresponding to the provided IDs and returns the data in JSON format. If the IDs are invalid or the request
     * fails, the response will include an error message.
     */
    public function allCharacterNamesByIds()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Helpers::sendResponse(['error' => 'Method not allowed.'], 405);
        }

        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data) || !is_array($data)) {
            Helpers::sendResponse(['error' => 'At least one valid ID is required to proceed.'], 400);
        }

        try {
            $names = $this->app->getCharacterNamesByIds($data);

            if ($names) {
                $responseCode = 200;
                Helpers::sendResponse([
                    'message' => 'Character names request completed successfully.',
                    'charactersnames' => $names
                ], 200);
            } else {
                $responseCode = 404;
                Helpers::sendResponse(['error' => 'Failed to retrieve character names.'], 404);
            }
        } catch (Exception $e) {
            $responseCode = 500;
            Helpers::sendResponse(['error' => 'An unexpected error occurred.', 'details' => $e->getMessage()], 500);
        } finally {
            $this->log->saveLogRegister([
                'request_method' => 'POST',
                'endpoint' => '/api/characters-names',
                'response_code' => $responseCode,
                'user_ip' => $this->userIP ?? 'N/A'
            ]);
        }
    }

    /**
     * Retrieves details for a specific movie by its ID.
     *
     * This method calls the `getFilmDetailById()` method from the `SwApiPy4e` model to fetch detailed information about a
     * movie, using the provided movie ID. The details are returned in JSON format. If an error occurs during retrieval,
     * an error message will be returned with the appropriate response code.
     *
     * @param string $id The ID of the movie for which details should be retrieved.
     */
    public function filmsDetailsById(string $id)
    {
        try {
            $filmDetails = $this->app->getFilmDetailById($id);
            $responseCode = $filmDetails['responseCode'];
            Helpers::sendResponse($filmDetails);
        } catch (Exception $e) {
            $responseCode = 500;
            Helpers::sendResponse(['error' => 'An unexpected error occurred.', 'details' => $e->getMessage()], 500);
        } finally {
            $this->log->saveLogRegister([
                'request_method' => 'GET',
                'endpoint' => '/api/films/details/'.$id,
                'response_code' => $responseCode,
                'user_ip' => $this->userIP ?? 'N/A'
            ]);
        }
    }

    /**
     * Retrieves a movie poster based on the movie name.
     *
     * This method calls the `getPosterByMovieName()` method from the `SwApiPy4e` model to fetch the movie poster image
     * based on the provided movie name. If the poster is found, it is returned in JSON format. If the poster is not found,
     * an error will be returned with the appropriate response code.
     *
     * @param string $movieName The name of the movie for which the poster should be retrieved.
     */
    public function getMoviePosterByName(string $movieName)
    {
        try {
            $poster = $this->app->getPosterByMovieName($movieName);

            if ($poster) {
                $responseCode = $poster['responseCode'];
                Helpers::sendResponse($poster);
            } else {
                $responseCode = 404;
                Helpers::sendResponse(['error' => 'Failed to retrieve the movie name.'], 404);
            }
        } catch (Exception $e) {
            $responseCode = 500;
            Helpers::sendResponse(['error' => 'An unexpected error occurred.', 'details' => $e->getMessage()], 500);
        } finally {

            $this->log->saveLogRegister([
                'request_method' => 'GET',
                'endpoint' => '/api/movie/'.$movieName,
                'response_code' => (string)$responseCode,
                'user_ip' => $this->userIP ?? 'N/A'
            ]);
        }
    }

    /**
     * Registers a log for a request.
     *
     * This method receives details about a request (such as HTTP method, endpoint, and response code) and
     * logs this information into the database for audit or monitoring purposes.
     *
     * @param array $data Request data, including 'request_method', 'endpoint', and 'response_code'.
     */
    public function logRegister(array $data) {
        $this->log->saveLogRegister([
            'register_date' => date('Y-m-d H:i:s'),
            'request_method' => $data['request_method'],
            'endpoint' => $data['endpoint'],
            'response_code' => $data['response_code'],
            'user_ip' => $this->userIP ?? 'N/A'
        ]);
    }

    /**
     * Function to get the user's IP address.
     *
     * This function identifies and returns the IP address of the user accessing the page. It considers scenarios
     * where the user may be behind a proxy, load balancer, or accessing directly.
     *
     * The IP will be identified in the following order:
     * 1. `HTTP_CLIENT_IP` - When the user is behind a proxy.
     * 2. `HTTP_X_FORWARDED_FOR` - To get the user's real IP if they are behind a reverse proxy.
     * 3. `REMOTE_ADDR` - The client's direct IP.
     *
     * @return string The user's IP address.
     */
    private function getUserIP() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }


}
