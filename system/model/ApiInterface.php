<?php

namespace system\model;

/**
 * Interface for managing and retrieving information about a specific film or episode.
 *
 * This interface provides methods to access details such as the name, episode number,
 * synopsis, release date, director, producers, character names, and the age of the film.
 */
interface ApiInterface
{
     public function getFilmsData(): array;

    public function standardizeFilmsData(): array;

     public function getAllByField(string $endPoint, string $searchedField): array;

     public function getIdFromUrl($urls): array;


}