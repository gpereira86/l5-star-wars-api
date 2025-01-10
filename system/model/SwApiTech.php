<?php

namespace system\model;

use system\model\ApiInterface;
use system\core\ExternalApiConection;
use system\core\SwApiModel;

class SwApiTech extends SwApiModel implements ApiInterface
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
        $this->baseUrl = 'https://www.swapi.tech/';
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

        $allCharacters = $this->getAllByField($this->peopleEndpoint, 'name');

        $data = [];

        if(isset($rawDataParameter)){
            $film = parent::fetchData($this->filmsEndpoint.$rawDataParameter.'/');
            $characterIds = parent::getIdFromUrl($film['characters']);
            $characterNames = array_map(fn($id) => $allCharacters[$id] ?? 'Unknown', $characterIds);

            $data[] = [
                'name' => $film['title'],
                'episode' => $film['episode_id'],
                'synopsis' => $film['opening_crawl'],
                'release_date' => $film['release_date'],
                'director' => $film['director'],
                'producers' => $film['producer'],
                'characters' => $characterNames,
                'film_age' => parent::calculateFilmAge($film['release_date']),
                'moviePoster' => parent::getPosterByMovieName($film['title'])
            ];

        } else {

            $rawData = parent::fetchData($this->filmsEndpoint);

            $data = array_map(function($film) {
                return [
                    'name' => $film['title'],
                    'release_date' => $film['release_date'],
                    'id' => parent::getIdFromUrl($film['url']),
                    'moviePoster' => parent::getPosterByMovieName($film['title'])
//                    'moviePoster' => ExternalApiConection::getPosterWithFilmName($film['title']),
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
     * Retrieves the detailed information of a film by its unique identifier.
     *
     * @param string $id The unique identifier of the film.
     * @return array An array containing the standardized film details.
     */
    public function getFilmDetailById(string $id): array
    {
        return $this->standardizeFilmsData($id);
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

}