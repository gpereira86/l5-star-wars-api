<?php

namespace system\core;

use system\core\ExternalApiConection;


/**
 * Class SwApiModel
 *
 * Main class for handling API interactions related to movies and associated data.
 * The `SwApiModel` simplifies fetching movie and poster data through external APIs
 * and performing additional operations such as calculating movie age and processing
 * information from URLs.
 *
 * Key Functionalities:
 * - Interacting with external APIs to retrieve movie and poster data.
 * - Performing calculations based on retrieved data, such as movie age.
 * - Processing URLs to extract IDs and mapping specific fields to movie data.
 */
class SwApiModel
{
    protected string $baseUrl;
    protected string $posterUrl = URL_API_MOVIE_POSTER;
    protected string $youtubeUrl = URL_YOUTUBE_MOVIE_TRAILER;

    /**
     * Constructor to initialize the API base URL.
     *
     * Initializes the base URL for making API requests.
     *
     * @param string $url The base URL to be used for subsequent API operations.
     * @return void
     */
    public function __construct(string $url)
    {
        $this->baseUrl = $url;
    }

    /**
     * Retrieves data from an external API using a specific endpoint.
     * Optionally, it fetches movie poster data or YouTube trailer link if requested.
     *
     * @param string $endpoint The API endpoint to be called.
     * @param bool $isPosterCall Indicates if the request is to fetch movie poster data (default is false).
     * @param bool $isYoutubeCall Indicates if the request is to fetch YouTube trailer URL (default is false).
     * @return mixed The response data from the API, or false if the request fails.
     */
    private function getExternalApiData(string $endpoint, bool $isPosterCall = false, bool $isYoutubeCall = false)
    {
        if ($isPosterCall) {
            // Fetch poster URL
            $url = $this->posterUrl . urlencode('Star Wars: ' . $endpoint) . "&api_key=" . FILM_IMAGE_API_KEY;
        } elseif ($isYoutubeCall) {
            // Fetch YouTube trailer link
            $url = $this->youtubeUrl . $endpoint;
        } else {
            // Default API request
            $url = $this->baseUrl . $endpoint;
        }

        // Fetch data using ExternalApiConection
        $dataExternalApi = ExternalApiConection::makeRequest($url);

        return $dataExternalApi ? $dataExternalApi : false; // Return the fetched data, or false if failed.
    }

    /**
     * Retrieves the movie poster URL for a specific movie name.
     *
     * Searches the external API for the movie and returns the URL of the poster image.
     * If no poster is found, it returns a default message.
     *
     * @param string $movieName The name of the movie for which the poster URL is to be fetched.
     * @return string The URL of the movie poster or a message if not found.
     */
    public function getLinkPosterByMovieName(string $movieName): string
    {
        $movieData = json_decode($this->getExternalApiData($movieName, true), true);

        if (!empty($movieData['results'][0]['poster_path'])) {
            return "https://image.tmdb.org/t/p/w500" . $movieData['results'][0]['poster_path'];
        } else {
            return 'Poster not available'; // Return default message if poster is not found.
        }
    }

    /**
     * Retrieves the YouTube trailer URL for a specific movie based on its name.
     *
     * Searches for the movie's trailer on YouTube and extracts the trailer URL from the search results.
     * If a valid URL is found, it is returned; otherwise, an error message is returned.
     *
     * @param string $params The parameters to search for the movie trailer.
     * @param bool $isYoutube Indicates whether the request is for YouTube search (default is false).
     * @return string The URL of the trailer or an error message if not found.
     */
    public function getYoutubeLinkFound(string $params, bool $isYoutube = false): string
    {
        $youtubeLink = $this->getExternalApiData($params, false, $isYoutube);

        // Match YouTube video ID from the response
        if (preg_match('/\/watch\?v=([a-zA-Z0-9_-]{11})/', $youtubeLink, $matches)) {
            $videoId = $matches[1];
            return 'https://www.youtube.com/watch?v=' . $videoId;
        } else {
            return "Youtube link not found"; // Return message if YouTube link is not found.
        }
    }

    /**
     * Fetches data from a specific API endpoint.
     *
     * This method retrieves general data from the given endpoint, decodes the JSON response,
     * and returns it as an associative array. If the request fails, it logs an error.
     *
     * @param string $endpoint The API endpoint from which to fetch the data.
     * @return array The response data as an associative array, or an empty array if the request fails.
     */
    public function fetchData(string $endpoint): array
    {
        $response = $this->getExternalApiData($endpoint); // Fetch data from the API

        if ($response === false) {
            // Log error if the request fails
            error_log("Error fetching data from endpoint: " . $endpoint);
            return [];
        }

        $data = json_decode($response, true); // Decode JSON response

        if (is_null($data)) {
            // Log error if JSON response is invalid
            error_log("Invalid JSON response for endpoint: " . $endpoint);
            return [];
        }

        return $data; // Return the decoded data
    }

    /**
     * Fetches all items from a paginated API endpoint.
     *
     * This method handles pagination by making multiple requests to the API endpoint,
     * retrieving all pages of results. It maps a specified field to the item's ID.
     *
     * @param string $endpoint The API endpoint to fetch data from.
     * @param string $fieldToMap The key in the response to map to the item ID.
     * @return array An associative array where keys are item IDs and values are the mapped fields.
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
                    // Extract ID from the URL and map the specified field
                    $getId = $this->getIdFromUrl($item['url']);
                    $id = $getId[0] ?? null;
                    if ($id && isset($item[$fieldToMap])) {
                        $items[$id] = $item[$fieldToMap];
                    }
                }
            }

            $page++;
            $nextPage = isset($response['next']) ? $endpoint . '?page=' . $page . '&format=json' : null;
        }

        return $items;
    }

    /**
     * Calculates the age of a film based on its release date.
     *
     * This method calculates the difference between the current date and the film's release date,
     * and returns the result as a formatted string showing years, months, and days.
     *
     * @param string $releaseDate The release date of the film in 'YYYY-MM-DD' format.
     * @return string The age of the film, formatted as 'X years, Y months, Z days'.
     */
    public function calculateFilmAge(string $releaseDate): string
    {
        $release = new \DateTime($releaseDate);
        $now = new \DateTime();
        $interval = $now->diff($release);

        return $interval->format('%y years, %m months, %d days');
    }

    /**
     * Extracts numerical IDs from a given URL or an array of URLs.
     *
     * This method uses regular expressions to extract numerical IDs from the URLs,
     * which are typically used to identify specific items in the API response.
     *
     * @param string|array $urls A single URL as a string or an array of URLs from which to extract IDs.
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