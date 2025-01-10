<?php

namespace system\model;


/**
 * Interface ApiInterface
 *
 * This interface defines the contract for classes that interact with external APIs, ensuring that
 * they implement methods to retrieve and manipulate data in a standardized way.
 */
interface ApiInterface
{
    /**
     * Method to retrieve and standardize film data.
     *
     * @return array The array containing the standardized film data.
     */
    public function getFilmsData(): array;

    /**
     * Method to process and standardize film data based on provided raw data or retrieves all
     * film data if no specific parameter is supplied.
     *
     * @param string|null $rawDataParameter An optional parameter specifying the raw data identifier
     *                                      for a specific film.
     * @return array An associative array containing metadata such as the HTTP method, the request
     *               endpoint, HTTP response code, and the processed film data. The film data may include
     *               title, release date, episode details, directors, producers, characters, film age,
     *               and associated movie posters.
     */
    public function standardizeFilmsData(string $rawDataParameter = null): array;

    /**
     * Method to retrieve detailed information of a film by its unique identifier.
     *
     * @param string $id The unique identifier of the film.
     * @return array An array containing the standardized film details.
     */
    public function getFilmDetailById(string $id): array;

    /**
     * Method to retrieve all data from the specified endpoint based on the provided field.
     *
     * @param string $endPoint The API endpoint from which data will be fetched.
     * @param string $searchedField The field used to filter the data.
     * @return array An array containing the filtered data.
     */
    public function getAllByField(string $endPoint, string $searchedField): array;
}
