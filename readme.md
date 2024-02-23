# Management Products API

The Management Products API is a Laravel-based web application that allows users to manage products in a database.

## Getting Started

To get started with the Management Products API, follow these steps:

1. Clone the repository: `git clone https://github.com/codingcab/ShipTown.git`
2. Navigate to the project directory: `cd ShipTown`
3. Install dependencies: `composer install`
4. Copy the `.env.example` file to `.env`: `cp .env.example .env`
5. Generate a new application key: `php artisan key:generate`
6. Create a new MySQL database for the application and update the `.env` file with your database credentials
7. Run database migrations: `php artisan migrate`
8. Start the application: `php artisan serve`

## Authentication

The Management Products API uses token-based authentication to secure API endpoints. To authenticate, send a `POST` request to the `/api/auth/login` endpoint with your email and password in the request body. The API will return a JSON response containing your authentication token. To access authenticated endpoints, include your token in the `Authorization` header of your requests.

## Error Handling

The Management Products API uses HTTP status codes to indicate the success or failure of requests. Successful requests will return a `200 OK` status code, while unsuccessful requests will return an appropriate error code (e.g. `400 Bad Request`, `401 Unauthorized`, `404 Not Found`, etc.). In addition, error messages will be included in the response body.

## Contributing

Contributions to the Management Products API are welcome! To contribute, please follow these steps:

1. Fork the repository.
2. Create a new branch for your changes: `git checkout -b my-new-branch`
3. Make your changes and commit them: `git commit -am 'Add some feature'`
4. Push to the branch: `git push origin my-new-branch`
5. Submit a pull request.

Please ensure that your code follows the existing coding style and includes tests for any new functionality.

## License

The Management Products API is open source software licensed under the MIT license. Please see the `LICENSE` file for more details.

## Contact Information
    
If you have any questions or issues with the Management Products API, please contact the project owner at `contact@ship.town`.
