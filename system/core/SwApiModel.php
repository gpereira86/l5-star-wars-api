<?php

namespace system\core;

use system\core\ExternalApiConection;


/**
 * Main class for handling API interactions related to movies and related data.
 *
 * The `SwApiModel` simplifies fetching movie and poster data through APIs and
 * related operations, such as extracting IDs from URLs and performing custom calculations.
 *
 * SwApiModel: A utility class for API integration and movie data handling.
 *
 * This class provides functionalities for:
 * - Interacting with external APIs to retrieve movie and poster data.
 * - Calculating information based on retrieved data, like movie age.
 * - Processing results, such as mapping specific fields to IDs and extracting information from URLs.
 */
class SwApiModel
{
    protected string $baseUrl;
    protected string $posterUrl = URL_API_MOVIE_POSTER;


    /**
     * Constructor method to initialize the base URL.
     *
     * Initializes the base URL used for making API requests.
     *
     * @param string $url The base URL to be used for subsequent operations.
     * @return void
     */
    public function __construct(string $url)
    {
        $this->baseUrl = $url;
    }

    /**
     * This function retrieves data from an external API, using a specific endpoint and optionally making a request to a "Poster" API.
     *
     * It checks if the request is for fetching movie posters, and adjusts the URL accordingly.
     * Then it uses `ExternalApiConection::makeRequest()` to fetch the data.
     *
     * @param string $endpoint The API endpoint to be called.
     * @param bool $isPosterCall Indicates whether the call is to fetch poster data. Defaults to false.
     * @return mixed The response data from the external API on success, or false on failure.
     */
    private function getExternalApiData(string $endpoint, bool $isPosterCall = false)
    {
        if ($isPosterCall) {
            $url = $this->posterUrl . urlencode('Star Wars: '.$endpoint). "&api_key=" . FILM_IMAGE_API_KEY;
        } else {
            $url = $this->baseUrl . $endpoint;
        }

        $dataExternalApi = ExternalApiConection::makeRequest($url);

        // If data retrieval is successful, return it, otherwise return false
        if ($dataExternalApi) {
            return $dataExternalApi;
        }

        return false;
    }


    /**
     * Retrieves the poster image URL for a given movie name.
     *
     * Searches for the movie in the external API and extracts the poster image URL.
     * Returns the URL of the movie poster or a default message indicating the poster is not available.
     *
     * @param string $movieName The name of the movie for which the poster image URL is to be retrieved.
     * @return string The URL of the movie poster if available, or a default message indicating the poster is not available.
     */
    public function getPosterByMovieName(string $movieName): string
    {
        $movieData = json_decode($this->getExternalApiData($movieName, true), true);

        if (!empty($movieData['results'][0]['poster_path'])) {
            return "https://image.tmdb.org/t/p/w500" . $movieData['results'][0]['poster_path'];
        } else {
            return 'Poster not available';
        }
    }


    /**
     * Fetches data from the provided endpoint by making an API request.
     *
     * This method is used to fetch general data from a given API endpoint.
     * The response is decoded from JSON and returned as an associative array.
     *
     * @param string $endpoint The API endpoint to fetch data from.
     * @return array The response data retrieved from the API as an associative array.
     */
    public function fetchData(string $endpoint): array
    {
        return json_decode($this->getExternalApiData($endpoint), true);
    }

    /**
     * Fetches and processes all items from a paginated API endpoint, mapping specific fields to their respective IDs.
     *
     * Handles pagination by retrieving data from each page until all results are fetched.
     * Maps a specific field from the API response to the item ID.
     *
     * @param string $endpoint The base API endpoint URL to fetch data from.
     * @param string $fieldToMap The key of the field in the API response to map to the item's ID.
     * @return array An associative array where the keys are item IDs and the values are the mapped fields.
     */
    public function fetchAllFromEndpoint(string $endpoint, string $fieldToMap): array
    {
        $items = [];
        $nextPage = $endpoint;
        $page = 1;
        while ($nextPage) {
            $response = json_decode($this->getExternalApiData($nextPage), true);

            if (isset($response['results'])) {
                foreach ($response['results'] as $item) {
                    // Extract ID from the URL
                    $getId = $this->getIdFromUrl($item['url']);
                    $id = $getId[0] ?? null;
                    if ($id && isset($item[$fieldToMap])) {
                        $items[$id] = $item[$fieldToMap];
                    }
                }
            }

            $page += 1;
            $nextPage = isset($response['next']) ? $endpoint.'?page='. $page . '&format=json' : null;
        }

        return $items;
    }


    /**
     * Calculates the age of a film based on its release date.
     *
     * Uses the current date and the film's release date to calculate the age of the movie.
     * The result is formatted as years, months, and days.
     *
     * @param string $releaseDate The release date of the film in 'YYYY-MM-DD' format.
     * @return string The age of the film formatted as 'X years, Y months, Z days'.
     */
    public function calculateFilmAge(string $releaseDate): string
    {
        $release = new \DateTime($releaseDate);
        $now = new \DateTime();
        $interval = $now->diff($release);

        return $interval->format('%y years, %m months, %d days');
    }


    /**
     * Extracts numerical IDs from a given URL or array of URLs.
     *
     * This method extracts numeric IDs from URLs, which are typically used to identify specific items in an API response.
     * The method uses regular expressions to find and extract IDs in the form of digits at the end of the URL.
     *
     * @param string|array $urls A single URL as a string or an array of URLs from which IDs need to be extracted.
     * @return array An array of extracted numerical IDs.
     */
    public function getIdFromUrl($urls): array
    {
        $urls = (array) $urls;

        $ids = [];
        foreach ($urls as $url) {
            // Extract numerical ID from the URL
            if (preg_match('/(\d+)\/$/', $url, $matches)) {
                $ids[] = $matches[1];
            }
        }

        return $ids;
    }

}
