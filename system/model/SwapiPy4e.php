<?php

namespace system\model;

use system\core\ApiInterface;
use system\core\ExternalApiConection;
use system\core\SwapiModel;

class SwapiPy4e extends SwapiModel
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

    public function standardizeFilmsData(): array
    {
        $allCharacters = $this->getAllCharacters();
        $rawData = parent::fetchData($this->filmsEndpoint);
        $standardized = [];
        foreach ($rawData['results'] as $film) {
            $characterIds = $this->getIdFromUrl($film['characters']);
            $characterNames = array_map(fn($id) => $allCharacters[$id] ?? 'Unknown', $characterIds);

            $standardized[] = [
                'name' => $film['title'],
                'episode' => $film['episode_id'],
                'synopsis' => $film['opening_crawl'],
                'release_date' => $film['release_date'], // formato do retorno da API: "1977-05-25"
                'director' => $film['director'],
                'producers' => $film['producer'],
                'characters' => $characterNames,
                'film_age' => parent::calculateFilmAge($film['release_date']),
            ];
        }
        return $standardized;
    }

    public function getAllCharacters(): array
    {
        return $this->fetchAllFromEndpoint($this->peopleEndpoint, 'name');
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