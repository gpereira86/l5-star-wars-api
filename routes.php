<?php

use system\controller\DbRegisterController;
use system\controller\ApiMoviesController;
use system\controller\SiteController;
use system\core\Helpers;

/**
 * Handles routing for both site and API requests.
 * This function maps the incoming URI and request method to the appropriate controller actions.
 *
 * Based on the environment (development or production), it sets the base URIs for both API and site routes.
 *
 * @param string $uri The URI of the incoming request.
 * @param string $requestMethod The HTTP request method (GET, POST, etc.).
 *
 * @return void
 */
function defineRoutes($uri, $requestMethod)
{
    if (Helpers::localhost()) {
        $baseApiUri = URL_DEVELOPMENT.'api/';
        $baseSiteUri = URL_DEVELOPMENT;
    } else {
        $baseApiUri = URL_PRODUCTION.'api/';
        $baseSiteUri = URL_PRODUCTION;
    }

    $pos = strpos($uri, 'api/');

    // ===> Handle Site Routes <===
    if (!$pos) {
        $siteController = new SiteController();

        if ($uri === $baseSiteUri && $requestMethod === 'GET') {
            http_response_code(200);
            $siteController->index();

        } elseif (preg_match("#^{$baseSiteUri}movie/([^/]+)$#", $uri, $matches) && $requestMethod === 'GET') {
            http_response_code(200);
            $movieName = $matches[1];
            $siteController->movieDetailPage($movieName);

        } elseif ($uri === "{$baseSiteUri}error-page" && $requestMethod === 'GET') {
            http_response_code(404);
            $siteController->errorPage();

        } else {
            http_response_code(404);
            Helpers::redirectUrl('error-page');
        }

    // ===> Handle API Routes <===
    } elseif ($pos) {
        $movieController = new ApiMoviesController();
        $logDataController = new dbRegisterController();

        if ($uri === $baseApiUri && $requestMethod === 'GET') {
            http_response_code(200);

            echo json_encode([
                "Method" => $requestMethod,
                "responseCode" => 200,
                "message" => "Welcome to Star Wars API!",
                "endpoints" => [
                    "films" => "{$baseApiUri}films",
                    "films-detail" => "{$baseApiUri}films/details/{id}",
                    "movie-poster" => "{$baseApiUri}movie/{movieName}",
                    "characters-names" => "{$baseApiUri}characters-names (POST)",
                    "log-data" => "{$baseApiUri}log-data/query (API KEY REQUIRED)"
                ],
            ]);

            $movieController->logRegister([
                'endpoint' => '/api/',
                'request_method' => $requestMethod,
                'response_code' => 200
            ]);

        } elseif ($uri === "{$baseApiUri}films" && $requestMethod === 'GET') {
            $movieController->allFilms();

        } elseif (preg_match("#^{$baseApiUri}films/details/(\d+)$#", $uri, $matches) && $requestMethod === 'GET') {
            $id = $matches[1];
            $movieController->filmsDetailsById($id);

        } elseif (preg_match("#^{$baseApiUri}movie/([^/]+)$#", $uri, $matches) && $requestMethod === 'GET') {
            $movieName = $matches[1];
            $movieController->getMoviePosterByName($movieName);

        } elseif ($uri === "{$baseApiUri}characters-names" && $requestMethod === 'POST') {
            $movieController->allCharacterNamesByIds();

        } elseif (preg_match("#^{$baseApiUri}log-data/query#", $uri) && $requestMethod === 'GET') {
            $response = $logDataController->getLogRegister();
            echo $response;

        } else {
            http_response_code(404);
            echo json_encode([
                "error" => "Route not found",
                "responseCode" => 404,
            ]);
            $movieController->logRegister([
                'endpoint' => str_replace('/l5-test', '',preg_replace('/^https?:\/\/[^\/]+/', '', $uri)),
                'request_method' => $requestMethod,
                'response_code' => 404
            ]);
        }
    }
}
