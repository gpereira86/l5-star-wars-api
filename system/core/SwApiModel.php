<?php

namespace system\core;

use system\core\ExternalApiConection;

class SwApiModel
{
    protected string $baseUrl;
    protected string $posterUrl = "https://api.themoviedb.org/3/search/movie?query=";


    /**
     * Constructor method to initialize the base URL.
     *
     * @param string $url The base URL to be used for subsequent operations.
     * @return void
     */
    public function __construct(string $url)
    {
        $this->baseUrl = $url;
    }

    /**
     * Retrieves data from an external API using the configured base URL.
     *
     * @return mixed Returns the API response data if the request is successful, or false if the request fails.
     */
    private function getExternalApiData(string $endpoint, bool $isPosterCall = false)
    {
        if ($isPosterCall) {
            $url = $this->posterUrl . urlencode('Star Wars: '.$endpoint). "&api_key=" . FILM_IMAGE_API_KEY;
        } else {
            $url = $this->baseUrl . $endpoint;
        }

        $dataExternalApi = ExternalApiConection::makeRequest($url);

        if ($dataExternalApi) {
            return $dataExternalApi;
        }

        return false;
    }


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
     * @param string $endpoint The API endpoint to fetch data from.
     * @return array The response data retrieved from the API as an associative array.
     */
    public function fetchData(string $endpoint): array
    {
        return json_decode($this->getExternalApiData($endpoint), true);
    }

    /**
     * Fetches and processes all items from a paginated API endpoint.
     *
     * @param string $endpoint The specific API endpoint to retrieve data from.
     * @param string $fieldToMap The field to extract and map from the response items.
     * @return array An associative array where keys are item IDs and values are the mapped field values.
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
     * Calculates the age of a film based on its release date and the current date.
     *
     * @param string $releaseDate The release date of the film in 'YYYY-MM-DD' format.
     * @return string A formatted string representing the time difference (e.g., years, months, and days) between the release date and the current date.
     */
    public function calculateFilmAge(string $releaseDate): string
    {
        $release = new \DateTime($releaseDate);
        $now = new \DateTime();
        $interval = $now->diff($release);

        return $interval->format('%y years, %m months, %d days');
    }


    public function getIdFromUrl($urls): array
    {
        $urls = (array) $urls;

        $ids = [];
        foreach ($urls as $url) {
            if (preg_match('/(\d+)\/$/', $url, $matches)) {
                $ids[] = $matches[1];
            }
        }

        return $ids;
    }

}

