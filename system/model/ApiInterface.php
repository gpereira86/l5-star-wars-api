<?php

namespace system\core;

interface ApiInterface
{
    public function getName(): string;
    public function getEpisodeNumber(): int;
    public function getSynopsis(): string;
    public function getReleaseDate(): string;
    public function getDirector(): string;
    public function getProducers(): array;
    public function getCharacterNames(): array;
    public function getFilmAge(): string;

}