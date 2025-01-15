<?php

/**
 * Set the default timezone to São Paulo.
 * This ensures that all date and time functions will use the specified timezone.
 */
date_default_timezone_set('America/Sao_Paulo');

/**
 * Base URLs for the production and development environments.
 * These constants define the base URLs for different environments to ensure proper routing.
 */
define('PRODUCTION_URL', ''); // Base URL for the production environment (leave empty for actual use)
define('DEVELOPMENT_URL', 'http://localhost/l5-test'); // Base URL for the local development environment

/**
 * URLs for the site in different environments.
 * These constants define the base URLs for site routing depending on the environment.
 */
define('URL_DEVELOPMENT', '/l5-test/'); // Site URL in the development environment
define('URL_PRODUCTION', '/'); // Site URL in the production environment

/**
 * API URL for fetching movie posters from The Movie Database (TMDb).
 * This URL is used to perform movie searches and fetch movie posters via the TMDb API.
 */
define('URL_API_MOVIE_POSTER', 'https://api.themoviedb.org/3/search/movie?query='); // URL for fetching movie poster data

/**
 * URL for searching YouTube movie trailers.
 * This URL is used to search for movie trailers on YouTube based on a movie's name.
 */
define('URL_YOUTUBE_MOVIE_TRAILER', 'https://www.youtube.com/results?search_query='); // URL for fetching movie trailer data from YouTube

/**
 * Database connection settings.
 * These constants define the necessary credentials and configuration for connecting to the database.
 */
define('DB_HOST', 'localhost'); // Database server host (typically localhost)
define('DB_PORT', '3306'); // Port used for MySQL connections
define('DB_NAME', 'l5transactions'); // Database name
define('DB_USERNAME', 'root'); // Database username
define('DB_PASSCODE', ''); // Database password (leave empty if not used)


