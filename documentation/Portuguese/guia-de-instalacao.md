# Guia de Instalação do Projeto

Este guia fornece instruções detalhadas para configurar e iniciar o projeto.

---

## 1. Configuração Inicial

### 1.1. Modificação do arquivo `.htaccess`

Certifique-se de que o servidor está configurado para utilizar a versão 7.4 do PHP. Abaixo está o exemplo de configuração que pode ser inserido no arquivo `.htaccess` na raiz do projeto:

```apache
# Definir a versão 7.4 do PHP para este projeto
<FilesMatch "\.(php4|php5|php3|php2|php|phtml)$">
    SetHandler application/x-lsphp74
</FilesMatch>

# Segurança e redirecionamentos
Options -Indexes
RewriteEngine On

# Uso em produção
RewriteBase /
 
## Uso em desenvolvimento
# RewriteBase /l5-test/

RewriteCond %{REQUEST_FILENAME} -f
RewriteRule ^ - [L]

RewriteCond %{REQUEST_FILENAME} -d
RewriteRule ^ - [L]

RewriteRule ^front-end/view/ - [L]
RewriteRule ^(.*)$ index.php [QSA,L]
```
> **Nota 1:** Confirme com o administrador do servidor se esta configuração é compatível.<br><br>
> **Nota 2:** Em ambiente de desenvolvimento não é necessário uso do FilesMatch, em geral as IDEs desempenham esse papel.

---

### 1.2. Alteração do arquivo `util.js`

Atualize as URLs base do projeto no arquivo `util.js`, localizado em `front-end/view/assets/js/utils`:

```javascript
window.globalApiUrl = 'http://localhost/l5-test/api/';
window.globalSiteUrl = 'http://localhost/l5-test/';
```

Substitua os valores conforme o ambiente de desenvolvimento ou produção.

---

## 2. Banco de Dados

### 2.1. Criação do Banco de Dados

1. Crie um banco de dados com o nome desejado.
2. Importe o arquivo SQL disponível na pasta `documentation` para configurar as tabelas e dados iniciais.

### 2.2. Estrutura do Banco de Dados

#### Tabelas

##### 1. `api_logs`
Armazena registros das chamadas realizadas na API.

| Campo            | Tipo        | Atributos                             | Descrição                               |
|-------------------|-------------|---------------------------------------|-----------------------------------------|
| `id`             | INT(20)     | NOT NULL, PRIMARY KEY, AUTO_INCREMENT | Identificador único.                   |
| `register_date`  | DATETIME    | NOT NULL                              | Data e hora do registro.               |
| `request_method` | VARCHAR(10) | NOT NULL                              | Método da requisição (ex.: GET, POST). |
| `endpoint`       | VARCHAR(255)| NOT NULL                              | Endpoint acessado.                     |
| `response_code`  | INT(3)      | NOT NULL                              | Código de resposta HTTP.               |
| `user_ip`        | VARCHAR(50) | NOT NULL                              | IP do usuário.                         |

##### 2. `users`
Armazena informações dos usuários que acessam a parte restrita da API.

| Campo      | Tipo        | Atributos                             | Descrição                           |
|------------|-------------|---------------------------------------|-------------------------------------|
| `id`       | INT(5)      | NOT NULL, PRIMARY KEY, AUTO_INCREMENT | Identificador único.                |
| `name`     | VARCHAR(150)| NOT NULL                              | Nome do usuário.                    |
| `api_key`  | CHAR(20)    | NOT NULL, UNIQUE KEY                  | Chave exclusiva de autenticação.    |

#### Índices

- **Tabela `api_logs`:**
    - Primary Key: `id`

- **Tabela `users`:**
    - Primary Key: `id`
    - Unique Key: `api_key`

#### Configurações Especiais

- Todas as tabelas utilizam:
    - Charset: `utf8mb4`
    - Collation: `utf8mb4_general_ci`
    - Engine: **InnoDB**
- **AUTO_INCREMENT**:
    - `users`: Começa em `2`.

---

## 3. Configuração do Arquivo `Config.php`

Edite o arquivo `Config.php`, localizado na pasta `system`, com as configurações do banco de dados e URLs:

### 3.1. Dados do Banco de Dados

```php
/**
 * Configurações de conexão ao banco de dados.
 */
define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_NAME', 'l5transactions');
define('DB_USERNAME', 'root');
define('DB_PASSCODE', '');
```

### 3.2. URLs do Sistema

```php
/**
 * URLs base para ambientes de produção e desenvolvimento.
 */
define('PRODUCTION_URL', 'https://seuprojeto.com');
define('DEVELOPMENT_URL', 'http://localhost/l5-test');
```

---

## 4. Integração com a API The Movie Database

Para exibir capas de filmes, é necessário se cadastrar na API The Movie Database. O cadastro é gratuito e pode ser feito no link:

[https://api.themoviedb.org/](https://api.themoviedb.org/)

Após o cadastro, insira a chave gerada no arquivo `secureConfig.php`, localizado na pasta `system`.

```php
/**
 * API key The Movie Database.
 */
define('FILM_IMAGE_API_KEY', 'Insira sua api key aqui');
```
> **Nota 1:** para informações sensíveis, como senhas e dados de acesso,  é recomendado o uso de arquivos `.env` ou tecnicas similares.<br>
> Neste projeto o uso de ferramentas externas foi limitado pelos requisitos, então para evitar um possível não cumnprimento, optei por usar um arquivo a parte, denominado `secureConfig.php`, apenas para não versionar a chave de api para o github<br><br>
> **Nota 2:** é necessário manter essa variável global para funcionamento do app, caso opte por não criar uma chave, mantenha a variável vazia.
---

## 5. Finalização

Após seguir todas as etapas, o projeto estará configurado e pronto para uso. Realize testes para garantir que tudo está funcionando corretamente.
