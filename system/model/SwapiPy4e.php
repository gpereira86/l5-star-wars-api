<?php

namespace system\model;


use system\core\ExternalApiConection;
use system\core\SwapiModel;
use system\core\Helpers;


class SwapiPy4e extends SwapiModel implements ApiInterface
{

    protected string $baseUrl;
    protected string $filmsEndpoint = "films/";
    protected string $peopleEndpoint = "people/";
    protected string $planetsEndpoint = "planets/";
    protected string $speciesEndpoint = "species/";
    protected string $starshipsEndpoint = "starships/";
    protected string $vehiclesEndpoint = "vehicles/";

    public function __construct()
    {
        $this->baseUrl = 'https://swapi.py4e.com/api/';
        parent::__construct($this->baseUrl);
    }

    public function getFilmsData(): array
    {
        $standardizedData = $this->standardizeFilmsData();

        $formattedFilms = [];
        foreach ($standardizedData['data'] as $film) {
            $formattedFilms[] = [
                'id' => $film['episode'],
                'name' => $film['name'],
                'synopsis' => Helpers::summarizeText($film['synopsis'], 100),
                'release_date' => $film['release_date'],
                'film_age' => $film['film_age'],
                'moviePoster' => $film['moviePoster'],
            ];
        }

        $response = [
            'method' => $_SERVER['REQUEST_METHOD'],
            'endpoint' => $_SERVER['REQUEST_URI'],
            'responseCode' => http_response_code(),
            'data' => $formattedFilms,
        ];

        return $response;
    }

    public function standardizeFilmsData(string $rawDataParameter = null): array
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $endpoint = $_SERVER['REQUEST_URI'];
        $responseCode = http_response_code();

        $allCharacters = $this->getAllByField($this->peopleEndpoint, 'name');

        $data = [];

        if(isset($rawDataParameter)){
            $film = parent::fetchData($this->filmsEndpoint.$rawDataParameter.'/');
            $characterIds = $this->getIdFromUrl($film['characters']);
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
                'moviePoster' => ExternalApiConection::getPosterWithFilmName($film['title']),
            ];

        } else {

            $rawData = parent::fetchData($this->filmsEndpoint);

            foreach ($rawData['results'] as $film) {
                $characterIds = $this->getIdFromUrl($film['characters']);
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
                    'moviePoster' => ExternalApiConection::getPosterWithFilmName($film['title']),
                ];
            }
        }

        return [
            'method' => $method,
            'endpoint' => $endpoint,
            'responseCode' => $responseCode,
            'data' => $data,
        ];
    }

    public function getFilmDetailById(string $id): array
    {
        return $this->standardizeFilmsData($id);
    }

    public function getAllByField(string $endPoint,string $searchedField): array
    {
        return $this->fetchAllFromEndpoint($endPoint, $searchedField);
    }


    public function getIdFromUrl($urls):array
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

}