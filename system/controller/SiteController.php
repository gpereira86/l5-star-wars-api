<?php

namespace system\controller;

/**
 * SiteController is responsible for handling the rendering of different pages for the site.
 *
 * This controller manages the logic for rendering pages like the homepage, movie detail page, and error page.
 * It uses the `renderHTML` method to load the corresponding HTML files from the front-end views.
 */
class SiteController
{
    /**
     * Renders the homepage of the site.
     *
     * This method loads the `index.html` file from the front-end views and outputs it to the browser.
     * It does not require any data to be passed and simply renders the static homepage.
     */
    public function index()
    {
        $this->renderHTML('./front-end/view/index.html');
    }

    /**
     * Renders the movie details page for a specific movie.
     *
     * This method loads the `movie-details.html` file from the front-end views, and it may include
     * specific movie data if provided. The movie details are expected to be passed as part of the
     * data array.
     *
     * @param string $movieName The name of the movie whose details are to be displayed.
     * @param array $data Optional data to be passed to the view, such as movie information.
     */
    public function movieDetailPage($movieName, $data = [])
    {
        // Pass the movie name and additional data to the view if necessary
        $data['movieName'] = $movieName;
        $this->renderHTML('./front-end/view/movie-details.html', $data);
    }

    /**
     * Renders the error page.
     *
     * This method loads the `error.html` file from the front-end views and displays an error message.
     * It can be used for handling various types of errors that occur on the site.
     */
    public function errorPage()
    {
        $this->renderHTML('./front-end/view/error.html');
    }

    /**
     * Renders an HTML file and outputs its content to the browser.
     *
     * This method is responsible for loading the specified HTML file, extracting any data passed
     * as an associative array, and rendering the content. If the HTML file does not exist, it throws
     * an exception with an error message.
     *
     * It utilizes output buffering to capture the content of the HTML file and pass any variables
     * extracted from the data array.
     *
     * @param string $file The path to the HTML file to be rendered.
     * @param array $data Optional associative array of data to be passed to the HTML file.
     * @throws \Exception If the specified HTML file is not found.
     */
    public function renderHTML($file, $data = [])
    {
        try {
            // Check if the HTML file exists
            if (!file_exists($file)) {
                throw new \Exception("HTML file not found: " . $file);
            }

            // Start output buffering to capture HTML content
            ob_start();

            // Extract data array as individual variables
            extract($data);

            // Include the HTML file and capture its content
            include($file);

            // Get the content from the output buffer and clean it
            $content = ob_get_clean();

            // Output the rendered content to the browser
            echo $content;
        } catch (\Exception $e) {
            // Handle exceptions by displaying an error message (consider logging this error)
            echo "Error: " . $e->getMessage();
        }
    }
}
