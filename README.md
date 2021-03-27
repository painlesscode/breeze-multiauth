# Breeze Multiauth

[![Laravel](https://github.com/painlesscode/breeze-multiauth/workflows/Laravel/badge.svg)](https://github.com/painlesscode/breeze-multiauth/actions?query=workflow%3ALaravel)

laravel breeze with multi authentication system.

If you want to create standalone authentication system like default laravel user authentication system but using different guard, this package will help you.

## Installation
You can install the package via composer:
``` bash
composer require painlesscode/breeze-multiauth --dev
```
 
## Usage

You just need to run one artisan command.
```sh
php artisan breeze:multiauth Administrator
```
Here, `Administrator` is the role name of newly created authentication system.


You can log in with `http://example.com/administrator/login`

This Package does not provides assets (css/js) by default.

If you want assets too, you can run
```sh
php artisan breeze:multiauth Administrator --asset
```

If you want to replace existing files, you can run
```sh
php artisan breeze:multiauth Administrator --force
```

Now, Migrate,

```sh
php artisan migrate
```

## Test
You can test newly created authentication system by
```sh
php artisan test
```
