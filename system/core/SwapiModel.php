<?php

namespace system\core;

use system\core\ApiInterface;
use system\core\ExternalApiConection;

class SwapiModel implements ApiInterface
{

    protected $name;
    protected $EpisodeNumber;
    protected $Synopsis;
    protected $ReleaseDate;
    protected $Director;
    protected $Producers;
    protected $CharacterNames;
    protected $FilmAge;

    public function __construct(string $url)
    {
        ExternalApiConection::makeRequest($url);
    }

}

