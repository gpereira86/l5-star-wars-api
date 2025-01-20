
# Manual de Uso da API

## Introdução
Esta API oferece endpoints para interagir com dados relacionados aos filmes Star Wars, incluindo detalhes dos filmes, personagens, capas do filme e mais.

A API fornece respostas no formato JSON.

## Endpoints Disponíveis

### Importante!
A URL deve ser precedida pelo host => Host + Endpoint.
Exemplo:
- host: https://minhaapi.com.br
- Endpoint: /api/films
- URL: https://minhaapi.com.br/api/films

### 1. **Base da API**
- **URL**: /api/
- **Método**: GET
  - **Descrição**: Retorna uma mensagem de boas-vindas ao ser acessado.
  - **Exemplo de Resposta**:
    ```json
    {
      "Method": "GET",
      "responseCode": 200,
      "message": "Welcome to Star Wars API!",
      "endpoints": {
        "films": "/api/films",
        "films-detail": "/api/films/details/{id}",
        "movie-name": "/api/movie/{movieName}",
        "characters-names": "/api/characters-names(POST-method-only)",
        "log-data": "/api/log-data/query?{API-KEY-REQUIRED}"
      },
     "showErrorPage": true
    }
    ```

### 2. **Listar Todos os Filmes**
- **URL**: /api/films
- **Método**: GET
  - **Descrição**: Retorna uma lista de todos os filmes disponíveis com suas respectivas capas.
    - **Exemplo de Resposta**:
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

### 3. **Detalhes do Filme por ID**
- **URL**: /api/films/details/{id}
- **Método**: GET
  - **Descrição**: Retorna detalhes de um filme pelo ID especificado.
    - **Exemplo de Resposta**:
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

### 4. **Obter Nomes dos Personagens por IDs**
- **URL**: /api/characters-names
- **Método**: POST
  - **Descrição**: Recebe uma lista de IDs de personagens e retorna os nomes correspondentes.
    - **Corpo da Requisição (Exemplo)**:
      ```json
      [1, 2, 3]
      ```
    - **Exemplo de Resposta**:
      ```json
      {
        "message": "Request for names successfully completed.",
        "charactersnames": ["Luke Skywalker", "C-3PO", "R2-D2"]
      }
      ```

### 5. **Registros de Logs**
Essa categoria recupera os registros de log gravados para cada requisição realizada para API, inclusive a si mesma.

Necessita de uma API KEY para acesso.

- **URL**: /api/log-data/query
  - **Método**: GET
    - **Descrição**: Recupera os registros de log no sistema.
      - **Formato da query (Exemplo)**:
      ```json
        Parâmetros: 
            {days} => 7, 15, 30 -> Filtro quantidade de dias. (opcional -> valor padrão=5)
            {finished} => data formato: yyyy-mm-dd -> Data final do período desejado. (opcional)
            {apikey} => api key válida (mandatório)                  
                
        Url com a query completa:
            http://{SEU HOST}/api/log-data/query?days=7&finished=2025-01-14&apikey={SUA API KEY}
      ```
       
      - **Exemplo de Resposta**:
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

### 6. **Erro 404**
- **URL Não Encontrada**
- **Método**: Qualquer método
  - **Descrição**: Quando o endpoint não é encontrado, a API retorna um erro 404.
    - **Exemplo de Resposta**:
      ```json
      {
        "error": "Route not found",
        "responseCode": 404,
        "showErrorPage": true
      }
      ```

## Considerações Finais
Este manual fornece informações sobre como utilizar os endpoints da API. As requisições podem ser feitas utilizando ferramentas como o Postman ou diretamente por meio de código. Lembre-se de que a API segue padrões REST e retorna respostas em formato JSON.