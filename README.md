# Laravel API for BookStore Application

This Laravel API serves as the backend for the Vue 3 bookstore application. It includes endpoints for both the Customer and Admin functionalities.

## Installation Prerequisites

Before setting up the Laravel API, make sure you have installed elastic search on your system. You can follow the instructions [here](https://www.digitalocean.com/community/tutorials/how-to-install-elasticsearch-logstash-and-kibana-elastic-stack-on-ubuntu-20-04) to install elastic search on your ubuntu system.

## Installation

Follow these steps to set up and run the Laravel API locally:

1. Clone the repository:
   ```bash
   git clone https://github.com/maulik4/bookstore-api.git
   cd bookstore-api
    ```
2. Install dependencies:
    ```bash
    composer install
    ```
3. Create a `.env` file by copying the `.env.example` file:
    ```bash
    cp .env.example .env
    ```

4. Generate an app encryption key:
    ```bash
    php artisan key:generate
    ```
5. Create an empty database for the application. In the `.env` file, add database information to allow Laravel to connect to the database.

6. Setup front end application url into your `.env` file:
    ```bash
    FRONTEND_URL=http://localhost:3000
    ```

6. To configure elastic search for the application, add the following to the `.env` file:
    ```bash
    ELASTICSEARCH_HOST=localhost
    ``` 
7. Migrate the database:
    ```bash
    php artisan migrate
    ```
8. Seed the database:
    ```bash
    php artisan db:seed
    ```

9. Sync the elastic search index:
    ```bash
    php artisan sync:books-to-elasticsearch
    ```
10. Create a symbolic link from `public/storage` to `storage/app/public`:
    ```bash
    php artisan storage:link
    ```

11. Start the local development server:
    ```bash
    php artisan serve
    ```
12. You can now access the server at http://localhost:8000

## API Endpoints

### Admin Endpoints

#### Register a new admin

```http
POST /api/register
```

| Parameter | Type     | Description                |
| :-------- | :------- | :------------------------- |
| `name`    | `string` | **Required**. Admin name|
| `email`   | `string` | **Required**. Admin email|
| `password`| `string` | **Required**. Admin password|
| `confirm_password`| `string` | **Required**. Admin Confirm password|

#### Login a admin

```http
POST /api/login
```

| Parameter | Type     | Description                |
| :-------- | :------- | :------------------------- |
| `email`   | `string` | **Required**. Admin email|
| `password`| `string` | **Required**. Admin password|

#### Get all books

```http
GET /api/books
```

| Parameter | Type     | Description                |
| :-------- | :------- | :------------------------- |
| `page`    | `int` | **Optional**. Page number|


#### Get a single book

```http
GET /api/books/${id}
```

#### Create a new book

```http
POST /api/books
```

| Parameter | Type     | Description                |
| :-------- | :------- | :------------------------- |
| `title`    | `string` | **Required**. Book title|
| `author`   | `string` | **Required**. Book author|
| `description`| `string` | **Required**. Book description|
| `genre`| `string` | **Required**. Book genre|
| `image`| `string` | **Required**. Book image|
| `isbn`| `int` | **Required**. Book isbn|
| `published`| `string` | **Required**. Book published date in YYYY-mm-dd|
| `publisher`| `string` | **Required**. Book publisher|

#### Update a book

```http
PUT /api/books/${id}
```

| Parameter | Type     | Description                |
| :-------- | :------- | :------------------------- |
| `title`    | `string` | **Required**. Book title|
| `author`   | `string` | **Required**. Book author|
| `description`| `string` | **Required**. Book description|
| `genre`| `string` | **Required**. Book genre|
| `image`| `string` | **Optional**. Book image|
| `isbn`| `int` | **Required**. Book isbn|
| `published`| `string` | **Required**. Book published date in YYYY-mm-dd|
| `publisher`| `string` | **Required**. Book publisher|

#### Delete a book

```http
DELETE /api/books/${id}
```

#### Get a single book

```http
GET /api/books/${id}
```



### Customer Endpoints

#### Get all books

```http
GET /api/book-store
```

| Parameter | Type     | Description                |
| :-------- | :------- | :------------------------- |
| `page`    | `int` | **Optional**. Page number|
| `filters`    | `string` | **Optional**. Search by title, author, genre, publisher, isbn|

#### Get a single book

```http
GET /api/book-store/${id}
```