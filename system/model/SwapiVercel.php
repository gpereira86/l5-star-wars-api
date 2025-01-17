<?php

namespace system\model;

use system\core\SwApiModel;

/**
 * Represents the SwApiVercel class.
 *
 * An extension of the SwApiModel that provides interaction with the Star Wars API (SWAPI vercel).
 * Implements the ApiInterface to ensure compatibility with API standards.
 */
class SwapiVercel extends SwApiModel implements ApiInterface
{
    protected string $baseUrl;
    protected string $filmsEndpoint = "films/";
    protected string $peopleEndpoint = "people/";
    protected bool $adjustKeysParam = true;

    /**
     * SwapiVercel constructor.
     *
     * Initializes the class by setting the base URL for the Star Wars API (SWAPI) hosted on Vercel.
     * Calls the parent constructor to ensure proper initialization.
     */
    public function __construct()
    {
        $this->baseUrl = 'https://swapi-node.vercel.app/api/';
    }

    /**
     * Restructures the raw data retrieved from the API.
     *
     * Processes the raw data array by flattening nested 'fields' arrays into the main data structure.
     * If the 'results' key exists, each result is processed individually.
     * For single-item responses, the method flattens the 'fields' directly.
     *
     * @param array $rawData The raw data array returned by the API.
     * @return array The restructured data array.
     */
    protected function restructureData($rawData): array
    {
        if (isset($rawData['results']) && is_array($rawData['results'])) {
            foreach ($rawData['results'] as &$item) {
                if (isset($item['fields'])) {
                    foreach ($item['fields'] as $key => $value) {
                        $item[$key] = $value;
                    }
                    unset($item['fields']);
                }
            }
        } elseif (count($rawData) === 1) {
            $rawData = $rawData['fields'];
        } else {
            foreach ($rawData['results'] as &$item) {
                if (isset($item['fields'])) {
                    foreach ($item['fields'] as $key => $value) {
                        $item[$key] = $value;
                    }
                    unset($item['fields']);
                }
            }
        }

        return $rawData;
    }

    /**
     * Constructs a full URL by appending a given relative path to the base URL.
     *
     * Ensures that the base URL does not end with `/api/` or trailing slashes before concatenation.
     * This method is used to create complete URLs for API requests.
     *
     * @param string $url The relative path to append to the base URL.
     * @return string The fully constructed URL.
     */
    protected function buildUrl(string $url): string
    {
        return preg_replace('#/api/?$#', '', $this->baseUrl) . $url;
    }
}
