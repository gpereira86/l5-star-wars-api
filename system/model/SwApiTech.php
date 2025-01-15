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


    public function __construct()
    {
        $this->baseUrl = 'https://www.swapi.tech/';
        parent::__construct($this->baseUrl);
    }


    public function getFilmsData(): array
    {
        return $this->standardizeFilmsData();
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
                'moviePoster' => parent::getLinkPosterByMovieName($film['title'])
            ];

        } else {

            $rawData = parent::fetchData($this->filmsEndpoint);

            $data = array_map(function($film) {
                return [
                    'name' => $film['title'],
                    'release_date' => $film['release_date'],
                    'id' => parent::getIdFromUrl($film['url']),
                    'moviePoster' => parent::getLinkPosterByMovieName($film['title'])
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


    public function getFilmDetailById(string $id): array
    {
        return $this->standardizeFilmsData($id);
    }

    public function getAllByField(string $endPoint,string $searchedField): array
    {
        return $this->fetchAllFromEndpoint($endPoint, $searchedField);
    }

    public function keyAdjusting(array $data): array
    {
        $data['results'] = $data['properties'];

        unset($data['oldKey']);

        return $data;
    }

}