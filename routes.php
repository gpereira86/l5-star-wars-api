<?php

use system\controller\DbRegisterController;
use system\controller\MoviesController;
use system\controller\SiteController;
use system\core\Helpers;

function defineRoutes($uri, $requestMethod)
{
    $baseApiUri = URL_API_DEVELOPMENT;
    $baseSiteUri = URL_SITE_DEVELOPMENT;


    if(strpos($uri, 'api/') === false){ // ===> Site Routes <===

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

        }else {
            http_response_code(404);
            Helpers::redirectUrl('error-page');
        }

    } elseif (strpos($uri, 'api/')) { // ===> API Routes <===

        if ($uri === $baseApiUri && $requestMethod === 'GET')
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

        } elseif (preg_match("#^{$baseApiUri}films/details/(\d+)$#", $uri, $matches)  && $requestMethod === 'GET')
        {
            $movieController = new MoviesController();

            $id = $matches[1];
            $movieController->filmsDetailsById($id);

        } elseif ($uri === "{$baseApiUri}log-register" && $requestMethod === 'POST')
        {
            $DbRegisterController = new dbRegisterController();

            $DbRegisterController->saveLogRegister();

        } else {

            http_response_code(404);
            echo json_encode([
                "error" => "Route not found",
                "responseCode" => 404,
                "showErrorPage" => true
            ]);

        }

    }

}