# Lumen PHP Framework
## Secure authentication and authorization

This is an implementation of Lumen PHP Framework with just the user management system, the main features are:
- Random token 60 char authentication
- Authorization levels
- Google reCAPTCHA
- Account activation via email

## Installation and configuration

```
composer create-project marcocastignoli/lumen_auth YOUR_PROJECT 1.*
```

```
php artisan migrate
```

Just enter your Google reCAPTCHA private KEY in *config/captcha.php* and you're ready to go.

## License

The Lumen framework is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)
