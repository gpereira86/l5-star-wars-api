<?php

namespace system\core;

use system\core\ExternalApiConection;

class SwapiModel
{
    protected string $baseUrl;

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
    protected function getExternalApiData()
    {
        $dataExternalApi = ExternalApiConection::makeRequest($this->baseUrl);

        if ($dataExternalApi) {
            return $dataExternalApi;
        }

        return false;
    }


    /**
     * Fetches data from the provided endpoint by making an API request.
     *
     * @param string $endpoint The API endpoint to fetch data from.
     * @return array The response data retrieved from the API as an associative array.
     */
    public function fetchData(string $endpoint): array
    {
        $url = $this->baseUrl . $endpoint;
        return json_decode(ExternalApiConection::makeRequest($url), true);
    }

    /**
     * Fetches data by appending the given ID to the endpoint and making a request.
     *
     * @param string $endpoint The API endpoint to which the ID will be appended.
     * @param string $id The unique identifier to fetch specific data.
     * @return array The response data fetched from the built endpoint.
     */
    public function fetchDataById(string $endpoint, string $id): array
    {
        $endpoint .= ($endpoint[-1] != '/') ? '/' : '';
        return $this->fetchData($endpoint . $id);
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
        $nextPage = $this->baseUrl . $endpoint;

        while ($nextPage) {
            $response = json_decode(ExternalApiConection::makeRequest($nextPage), true);

            if (isset($response['results'])) {
                foreach ($response['results'] as $item) {
                    preg_match('/(\d+)\/$/', $item['url'], $matches);
                    $id = $matches[1] ?? null;
                    if ($id && isset($item[$fieldToMap])) {
                        $items[$id] = $item[$fieldToMap];
                    }
                }
            }

            $nextPage = $response['next'] ?? null;
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




    //-------------------------------
    private function getIdFromUrl($urls):array
    {
        if (is_string($urls)) {
            $urls = [$urls];
        }

        $ids = [];

        foreach ($urls as $url) {
            preg_match('/(\d+)\/$/', $url, $matches);

            if (isset($matches[1])) {
                $ids[] = $matches[1];
            }
        }

        return $ids;
    }

    public function fetchSpecificDataFromFetchDataById(string $endpoint, array $urlWithIdOrIds, string $specificData): array
    {
        $endpoint .= ($endpoint[-1] != '/') ? '/' : '';
        $ids = $this->getIdFromUrl($urlWithIdOrIds);
        $specificDataArray = [];
        foreach ($ids as $value) {
            $data = $this->fetchData($endpoint . $value);
            $specificDataArray[] = $data[$specificData];
        }
        return $specificDataArray;
    }

}

