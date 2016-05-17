# Lumen PHP Framework
## Secure authentication and authorization

This is an implementation of Lumen PHP Framework with just the user management system, the main features are:
- Random token 60 char authentication
- Authorization levels
- Read and write permissions
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

## Help

###Read and write permissions

It works like this:

First, use the standard methods to get your object, for example:
```
$user = User::where("id", "=", $id)->first();
```

Then use the object as a parameter in the read function with the request
```
$user = Authorization::read($request, $user);
```

The read method is going to read from the authorizations table the read column, it gets the row corresponding to the auth level of the logged user ("auth") AND the controller_actions (that is the action called inside the request).
When it finds the read permissions (that are the names of the columns of the object), it removes from the object all the property that the logged user can't read.

The same happens with the write method.

## License

The Lumen framework is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)
