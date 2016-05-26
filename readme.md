# Lumen PHP Framework
## Secure authentication and authorization

This is an implementation of Lumen PHP Framework with just the user management system, the main features are:
- Random token 60 char authentication
- Authorization and permissions
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

###Authorization and permissions
All the permissions are managed from the authorization table, one row corrispond to one rule.
These are the conditions of a rule:

- AUTH [INT]: is the level of authorization that you can assign to a User
- OBJECT [STRING]: is the object of this rule (name of the class)
- FIELD [STRING]: is the property of the object that is covered by this rule
- OWN [STRING]: is the property of the object that is used to define the owner (its id)
- STORE, UPDATE, DESTROY, SHOW [INT:0,1,2]: are the level of permission for each action. 0 is no permission, 1 is permission on own objects, 2 is permission on all object

The AuthorizationController has 4 methods:

####Show
You can use this method to get informations from the database in a safe way.

```
show($whom, $object)
```
$whom: "my" | "\*"

$object: every kind of declared object in your project

returns: the eloquent model of object (so remember to "->get()"!)

## License

The Lumen framework is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)
