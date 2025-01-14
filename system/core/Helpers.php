<?php

namespace system\core;

/**
 * Helper class providing various utility methods for the application.
 *
 * This class includes methods for working with URLs, redirecting, summarizing text, and creating slugs. These methods are commonly used across the application to simplify common tasks.
 */
class Helpers
{
    /**
     * Returns true if the environment is localhost, false otherwise.
     *
     * This method checks the `SERVER_NAME` to determine if the application is running on localhost.
     *
     * @return bool True if the environment is localhost, false otherwise.
     */
    public static function localhost(): bool
    {
        $server = filter_input(INPUT_SERVER, 'SERVER_NAME');

        if ($server == 'localhost') {
            return true;
        }
        return false;
    }

    /**
     * Constructs a full URL based on the current environment (development or production).
     *
     * This method uses the `SERVER_NAME` to determine whether the environment is local or production, and then appends the provided URL to the base URL for the appropriate environment.
     *
     * @param string|null $url The URL to append to the environment base URL.
     * @return string The full URL.
     */
    public static function url(string $url = null): string
    {
        $server = filter_input(INPUT_SERVER, 'SERVER_NAME');
        $environmentInUse = ($server == 'localhost' ? DEVELOPMENT_URL : PRODUCTION_URL);

        if (strpos($url, '/') === 0) {
            return $environmentInUse . $url;
        }

        return $environmentInUse . '/' . $url;
    }

    /**
     * Simplified URL redirection using just the slug.
     *
     * This method redirects the user to a new URL based on the provided slug. If no slug is provided, it redirects to the home page.
     *
     * @param string|null $url The slug or URL to redirect to.
     * @return void
     */
    public static function redirectUrl(string $url = null): void
    {
        header('HTTP/1.1 302 found');

        $local = ($url ? self::url($url) : self::url());

        header("location: {$local}");
        exit();
    }

    /**
     * Summarizes the provided text to a given character limit.
     *
     * This method trims and strips any HTML tags from the provided text, and then shortens the text to the specified character limit. If the text exceeds the limit, it appends a continuation string (e.g., '...').
     *
     * @param string $text The text to summarize.
     * @param int $limit The character limit for the summarized text.
     * @param string $continue The string to append if the text is shortened (default is '...').
     * @return string The summarized text.
     */
    public static function summarizeText(string $text, int $limit, string $continue = '...'): string
    {
        $cleanText = trim(strip_tags($text));

        if (mb_strlen($cleanText) <= $limit) {
            return $cleanText;
        }

        $summarizeText = mb_substr($cleanText, 0, mb_strrpos(mb_substr($cleanText, 0, $limit), ' '));

        return $summarizeText . $continue;
    }

    /**
     * Converts a string into a URL-friendly slug.
     *
     * This method converts special characters, accents, and spaces into a URL-friendly format, where words are separated by hyphens.
     *
     * @param string $string The string to convert into a slug.
     * @return string The generated slug.
     */
    public static function slug(string $string): string
    {
        $map['a'] = "ÁáãÃÉéÍíÓóÚúÀàÈèÌìÒòÙùÂâÊêÎîÔôÛûÄäËëÏïÖöÜüÇçÑñÝý!@#$%&!*_-+=:;,.?/|'~^°¨ªº´";
        $map['b'] = 'AaaAEeIiOoUuAaEeIiOoUuAaEeIiOoUuAaEeIiOoUuCcNnYy___________________________';

        $slug = strtr(utf8_decode($string), utf8_decode($map['a']), $map['b']);
        $slug = strip_tags(trim($slug));
        $slug = str_replace(' ', '-', $slug);
        $slug = str_replace(['-----', '----', '--', '-'], '-', $slug);

        return strtolower(utf8_decode($slug));
    }

    public static function sendResponse(array $data, int $statusCode = 200)
    {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($data);
//        exit;
    }




}
