Aqui está o guia traduzido para o inglês em formato Markdown:

```markdown
# Project Installation Guide

This guide provides detailed instructions to set up and start the project.

---

## 1. Initial Setup

### 1.1. Modifying the `.htaccess` File

Ensure that the server is configured to use PHP version 7.4. Below is an example configuration that can be inserted into the `.htaccess` file located at the root of the project:

```apache
# Set PHP version 7.4 for this project
<FilesMatch "\.(php4|php5|php3|php2|php|phtml)$">
    SetHandler application/x-lsphp74
</FilesMatch>

# Security and redirects
Options -Indexes
RewriteEngine On

# Production use
RewriteBase /

## Development use
# RewriteBase /l5-test/

RewriteCond %{REQUEST_FILENAME} -f
RewriteRule ^ - [L]

RewriteCond %{REQUEST_FILENAME} -d
RewriteRule ^ - [L]

RewriteRule ^front-end/view/ - [L]
RewriteRule ^(.*)$ index.php [QSA,L]
```

> **Note 1:** Confirm with the server administrator if this configuration is compatible.<br>
> **Note 2:** In a development environment, the `FilesMatch` directive is generally not required as most IDEs perform this task.

---

### 1.2. Modifying the `util.js` File

Update the project's base URLs in the `util.js` file, located in `front-end/view/assets/js/utils`:

```javascript
window.globalApiUrl = 'http://localhost/l5-test/api/';
window.globalSiteUrl = 'http://localhost/l5-test/';
```

Replace the values according to the development or production environment.

---

## 2. Database

### 2.1. Creating the Database

1. Create a database with the desired name.
2. Import the SQL file available in the `documentation` folder to set up the tables and initial data.

### 2.2. Database Structure

#### Tables

##### 1. `api_logs`
Stores records of API calls made.

| Field           | Type        | Attributes                             | Description                               |
|-----------------|-------------|---------------------------------------|-------------------------------------------|
| `id`            | INT(20)     | NOT NULL, PRIMARY KEY, AUTO_INCREMENT | Unique identifier.                       |
| `register_date` | DATETIME    | NOT NULL                              | Date and time of the record.             |
| `request_method`| VARCHAR(10) | NOT NULL                              | HTTP request method (e.g., GET, POST).   |
| `endpoint`      | VARCHAR(255)| NOT NULL                              | Accessed endpoint.                       |
| `response_code` | INT(3)      | NOT NULL                              | HTTP response code.                      |
| `user_ip`       | VARCHAR(50) | NOT NULL                              | User's IP address.                       |

##### 2. `users`
Stores information about users who access the restricted API area.

| Field      | Type        | Attributes                             | Description                           |
|------------|-------------|---------------------------------------|---------------------------------------|
| `id`       | INT(5)      | NOT NULL, PRIMARY KEY, AUTO_INCREMENT | Unique identifier.                    |
| `name`     | VARCHAR(150)| NOT NULL                              | User's name.                          |
| `api_key`  | CHAR(20)    | NOT NULL, UNIQUE KEY                  | Unique authentication key.           |

#### Indexes

- **Table `api_logs`:**
    - Primary Key: `id`

- **Table `users`:**
    - Primary Key: `id`
    - Unique Key: `api_key`

#### Special Configurations

- All tables use:
    - Charset: `utf8mb4`
    - Collation: `utf8mb4_general_ci`
    - Engine: **InnoDB**
- **AUTO_INCREMENT**:
    - `users`: Starts at `2`.

---

## 3. Configuring the `Config.php` File

Edit the `Config.php` file, located in the `system` folder, with the database and URL settings:

### 3.1. Database Settings

```php
/**
 * Database connection settings.
 */
define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_NAME', 'l5transactions');
define('DB_USERNAME', 'root');
define('DB_PASSCODE', '');
```

### 3.2. System URLs

```php
/**
 * Base URLs for production and development environments.
 */
define('PRODUCTION_URL', 'https://yourproject.com');
define('DEVELOPMENT_URL', 'http://localhost/l5-test');
```

---

## 4. Integration with The Movie Database API

To display movie posters, you need to register for The Movie Database API. Registration is free and can be done at the link:

[https://api.themoviedb.org/](https://api.themoviedb.org/)

After registering, insert the generated key in the `secureConfig.php` file located in the `system` folder.

```php
/**
 * The Movie Database API key.
 */
define('FILM_IMAGE_API_KEY', 'Insert your API key here');
```

> **Note:** For sensitive information, such as passwords and access data, it is recommended to use `.env` files or similar techniques.<br>
> In this project, the use of external tools was limited by the requirements, so to avoid potential non-compliance, I chose to use a separate file, called `secureConfig.php`, just to avoid versioning the API key on GitHub.

---

## 5. Finalization

After following all the steps, the project will be set up and ready to use. Run tests to ensure everything is functioning correctly.
```