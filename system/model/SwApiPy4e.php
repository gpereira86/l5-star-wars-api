<?php

namespace system\model;

use system\core\Helpers;
use system\core\SwApiModel;

/**
 * Class SwApiPy4E
 *
 * This class provides concrete implementations for interacting with the Star Wars API (SWAPI).
 * It extends the `SwApiModel` class to leverage core API functionalities and implements the `ApiInterface`
 * to ensure adherence to a defined API interaction contract.
 *
 * The class provides methods to:
 * - Retrieve and standardize film data with metadata such as movie posters and film age.
 * - Fetch detailed information about specific films or a list of all available films.
 *
 * Key features:
 * - Centralized handling of API requests through endpoint-specific methods.
 * - Transformation of raw API data into structured, application-ready formats.
 * - Additional functionality for retrieving movie posters and calculating film ages.
 *
 * Example usage:
 * - Retrieve all films: `getFilmsData()`
 * - Get details of a specific film by ID: `getFilmDetailById($id)`
 * - Query specific fields from any endpoint: `getAllByField($endpoint, $field)`
 */
class SwApiPy4E extends SwApiModel implements ApiInterface
{

    protected string $baseUrl;
    protected string $filmsEndpoint = "films/";
    protected string $peopleEndpoint = "people/";

    /**
     * Constructor method to initialize the base URL and call the parent constructor.
     * It is necessary to provide the API base URL. Example: https://swapi.py4e.com/api/
     *
     * @return void
     */
    public function __construct()
    {
        $this->baseUrl = 'https://swapi.py4e.com/api/';
        parent::__construct($this->baseUrl);
    }

    /**
     * Retrieves and standardizes film data from the API.
     *
     * @return array The array containing standardized film data.
     */
    public function getFilmsData(): array
    {
        return $this->standardizeFilmsData();
    }

    /**
     * Processes and standardizes film data based on provided raw data or retrieves all films data
     * if no specific parameter is supplied. The method fetches film details, character data, calculates
     * film age, and includes additional metadata such as movie posters.
     *
     * @param string|null $rawDataParameter An optional parameter specifying the raw data identifier for a specific film.
     * @return array An associative array containing metadata such as the HTTP method, the request endpoint,
     * HTTP response code, and the processed film data. The film data may include title, release date,
     * episode details, directors, producers, characters, film age, and associated movie posters.
     */
    public function standardizeFilmsData(string $rawDataParameter = null): array
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $endpoint = $_SERVER['REQUEST_URI'];
        $responseCode = http_response_code();

        $data = [];

        // If the rawDataParameter is provided
        if (isset($rawDataParameter)) {
            // Try to fetch the film data
            $film = parent::fetchData($this->filmsEndpoint . $rawDataParameter . '/');

            // Check if the film data is valid and contains the necessary keys
            if (empty($film) || !isset($film['title'])) {
                // If the data is not valid, return an error or fallback message
                return [
                    'method' => $method,
                    'endpoint' => $endpoint,
                    'responseCode' => 404, // Set an appropriate response code
                    'data' => ['error' => 'Film not found or invalid data.']
                ];
            }

            // Get the character IDs, with key check
            $characterIds = isset($film['characters']) ? parent::getIdFromUrl($film['characters']) : [];

            // Add the film data to the array
            $data[] = [
                'name' => $film['title'] ?? 'Unknown title',
                'episode' => $film['episode_id'] ?? 'Episode ID not available',
                'synopsis' => $film['opening_crawl'] ?? 'Synopsis not available',
                'release_date' => $film['release_date'] ?? 'Release date not available',
                'director' => $film['director'] ?? 'Unknown director',
                'producers' => $film['producer'] ?? 'Unknown producers',
                'characters' => $characterIds,
                'film_age' => isset($film['release_date']) ? parent::calculateFilmAge($film['release_date']) : 'Unknown film age',
                'moviePoster' => 'Poster not available',
                'movieTrailer' => $this->shearchedYoutubeMovietrailerUrl($film['title'] ?? 'Unknown title', 'Star Wars'),
            ];

        } else {
            // Otherwise, fetch all films
            $rawData = parent::fetchData($this->filmsEndpoint);

            // Check if the films data was fetched successfully
            if (empty($rawData) || !isset($rawData['results'])) {
                return [
                    'method' => $method,
                    'endpoint' => $endpoint,
                    'responseCode' => 404, // Set an appropriate response code
                    'data' => ['error' => 'No films found.']
                ];
            }

            // Map the raw data to a simpler structure
            $data = array_map(function ($film) {
                return [
                    'name' => $film['title'] ?? 'Unknown title',
                    'release_date' => $film['release_date'] ?? 'Release date not available',
                    'id' => isset($film['url']) ? parent::getIdFromUrl($film['url']) : 'ID not available',
                    'moviePoster' => 'Poster not available'
                ];
            }, $rawData['results']);
        }

        // Return the formatted data
        return [
            'method' => $method,
            'endpoint' => $endpoint,
            'responseCode' => $responseCode,
            'data' => $data,
        ];
    }



    /**
     * Retrieves the detailed information of a film by its unique identifier.
     *
     * @param string $id The unique identifier of the film.
     * @return array An array containing the standardized film details.
     */
    public function getFilmDetailById(string $id): array
    {
        return $this->standardizeFilmsData($id);
    }


    public function getCharacterNamesByIds($ids): array
    {
        $characterIds = (array) $ids;
        $allCharacters = $this->getAllByField($this->peopleEndpoint, 'name');

        return array_map(fn($id) => $allCharacters[$id] ?? 'Unknown', $characterIds);
    }

    public function getPosterByMovieName(string $movieName): array
    {
        $method = 'GET';
        $endpoint = URL_API_DEVELOPMENT.'movie/'.$movieName;
        $responseCode = 200;

        try {
            $moviePosterLink = $this->getLinkPosterByMovieName($movieName);

            if ($moviePosterLink === 'Poster not available'){
                $responseCode = 404;
            }

            return [
                'method' => $method,
                'endpoint' => $endpoint,
                'responseCode' => $responseCode,
                'data' => $this->getLinkPosterByMovieName($movieName),
            ];

        } catch (\Exception $e) {

            return [
                'method' => $method,
                'endpoint' => $endpoint,
                'responseCode' => 500,
                'error' => $e->getMessage(),
            ];

        }



    }


    /**
     * Retrieves all data from the given endpoint based on the specified field.
     * The method queries the specified endpoint and filters results by the provided field.
     *
     * @param string $endPoint The API endpoint to fetch data from.
     * @param string $searchedField The field used to filter the data.
     * @return array An array containing the filtered data.
     */
    public function getAllByField(string $endPoint,string $searchedField): array
    {
        return $this->fetchAllFromEndpoint($endPoint, $searchedField);
    }

    public function shearchedYoutubeMovietrailerUrl(string $movieName, string $chanelName='')
    {
        $params = 'Star+Wars%3A'.urlencode($movieName).'+trailer+channel%3'.urlencode($chanelName);

        return parent::getYoutubeLinkFound($params, true);

    }

}