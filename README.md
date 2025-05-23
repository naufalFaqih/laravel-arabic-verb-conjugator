# Tashrif Arab - Arabic Verb Conjugation App

<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo">
</p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

This Laravel 11 project is a web application designed to help users with Arabic verb conjugation (Tashrif). It allows users to search for Arabic verbs and displays their various conjugations, including past tense (madhi), present tense (mudhori), jussive (majzum), subjunctive (mansub), imperative (amar), and emphasized forms.

## Features

* **Arabic Verb Search**: Users can input an Arabic verb, and the application will fetch and display its conjugated forms.
* **External API Integration**: The application utilizes an external API (`http://qutrub.arabeyes.org/api`) to retrieve verb conjugation data.
* **User Authentication**: Users can register and log in to the application.
* **Search History**: For authenticated users, the application saves their search queries to a personal history, which can be viewed and re-searched. Users can also delete individual search history entries or clear all of them.
* **Responsive UI**: The front-end is built with Blade templates and Tailwind CSS, providing a responsive and user-friendly interface.
* **Robust Password Policy**: User registration enforces a strong password policy requiring a minimum of 8 characters with a mix of uppercase and lowercase letters, numbers, and symbols.
* **Session Management**: The application utilizes Laravel's session management to store user information like login time and name.
* **Error Handling and Logging**: Comprehensive error handling and logging are implemented for API requests and user registration to ensure a stable user experience.

## Installation

To set up this project locally, follow these steps:

1.  **Clone the repository:**

    ```bash
    git clone <repository_url>
    cd Latihan-Laravel11
    ```

2.  **Install Composer dependencies:**

    ```bash
    composer install
    ```

3.  **Install Node.js dependencies:**

    ```bash
    npm install
    ```

4.  **Copy the `.env.example` file and configure your environment:**

    ```bash
    cp .env.example .env
    php artisan key:generate
    ```
    Edit your `.env` file to configure your database connection. For example, for SQLite:
    ```
    DB_CONNECTION=sqlite
    DB_DATABASE=/path/to/your/database.sqlite
    ```
    Or for MySQL:
    ```
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=your_database_name
    DB_USERNAME=your_username
    DB_PASSWORD=your_password
    ```

5.  **Run database migrations:**

    ```bash
    php artisan migrate
    ```

6.  **Seed the database (optional, for test user):**

    ```bash
    php artisan db:seed
    ```
    A test user with email `test@example.com` and password `password` will be created.

7.  **Compile front-end assets:**

    ```bash
    npm run dev
    # Or for production:
    # npm run build
    ```

8.  **Start the Laravel development server:**

    ```bash
    php artisan serve
    ```

The application should now be accessible at `http://127.0.0.1:8000` (or your configured host and port).

## Usage

1.  **Navigate to the homepage**: You will see a search bar to input Arabic verbs.
2.  **Search for a verb**: Type an Arabic verb (e.g., `ضرب` or `بحث`) into the search bar and click "Tashrif".
3.  **View conjugations**: The results will display various conjugated forms of the verb, along with related information.
4.  **Register/Login**: To access the search history feature, register for an account or log in if you already have one.
5.  **View History**: After logging in, you can access your search history from the navigation bar to see past queries and re-search them.

## Project Structure (Relevant Files)

* `app/Http/Controllers/ApiController.php`: Handles requests to the external Arabic verb conjugation API.
* `app/Http/Controllers/Auth/LoginController.php`: Manages user login and logout processes.
* `app/Http/Controllers/Auth/RegisterController.php`: Manages user registration.
* `app/Http/Controllers/SearchHistoryController.php`: Handles saving, displaying, and deleting user search history.
* `app/Models/User.php`: Eloquent model for users, including a `last_login_at` field and relationship to search history.
* `app/Models/SearchHistory.php`: Eloquent model for storing search queries and their results.
* `database/migrations/*`: Database migration files for users, sessions, and search history.
* `resources/css/app.css`: Tailwind CSS definitions and custom spinner animation.
* `resources/js/app.js`: Main JavaScript file for the application.
* `resources/js/bootstrap.js`: Initializes Axios for HTTP requests.
* `resources/js/search-history.js`: Client-side logic for saving search queries to history (for authenticated users).
* `resources/js/search.js`: Handles front-end search functionality, including API calls and displaying results dynamically.
* `resources/views/*`: Blade templates for the application's views (e.g., `home.blade.php`, `login.blade.php`, `history.blade.php`).
* `routes/web.php`: Defines the web routes, including authentication routes, API routes, and history routes.
* `tailwind.config.js`: Tailwind CSS configuration.
* `vite.config.js`: Vite configuration for asset compilation.

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).