<?php

use system\controller\DbRegisterController;
use system\controller\ApiMoviesController;
use system\controller\SiteController;
use system\core\Helpers;

/**
 * Define application routes and handles requests based on the URI and request method.
 *
 * This function defines routes for both the site and API endpoints.
 * It maps the incoming URI and request method to the appropriate controller and action,
 * or responds with a 404 error if no route matches.
 *
 * @param string $uri The current request URI.
 * @param string $requestMethod The HTTP method of the current request (e.g., 'GET', 'POST').
 * @return void
 */
function defineRoutes($uri, $requestMethod)
{
    // Set the base URIs for the API and site based on the environment (development or production)
    if (Helpers::localhost()) {
        // Use the development base URIs if running on localhost
        $baseApiUri = URL_API_DEVELOPMENT;
        $baseSiteUri = URL_SITE_DEVELOPMENT;

    } else {
        // Use the production base URIs for all other environments
        $baseApiUri = URL_API_PRODUCTION;
        $baseSiteUri = URL_SITE_PRODUCTION;

    }

    // ===> Handle Site Routes <===
    if (strpos($uri, 'api/') === false) {
        if ($uri === $baseSiteUri && $requestMethod === 'GET') {
            // Render the home page
            http_response_code(200);
            $siteController = new SiteController();
            $siteController->index();

        } elseif (preg_match("#^{$baseSiteUri}movie/([^/]+)$#", $uri, $matches) && $requestMethod === 'GET') {
            // Render the movie detail page
            http_response_code(200);
            $siteController = new SiteController();
            $siteController->movieDetailPage();

        } elseif ($uri === "{$baseSiteUri}error-page" && $requestMethod === 'GET') {
            // Render the error page
            http_response_code(404);
            $siteController = new SiteController();
            $siteController->errorPage();

        } else {
            // Redirect to the error page for undefined routes
            http_response_code(404);
            Helpers::redirectUrl('error-page');

        }

        // ===> Handle API Routes <===
    } elseif (strpos($uri, 'api/')) {
        if ($uri === $baseApiUri && $requestMethod === 'GET') {
            // Respond with a welcome message for the API base endpoint
            http_response_code(200);

            echo json_encode([
                "Method" => $requestMethod,
                "responseCode" => 200,
                "message" => "Welcome to Star Wars API!",
                "endpoints" => [
                    "films" => "{$baseApiUri}films",
                    "films-detail" => "{$baseApiUri}films/details/{id}",
                    "movie-name" => "{$baseApiUri}movie/{movieName}",
                    "characters-names" => "{$baseApiUri}characters-names",
                    "log-data" => "{$baseApiUri}log-data/query"
                ],
                "showErrorPage" => true
            ]);

        } elseif ($uri === "{$baseApiUri}films" && $requestMethod === 'GET') {
            // Fetch and return all films
            $movieController = new ApiMoviesController();
            $movieController->allFilms();

        } elseif (preg_match("#^{$baseApiUri}films/details/(\d+)$#", $uri, $matches) && $requestMethod === 'GET') {
            // Fetch and return film details by ID
            $movieController = new ApiMoviesController();
            $id = $matches[1];
            $movieController->filmsDetailsById($id);

        } elseif (preg_match("#^{$baseApiUri}movie/([^/]+)$#", $uri, $matches) && $requestMethod === 'GET') {
            // Fetch and return Movie Poster By Movie Name
            $movieController = new ApiMoviesController();
            $movieName = $matches[1];
            $movieController->getMoviePosterByName($movieName);

        } elseif ($uri === "{$baseApiUri}characters-names" && $requestMethod === 'POST') {
            // Fetch and return Character Name By ID
            $movieController = new ApiMoviesController();
            $movieController->allCharacterNamesByIds();

        } elseif (preg_match("#^{$baseApiUri}log-data/query#", $uri) && $requestMethod === 'GET') {

            $logDataController = new dbRegisterController();
            $response = $logDataController->getLogRegister();

            echo $response;

        } else {
            // Respond with a 404 error for undefined API routes
            http_response_code(404);
            echo json_encode([
                "error" => "Route not found",
                "responseCode" => 404,
                "showErrorPage" => true
            ]);
            $movieController = new ApiMoviesController();
            $movieController->errorLogRegister([
                'endpoint' => preg_replace('/^https?:\/\/[^\/]+/', '', $uri),
                'request_method' => $requestMethod,
                'response_code' => 404
            ]);
        }
    }
}