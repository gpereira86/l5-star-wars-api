<?php
/**
 * Constant definitions for API keys (example configuration).
 *
 * This file is an example of how to define API keys and other sensitive data in your project.
 * Always replace the example values with valid keys or secrets in your local or production environment.
 *
 * - `FILM_IMAGE_API_KEY`: API key to access the movie poster service from "https://api.themoviedb.org".
 *   This key is required for the application to retrieve movie posters.
 *
 * @note **Security Recommendation**:
 *       - Avoid hardcoding sensitive values directly in the code, especially in production environments.
 *       - Use environment variables, configuration files, or secure vaults to manage secrets securely.
 *       - This file is meant as an example and should not be included in your production version.
 * @note This should not be deleted. If you choose not to use it, leave the key blank.
 *
 * @guidance: **Don’t forget to rename this file to `secureConfig.php` or another appropriate name as per
 *            your project’s naming convention. If the name is different from `secureConfig.php`, it is
 *            necessary to update the name in the `index.php` file in the root directory.**
 */
define('FILM_IMAGE_API_KEY', ''); // Your API key for the movie poster service goes here.

