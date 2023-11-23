###  Docker with symfony63 a tasks system managed by OpenApi
##### About The Project 
* docker with php8.2, mysql, nginx
* crud of tasks
* nesting tasks unlimited as horizontal as vertical
* forbidden delete a task which has child task with status "todo"
* recursive data output as a tree of nesting tasks
* PSR 12 standart
  

##### Built With

*  symfony 6.3
*  OPENAPI: "nelmio/api-doc-bundle"


<!-- GETTING STARTED -->
##### Getting Started

##### Prerequisites
* ubuntu
* docker compose

##### Installation

1. Clone the repo
2. docker compose build
3. docker compose up -d
4. open page http://localhost:83/start/
   which run scripts for create: database, migration and fixture with 20 generated randomly tasks
5. http://localhost:83/api/doc       as a result:
6. ![изображение](https://github.com/vadimlvov71/docker_symfony63_openapi/assets/57807117/bcb16571-c3a4-4754-a2e7-fe15f2dddc72)
