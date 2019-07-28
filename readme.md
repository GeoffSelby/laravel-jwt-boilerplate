## Laravel JWT Boilerplate

[![Build Status](https://travis-ci.com/GeoffSelby/laravel-jwt-boilerplate.svg?branch=master)](https://travis-ci.org/GeoffSelby/laravel-jwt-boilerplate)

This boilerplate is meant to be used to rapidly build an API with Laravel using JWT authentication.

Features inlude:

-   Full authentication suite with JWT-Auth - [tymondesigns/jwt-auth](https://github.com/tymondesigns/jwt-auth)
-   CORS handling with Laravel-CORS - [barryvdh/laravel-cors](http://github.com/barryvdh/laravel-cors)
-   Email verification handling done for you 🙌

## Installation

First, run `composer create-project geoffselby/laravel-jwt-boilerplate`

Then, have a 🍺 and wait for composer to do it's thing.

Once you have installed the boilerplate with Composer, set up your database in `.env` and run the `php artisan migrate` command to migrate the database. The JWT secret is generated automatically when you install the boilerplate with Composer.

## Usage

### API Development

Develop your API the same way you would normally develop a Laravel powered API with JWT authentication implemented out of the box.

### Frontend Implementation

This boilerplate is designed so that you can use whatever frontend implementation you choose _(i.e. Create React App or Vue CLI)_ as long as you implement authentication correctly.

Create React App example: **Coming Soon**

## Enabling CORS

By default, CORS is enabled for all routes since it is assumed you are using a seperate front end.

Check the [Laravel-CORS](https://github.com/barryvdh/laravel-cors) docs for more info.

## Contributing

If you would like to contribute to this project, submit a PR for review. A more detailed contribution guide is in the works.

## License

This project is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).
