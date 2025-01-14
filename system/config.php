<?php

// Set the default timezone to São Paulo
date_default_timezone_set('America/Sao_Paulo');

/**
 * Constant definitions for environment URLs and database connection settings.
 *
 * - `PRODUCTION_URL`: Base URL for the production environment (leave empty for actual use).
 * - `DEVELOPMENT_URL`: Base URL for the development environment, typically a local server.
 * - `URL_SITE_DEVELOPMENT`: URL for the site in the development environment.
 * - `URL_SITE_PRODUCTION`: URL for the site in the production environment.
 * - `URL_API_DEVELOPMENT`: Base URL for the API in the development environment.
 * - `URL_API_PRODUCTION`: Base URL for the API in the production environment.
 * - `URL_API_MOVIE_POSTER`: API URL for fetching movie poster data from The Movie Database (TMDb).
 * - `DB_HOST`: The hostname of the database server (typically `localhost`).
 * - `DB_PORT`: The port number of the database server (default is `3306` for MySQL).
 * - `DB_NAME`: The name of the database used for transactions.
 * - `DB_USERNAME`: The username for connecting to the database.
 * - `DB_PASSCODE`: The password for connecting to the database (leave empty if not used).
 */

// Base URLs for production and development environments
define('PRODUCTION_URL', ''); // Base URL for the production environment (leave empty for actual use)
define('DEVELOPMENT_URL', 'http://localhost/l5-test'); // Base URL for the local development environment

// URLs for the site in different environments
define('URL_SITE_DEVELOPMENT', '/l5-test/'); // Site URL in the development environment
define('URL_SITE_PRODUCTION', '/'); // Site URL in the production environment

// URLs for the API in different environments
define('URL_API_DEVELOPMENT', '/l5-test/api/'); // API URL in the development environment
define('URL_API_PRODUCTION', '/api/'); // API URL in the production environment

// API URL for fetching movie posters from The Movie Database (TMDb)
define('URL_API_MOVIE_POSTER', 'https://api.themoviedb.org/3/search/movie?query='); // URL for fetching movie poster data
define('URL_YOUTUBE_MOVIE_TRAILER', 'https://www.youtube.com/results?search_query='); // URL for fetching movie poster data

// Database connection settings
define('DB_HOST', 'localhost'); // Database server host (typically localhost)
define('DB_PORT', '3306'); // Port used for MySQL connections
define('DB_NAME', 'l5transactions'); // Database name
define('DB_USERNAME', 'root'); // Database username
define('DB_PASSCODE', ''); // Database password (leave empty if not used)


