<?php

use system\controller\DbRegisterController;
use system\controller\MoviesController;
use system\controller\SiteController;

function defineRoutes($uri, $requestMethod)
{
    $baseApiUri = URL_API_DEVELOPMENT;
    $baseSiteUri = URL_SITE_DEVELOPMENT;

    if($uri === $baseSiteUri && $requestMethod === 'GET'){

        http_response_code(200);
        $siteController = new SiteController();
        $siteController->index();

    } elseif (preg_match("#^{$baseSiteUri}movie/([^/]+)$#", $uri, $matches) && $requestMethod === 'GET')
    {

        $movieName = urldecode($matches[1]);

        http_response_code(200);
        $siteController = new SiteController();
        $siteController->movieDetailPage($movieName);

    } elseif ($uri === "{$baseSiteUri}error-page" && $requestMethod === 'GET')
    {
        http_response_code(404);
        $siteController = new SiteController();
        $siteController->errorPage();

    } elseif ($uri === $baseApiUri && $requestMethod === 'GET')
    {
        http_response_code(200);
        echo json_encode([
            "Method" => $requestMethod,
            "responseCode" => 200,
            "message" => "Welcome to the API",
            "showErrorPage" => true
        ]);

    } elseif ($uri === "{$baseApiUri}films" && $requestMethod === 'GET')
    {
        $movieController = new MoviesController();
        $movieController->allFilms();

    } elseif ($uri === "{$baseApiUri}films/details" && $requestMethod === 'GET')
    {
        $movieController = new MoviesController();
        $movieController->filmsDetails();

    } elseif (preg_match("#^{$baseApiUri}films/details/(\d+)$#", $uri, $matches)  && $requestMethod === 'GET')
    {
        $movieController = new MoviesController();

        $id = $matches[1];
        $movieController->filmsDetailsById($id);

    } elseif ($uri === "{$baseApiUri}log-register" && $requestMethod === 'POST')
    {
        $DbRegisterController = new dbRegisterController();

        $DbRegisterController->teste();

    } else
    {
        if(substr($uri, 0, strlen($baseApiUri)) === $baseApiUri){
            http_response_code(404);
            echo json_encode([
                "error" => "Route not found",
                "responseCode" => 404,
                "showErrorPage" => true
            ]);
        } else {
            http_response_code(404);
            $siteController = new SiteController();
            $siteController->errorPage();
        }
    }
}