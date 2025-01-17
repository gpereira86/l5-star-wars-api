<?php

namespace system\core;

use system\core\ExternalApiConnection;


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
abstract class SwApiModel
{
    protected string $baseUrl;
    protected string $posterUrl = URL_API_MOVIE_POSTER;
    protected string $youtubeUrl = URL_YOUTUBE_MOVIE_TRAILER;

 
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
        $response = $this->getExternalApiData($endpoint);

        if ($response === false) {
            error_log("Error fetching data from endpoint: " . $endpoint);
            return [];
        }

        $data = json_decode($response, true);

        if ($this->adjustKeysParam) {
            $data = $this->restructureData($data);
        }

        if (is_null($data)) {
            error_log("Invalid JSON response for endpoint: " . $endpoint);
            return [];
        }

        return $data;
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

            if ($this->adjustKeysParam) {
                $response = $this->restructureData($response);
            }

            if (isset($response['results'])) {
                foreach ($response['results'] as $item) {
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
            if(empty(FILM_IMAGE_API_KEY)){
                return false;
            }
            $url = $this->posterUrl . urlencode('Star Wars: ' . $endpoint) . "&api_key=" . FILM_IMAGE_API_KEY;
        } elseif ($isYoutubeCall) {
            $url = $this->youtubeUrl . $endpoint;
        } else {
            $url = $this->baseUrl . $endpoint;
        }

        $dataExternalApi = ExternalApiConnection::makeRequest($url);

        return $dataExternalApi ? $dataExternalApi : false;
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
            return 'Poster not available';
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

        if (preg_match('/\/watch\?v=([a-zA-Z0-9_-]{11})/', $youtubeLink, $matches)) {
            $videoId = $matches[1];
            return 'https://www.youtube.com/watch?v=' . $videoId;
        } else {
            return "Youtube link not found";
        }
    }

    /**
     * Calculates the age of a film based on its release date.
     *
     * This method calculates the difference between the current date and the film's release date,
     * and returns the result as a formatted string showing years, months, and days.
     *
     * @param string $releaseDate The release date of the film in 'YYYY-MM-DD' format.
     * @return string The age of the film, formatted as 'X years, Y months, Z days'.
     * @throws \DateMalformedStringException
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
            $url = $this->buildUrl($url);
            $url .= ($url[-1] !== '/') ? '/' : '';

            if (preg_match('/(\d+)\/$/', $url, $matches)) {
                $ids[] = $matches[1];
            }
        }
        return $ids;
    }

    /**
     * Constructs a full URL by appending the given relative path to the base URL.
     * Ensures the base URL does not end with `/api/` or trailing slashes before concatenation.
     *
     * @param string $url The relative path to append to the base URL.
     * @return string The fully constructed URL.
     */
    protected function buildUrl(string $url): string
    {
        return $url;
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


        if (isset($rawDataParameter)) {
            $film = $this->fetchData($this->filmsEndpoint . $rawDataParameter . '/');

            if (empty($film) || !isset($film['title'])) {
                return [
                    'method' => $method,
                    'endpoint' => $endpoint,
                    'responseCode' => 404,
                    'data' => ['error' => 'Film not found or invalid data.']
                ];
            }

            $characterIds = isset($film['characters']) ? $this->getIdFromUrl($film['characters']) : [];

            $data[] = [
                'name' => $film['title'] ?? 'Unknown title',
                'episode' => $film['episode_id'] ?? 'Episode ID not available',
                'synopsis' => $film['opening_crawl'] ?? 'Synopsis not available',
                'release_date' => $film['release_date'] ?? 'Release date not available',
                'director' => $film['director'] ?? 'Unknown director',
                'producers' => $film['producer'] ?? 'Unknown producers',
                'characters' => $characterIds,
                'film_age' => isset($film['release_date']) ? $this->calculateFilmAge($film['release_date']) : 'Unknown film age',
                'moviePoster' => Helpers::url().'api/movie/'.rawurlencode($film['title']),
                'movieTrailer' => $this->searchedYoutubeMovieTrailerUrl($film['title'] ?? 'Unknown title', 'Star Wars'),
            ];
        } else {
            $rawData = $this->fetchData($this->filmsEndpoint);

            if (empty($rawData) || !isset($rawData['results'])) {
                return [
                    'method' => $method,
                    'endpoint' => $endpoint,
                    'responseCode' => 404,
                    'data' => ['error' => 'No films found.']
                ];
            }


            $data = array_map(function ($film) {
                return [
                    'name' => $film['title'] ?? 'Unknown title',
                    'release_date' => $film['release_date'] ?? 'Release date not available',
                    'id' => isset($film['url']) ? $this->getIdFromUrl($film['url']) : 'ID not available',
                    'moviePoster' => Helpers::url().'api/movie/'.rawurlencode($film['title'])
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

        return $this->getYoutubeLinkFound($url, true);
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


}