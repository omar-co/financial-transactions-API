# Technical test for Quilter

## Getting Started

Follow these steps to set up and run the Project:

### Prerequisites
Ensure you have Docker and Docker Compose installed. You can verify by running:

```bash
docker --version
docker compose version
```

If these commands do not return the versions, install Docker and Docker Compose using the official documentation: [Docker](https://docs.docker.com/get-docker/) and [Docker Compose](https://docs.docker.com/compose/install/).

### Clone the Repository

```bash
git clone https://github.com/omar-co/financial-transactions-API.git
cd financial-transactions-API
```

### Setting Up the Development Environment

1. Copy the .env.example file to .env and adjust any necessary environment variables:

```bash
cp .env.example .env
```

Hint: adjust the `UID` and `GID` variables in the `.env` file to match your user ID and group ID. You can find these by running `id -u` and `id -g` in the terminal.

2. Start the Docker Compose Services:

```bash
docker compose -f compose.yaml up -d
```

3. Install Laravel Dependencies:

```bash
docker compose -f compose.yaml exec php-fpm composer install
```

4. Run Migrations:

```bash
docker compose -f compose.yaml exec php-fpm php artisan migrate
```

5. Create the personal access token client:

```bash
docker compose -f compose.yaml exec php-fpm php artisan passport:client --personal
```

6. Copy the `Client ID` and `Client secret` to your `.env` file.
7. Access the Application:

[http://localhost](http://localhost).

### Accessing the Project Container

The container `php-fpm` includes Composer, Node.js, NPM, and other tools necessary for Laravel development.

```bash
docker compose -f compose.yaml exec php-fpm bash
```

### Run Artisan Commands:

```bash
docker compose -f compose.yaml exec php-fpm php artisan migrate
```

### Run PHPUnit tests:

```bash
 docker compose -f compose.yaml exec php-fpm php artisan test
```

### Rebuild Containers:

```bash
docker compose -f compose.yaml up -d --build
```

### Stop Containers:

```bash
docker compose -f compose.yaml down
```

### View Logs:

```bash
docker compose -f compose.yaml logs -f
```

For specific services, you can use:

```bash
docker compose -f compose.yaml logs -f mysql
```

## Technical Details

- **PHP**: Version **8.3 FPM**
- **MySQL**: Version **8**
- **Nginx**
- **Docker Compose**

## API documentation

[https://documenter.getpostman.com/view/6431220/2sB2cUCPCN](https://documenter.getpostman.com/view/6431220/2sB2cUCPCN).
