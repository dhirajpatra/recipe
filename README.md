## Prerequsites

Used [Docker](https://www.docker.com/products/docker) to administer this test. This ensures that we get an identical result to you when we test your application out, and it also matches our internal development workflows. If you don't have it already, you'll need Docker installed on your machine. **The application MUST run in the Docker containers** - if it doesn't we cannot accept your submission. You **MAY** edit the containers or add additional ones if you like, but this  be clearly documented.

- Application using postgres 9.6 container. For details can check docker-compose.yml and Dockerfile.


### Technology

- Valid PHP 7.1, Go 1.8, or Python 3.6 code
- Persist data to either Postgres, Redis, or MongoDB (in the provided containers).
    - Postgres connection details:
        - host: `postgres`
        - port: `5432`
        - dbname: `recipe`
        - username: `recipe`
        - password: `recipe`
- Used the provided `docker-compose.yml` file in the root of this repository. You are free to add more containers to this if you like.

## Instructions

1. phpunit tests also automated in install script.
- Protected API must call after calling login with extra headers. See details in API section bellow.
- Instatll script will not only install and configure the necessary lib but also it will create database tables and seeding for testing after connect to the DB which is PGSql 9.6.

## How to run

- To start the application server, run the following from root of application:
####./instatll.sh

## Requirements

A simple Recipes API. The API  conform to REST practices and  provide the following functionality:

- List, create, read, update and delete Recipes
- Search recipes
- Rate recipes

### Endpoints

This application conform to the following endpoint structure and return the HTTP status codes appropriate to each operation. Endpoints specified as protected below require authentication to view. The method of authentication is up to you.

Login api call need for all protected api calls. It will create a dynamic time valid session. Every protected api call need to send same signature [token+secret] as successful login in header.

##### Recipes

| Name   | Method      | URL                    | Protected |
| ---    | ---         | ---                    | ---       |

| Login   | `POST`       | `http://localhost/api/user/login`             | ✘         |
curl -X POST -H 'X-Auth-Secret: 12345678' -H 'X-Auth-Token: 12345678' -i http://localhost/api/login --data '{"username":"recipe","password":"recipe"}'

| List   | `GET`       | `http://localhost/api/recipes`             | ✘         |
curl -X GET -i http://localhost/api/recipes

| Create | `POST`      | `http://localhost/api/recipes`             | ✓         |
curl -X POST -H 'X-Auth-Secret: 12345678' -H 'X-Auth-Token: 12345678' -i http://localhost/api/recipes --data '{"recipe_name":"Hilsha","preparation_time":3,"difficulty_level":"3","veg":false,"status":true}'

| Get    | `GET`       | `http://localhost/api/recipes/2`        | ✘         |
curl -X POST -i http://localhost/api/recipes/2

| Update | `PATCH` | `http://localhost/api/recipes/2`        | ✓         |
curl -X PATCH -H 'X-Auth-Secret: 12345678' -H 'X-Auth-Token: 12345678' -i http://localhost/api/recipes/2 --data '{"recipe_name":"Porota","preparation_time":2,"difficulty_level":"1","veg":true,"status":true}'

| Delete | `DELETE`    | `http://localhost/api/recipes/2`        | ✓         |
curl -X DELETE -H 'X-Auth-Secret: 12345678' -H 'X-Auth-Token: 12345678' -i http://localhost/api/recipes/2

| Rate   | `POST`      | `http://localhost/api/recipes/2/rating` | ✘         |
curl -X POST -i http://localhost/api/recipes/2/rating

| Search   | `POST`      | `http://localhost/api/recipes/search/kochu` | ✘         |
curl -X POST -i http://localhost/api/recipes/search/piz

An endpoint for recipe search functionality  also be implemented. The HTTP method and endpoint for this  be clearly documented. Pagination with primary structure implemented.

### Schema

- **Recipe**
    - Unique ID
    - Name
    - Prep time
    - Difficulty (1-3)
    - Vegetarian (boolean)

Additionally, recipes can be rated many times from 1-5 and a rating is never overwritten.

- Schema and seed sql are inside postgres/script/ folder and automatically call when docker compose statrt with build.

- For more details ER diagram kept in ER_diagram.png

