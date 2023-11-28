###  Docker with symfony63 a tasks system managed by OpenApi
##### About The Project 
* docker with php8.2, mysql, nginx
* crud of tasks
* nesting tasks unlimited as horizontal as vertical
* forbidden delete a task which has child task with status "todo"
* recursive data output as a tree of nesting tasks
* PSR 12 standart
* ![изображение](https://github.com/vadimlvov71/docker_symfony63_openapi/assets/57807117/fb26c16f-8529-4f64-8e3f-d72b3ca92cd4)


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
2. docker-compose build
3. docker-compose up -d
4. ![изображение](https://github.com/vadimlvov71/docker_symfony63_openapi/assets/57807117/0efe1a20-33fc-48d7-9eec-3fd89b17e601)


5. docker exec -it php_tasks bash
6. composer install
7. chown -R www-data:www-data /var/www
8. open page http://localhost:83/start/
   which run scripts for create: database, migration and fixture with 20 generated randomly tasks
9. http://localhost:83/api/doc       as a result:
10. ![изображение](https://github.com/vadimlvov71/docker_symfony63_openapi/assets/57807117/bcb16571-c3a4-4754-a2e7-fe15f2dddc72)
