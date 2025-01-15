<?php

namespace system\model;

use system\core\ApiInterface;
use system\core\SwApiModel;

/**
 * Class SwApiPy4E
 *
 * This class provides concrete implementations for interacting with the Star Wars API (SWAPI).
 * It extends the `SwApiModel` class to leverage core API functionalities and implements the `ApiInterface`
 * to ensure adherence to a defined API interaction contract.
 *
 * The class offers methods to interact with film data, including fetching information about films,
 * characters, and related metadata such as movie posters and trailers.
 *
 * Key Features:
 * - Retrieve and standardize film data, including metadata such as release date, episode, and characters.
 * - Fetch detailed information about a specific film by its ID.
 * - Retrieve movie posters and calculate the age of the film based on its release date.
 * - Query specific fields from the Star Wars API and return structured data.
 *
 * Example Usage:
 * - Retrieve all films: `getFilmsData()`
 * - Get details of a specific film by ID: `getFilmDetailById($id)`
 * - Fetch movie posters by movie name: `getPosterByMovieName($movieName)`
 * - Retrieve character names by their IDs: `getCharacterNamesByIds($ids)`
 */
class SwApiPy4E extends SwApiModel implements ApiInterface
{
    protected string $baseUrl;
    protected string $filmsEndpoint = "films/"; // Endpoint for film-related data.
    protected string $peopleEndpoint = "people/"; // Endpoint for character-related data.

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
        $this->baseUrl = 'https://swapi.py4e.com/api/'; // Star Wars API base URL
        parent::__construct($this->baseUrl); // Initialize the parent class with the base URL
    }

    /**
     * Retrieves and standardizes film data from the Star Wars API.
     *
     * This method calls the `standardizeFilmsData()` method to process and return film data
     * from the API in a standardized format.
     *
     * @return array An array containing the standardized film data.
     */
    public function getFilmsData(): array
    {
        return $this->standardizeFilmsData();
    }

    /**
     * Processes and standardizes film data from the API.
     *
     * This method fetches film data, character data, calculates film age, and includes additional metadata
     * such as movie posters. It processes raw data from the API into a more structured format.
     *
     * @param string|null $rawDataParameter Optional parameter specifying a film ID to fetch specific data.
     * @return array An associative array containing standardized film data with metadata such as title,
     * release date, episode details, directors, producers, characters, film age, and movie posters.
     */
    public function standardizeFilmsData(string $rawDataParameter = null): array
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $endpoint = $_SERVER['REQUEST_URI'];
        $responseCode = http_response_code();

        $data = [];

        // If rawDataParameter is provided, fetch data for a specific film.
        if (isset($rawDataParameter)) {
            $film = parent::fetchData($this->filmsEndpoint . $rawDataParameter . '/');

            if (empty($film) || !isset($film['title'])) {
                return [
                    'method' => $method,
                    'endpoint' => $endpoint,
                    'responseCode' => 404, // Return 404 if film not found
                    'data' => ['error' => 'Film not found or invalid data.']
                ];
            }

            // Process the film data and fetch related character IDs.
            $characterIds = isset($film['characters']) ? parent::getIdFromUrl($film['characters']) : [];

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
                'movieTrailer' => $this->searchedYoutubeMovieTrailerUrl($film['title'] ?? 'Unknown title', 'Star Wars'),
            ];
        } else {
            // If no parameter is provided, fetch all films.
            $rawData = parent::fetchData($this->filmsEndpoint);

            if (empty($rawData) || !isset($rawData['results'])) {
                return [
                    'method' => $method,
                    'endpoint' => $endpoint,
                    'responseCode' => 404, // Return 404 if no films found
                    'data' => ['error' => 'No films found.']
                ];
            }

            // Simplify the data structure for all films.
            $data = array_map(function ($film) {
                return [
                    'name' => $film['title'] ?? 'Unknown title',
                    'release_date' => $film['release_date'] ?? 'Release date not available',
                    'id' => isset($film['url']) ? parent::getIdFromUrl($film['url']) : 'ID not available',
                    'moviePoster' => 'Poster not available'
                ];
            }, $rawData['results']);
        }

        return [
            'method' => $method,
            'endpoint' => $endpoint,
            'responseCode' => $responseCode,
            'data' => $data,
        ];
    }

    /**
     * Retrieves detailed information about a film by its unique ID.
     *
     * This method acts as a wrapper around `standardizeFilmsData()` to fetch data
     * for a specific film by its ID.
     *
     * @param string $id The unique identifier of the film.
     * @return array An array containing the standardized film details.
     */
    public function getFilmDetailById(string $id): array
    {
        return $this->standardizeFilmsData($id);
    }

    /**
     * Retrieves the names of characters based on their unique IDs.
     *
     * This method maps character IDs to their corresponding names by querying
     * the 'people' endpoint.
     *
     * @param array|string $ids A single ID or an array of IDs to fetch character names.
     * @return array An array of character names.
     */
    public function getCharacterNamesByIds($ids): array
    {
        $characterIds = (array) $ids;
        $allCharacters = $this->getAllByField($this->peopleEndpoint, 'name');

        return array_map(fn($id) => $allCharacters[$id] ?? 'Unknown', $characterIds);
    }

    /**
     * Retrieves the movie poster by the movie name.
     *
     * This method returns the movie poster URL by querying an external service.
     * It returns a fallback message if the poster is unavailable.
     *
     * @param string $movieName The name of the movie for which to fetch the poster.
     * @return array An array containing the HTTP method, endpoint, response code, and movie poster data.
     */
    public function getPosterByMovieName(string $movieName): array
    {
        $method = 'GET';
        $endpoint = $_SERVER['REQUEST_URI'];
        $responseCode = 200;

        try {
            $moviePosterLink = $this->getLinkPosterByMovieName($movieName);

            if ($moviePosterLink === 'Poster not available') {
                $responseCode = 404;
            }

            return [
                'method' => $method,
                'endpoint' => $endpoint,
                'responseCode' => $responseCode,
                'data' => $moviePosterLink,
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
     * Retrieves all data from a specified endpoint and filters results by a specific field.
     *
     * This method queries the API endpoint and returns data based on the field provided.
     *
     * @param string $endPoint The API endpoint to fetch data from.
     * @param string $searchedField The field to filter the data by.
     * @return array An array containing the filtered data.
     */
    public function getAllByField(string $endPoint, string $searchedField): array
    {
        return $this->fetchAllFromEndpoint($endPoint, $searchedField);
    }

    /**
     * Retrieves the YouTube trailer URL for a specific movie.
     *
     * This method constructs a search URL for YouTube using the movie name and channel name
     * to find the trailer.
     *
     * @param string $movieName The name of the movie.
     * @param string $chanelName The name of the YouTube channel.
     * @return string The URL of the movie trailer.
     */
    public function searchedYoutubeMovieTrailerUrl(string $movieName, string $chanelName = ''): string
    {
        $params = 'Star+Wars%3A' . urlencode($movieName) . '%2F&search_query=trailer';
        $url = "https://www.youtube.com/results?search_query={$params}";

        return $url;
    }
}
