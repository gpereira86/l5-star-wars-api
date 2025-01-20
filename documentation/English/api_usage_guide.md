# API Usage Manual

## Introduction
This API provides endpoints to interact with data related to Star Wars movies, including movie details, characters, movie posters, and more.

The API responds in JSON format.

## Available Endpoints

### Important!
The URL should be prefixed by the host => Host + Endpoint.
Example:
- host: https://myapi.com.br
- Endpoint: /api/films
- URL: https://myapi.com.br/api/films

### 1. **API Base**
- **URL**: /api/
- **Method**: GET
  - **Description**: Returns a welcome message when accessed.
    - **Response Example**:

    ```json
    {
      "Method": "GET",
      "responseCode": 200,
      "message": "Welcome to Star Wars API!",
      "endpoints": {
        "films": "/api/films",
        "films-detail": "/api/films/details/{id}",
        "movie-name": "/api/movie/{movieName}",
        "characters-names": "/l5-test/api/characters-names(POST-method-only)",
        "log-data": "/l5-test/api/log-data/query?{API-KEY-REQUIRED}"
      },
     "showErrorPage": true
    }
    ```

### 2. **List All Movies**
- **URL**: /api/films
- **Method**: GET
  - **Description**: Returns a list of all available movies with their respective posters.
    - **Response Example**:
    ```json
    {
      "method": "GET",
      "endpoint": "/api/films",
      "responseCode": 200,
      "data": [
        {
          "name": "A New Hope",
          "release_date": "1977-05-25",
          "id": ["1"],
          "moviePoster": "https://image.tmdb.org/t/p/w500/6FfCtAuVAW8XJjZ7eWeLibRLWTw.jpg"
        },
        {
          "name": "The Empire Strikes Back",
          "release_date": "1980-05-17",
          "id": ["2"],
          "moviePoster": "https://image.tmdb.org/t/p/w500/nNAeTmF4CtdSgMDplXTDPOpYzsX.jpg"
        }
      ]
    }
    ```

### 3. **Movie Details by ID**
- **URL**: /api/films/details/{id}
- **Method**: GET
  - **Description**: Returns details of a movie by the specified ID.
    - **Response Example**:

    ```json
    {
        "method": "GET",
        "endpoint": "/api/films/details/1",
        "responseCode": 200,
        "data": [
          {
            "name": "A New Hope",
            "episode": 4,
            "synopsis": "It is a period of civil war...",
            "release_date": "1977-05-25",
            "director": "George Lucas",
            "producers": "Gary Kurtz, Rick McCallum",
            "characters": ["1", "2", "3"],
            "film_age": "47 years, 7 months, 18 days",
            "moviePoster": "https://image.tmdb.org/t/p/w500/6FfCtAuVAW8XJjZ7eWeLibRLWTw.jpg",
            "movieTrailer": "https://www.youtube.com/watch?v=vZ734NWnAHA"
          }
        ]
      }
    ```

### 4. **Get Character Names by IDs**
- **URL**: /api/characters-names
- **Method**: POST
  - **Description**: Receives a list of character IDs and returns the corresponding names.
    - **Request Body Example**:

    ```json
    [1, 2, 3]
    ```

    - **Response Example**:

    ```json
    {
      "message": "Request for names successfully completed.",
      "charactersnames": ["Luke Skywalker", "C-3PO", "R2-D2"]
    }
    ```

### 5. **Log Requests**
This category retrieves the log records saved for each request made to the API, including to itself.
Requires an API KEY for access.

- **URL**: /api/log-data/query
  - **Method**: GET
    - **Description**: Retrieves the log records in the system.
      - **Query Format (Example)**:
      ```json
        Parameters: 
            {days} => 7, 15, 30 -> Filter by the number of days. (optional -> default value=5)
            {finished} => date format: yyyy-mm-dd -> The end date of the desired period. (optional)
            {apikey} => valid API key (mandatory)                  

        URL with the complete query:
            http://{YOUR HOST}/api/log-data/query?days=7&finished=2025-01-14&apikey={YOUR API KEY}
      ```

      - **Response Example**:
      ```json
        {
        "Method": "GET",
        "responseCode": 200,
        "data": {
            "query-days-search": "7",
            "query-day-start": "2025-01-07 00:00:00",
            "query-day-end": "2025-01-14 16:46:46",
            "count": 78,
            "registers": [
              {
                "id": 1,
                "register_date": "2025-01-18 12:54:47",
                "request_method": "GET",
                "endpoint": "/api/films",
                "response_code": 200,
                "user_ip": "::1",
                "authorized_user_id": null
              },
              {
                "id": 2,
                "register_date": "2025-01-18 12:54:48",
                "request_method": "GET",
                "endpoint": "/api/movie/A%20New%20Hope",
                "response_code": 200,
                "user_ip": "::1",
                "authorized_user_id": null
              },
              {
                "id": 3,
                "register_date": "2025-01-18 12:54:48",
                "request_method": "GET",
                "endpoint": "/api/movie/The%20Empire%20Strikes%20Back",
                "response_code": 200,
                "user_ip": "::1",
                "authorized_user_id": 1,
                "authorized_user_name": "ADMIN"
              }
            ]
          }
        }
      ```



### 6. **Error 404**
- **URL Not Found**
- **Method**: Any method
  - **Description**: When the endpoint is not found, the API returns a 404 error.
    - **Response Example**:
    ```json
    {
      "error": "Route not found",
      "responseCode": 404,
      "showErrorPage": true
    }
    ```

## Final Considerations
This manual provides information on how to use the API endpoints. Requests can be made using tools like Postman or directly via code. Remember that the API follows REST standards and returns responses in JSON format.