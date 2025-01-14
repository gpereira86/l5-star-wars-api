<?php

namespace system\controller;

/**
 * SiteController handles the rendering of pages for the site.
 *
 * It is responsible for rendering the homepage, movie detail page,
 * and error page by loading their corresponding HTML files.
 */
class SiteController
{
    /**
     * Renders the homepage of the site.
     *
     * This method loads the index.html file from the front-end views.
     */
    public function index()
    {
        $this->renderHTML('./front-end/view/index.html');
    }

    /**
     * Renders the movie details page for a specific movie.
     *
     * This method loads the movie-details.html file from the front-end views.
     *
     * @param string $movieName The name of the movie for which details are to be displayed.
     */
    public function movieDetailPage(string $movieName=null)
    {
//        $data = ['movieName' => $movieName];  // Pass movieName to view
//        $this->renderHTML('./front-end/view/movie-details.html', $data);
        $this->renderHTML('./front-end/view/movie-details.html');
    }

    /**
     * Renders the error page.
     *
     * This method loads the error.html file from the front-end views.
     */
    public function errorPage()
    {
        $this->renderHTML('./front-end/view/error.html');
    }

    /**
     * Renders an HTML file and outputs its content.
     *
     * This method loads the specified HTML file, optionally extracts data
     * passed as an associative array, and then outputs the rendered content.
     * If the HTML file doesn't exist, it will throw an exception.
     *
     * @param string $file The path to the HTML file to be rendered.
     * @param array $data Optional data to be passed to the HTML file.
     */
    public function renderHTML($file, $data = [])
    {
        try {
            if (!file_exists($file)) {
                throw new \Exception("HTML file not found!");
            }

            // Explicitly pass data to avoid overwriting variables
            ob_start();  // Start output buffering
            extract($data);  // Extracts data array into variables

            include($file);  // Include the HTML file
            $content = ob_get_clean();  // Get the content of the buffer and clean it

            echo $content;  // Output the rendered content
        } catch (\Exception $e) {
            // Handle error with a custom message or logging
            echo "Error: " . $e->getMessage();  // You could also log this message
        }
    }
}
