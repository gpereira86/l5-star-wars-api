<?php

namespace system\core;

/**
 * Interface ApiInterface
 *
 * This interface defines the contract for interacting with the API, ensuring that any class implementing it
 * provides specific methods to retrieve data related to films, characters, and other API-related data.
 *
 * Required methods:
 * - `getFilmsData()`: Retrieves standardized data about the films.
 * - `getFilmDetailById($id)`: Retrieves detailed information about a specific film using its ID.
 * - `getCharacterNamesByIds($ids)`: Retrieves the names of characters based on the provided IDs.
 * - `getPosterByMovieName($movieName)`: Retrieves the poster of a film based on the movie name.
 * - `getAllByField($endPoint, $searchedField)`: Retrieves all data from an endpoint and filters the results by a specific field.
 * - `searchedYoutubeMovietrailerUrl($movieName, $chanelName = '')`: Retrieves the YouTube trailer URL for a specific film.
 */
interface ApiInterface
{
    /**
     * Retrieves and standardizes film data.
     *
     * @return array An array containing standardized film data.
     */
    public function getFilmsData(): array;

    /**
     * Retrieves details of a specific film by its ID.
     *
     * @param string $id The unique identifier of the film.
     * @return array An array containing the standardized details of the film.
     */
    public function getFilmDetailById(string $id): array;

    /**
     * Retrieves character names based on the provided IDs.
     *
     * @param array|string $ids A single ID or an array of IDs to retrieve character names.
     * @return array An array of character names.
     */
    public function getCharacterNamesByIds($ids): array;

    /**
     * Retrieves the poster of a film by the film's name.
     *
     * @param string $movieName The name of the film for which to retrieve the poster.
     * @return array An array containing the HTTP method, endpoint, response code, and film poster data.
     */
    public function getPosterByMovieName(string $movieName): array;

    /**
     * Retrieves all data from an endpoint and filters the results by a specific field.
     *
     * @param string $endPoint The API endpoint to fetch the data from.
     * @param string $searchedField The field used to filter the data.
     * @return array An array containing the filtered data.
     */
    public function getAllByField(string $endPoint, string $searchedField): array;

    /**
     * Retrieves the YouTube trailer URL for a specific film.
     *
     * @param string $movieName The name of the film.
     * @param string $chanelName The name of the YouTube channel (optional).
     * @return string The YouTube trailer URL of the film.
     */
    public function searchedYoutubeMovieTrailerUrl(string $movieName, string $chanelName = ''): string;

}
