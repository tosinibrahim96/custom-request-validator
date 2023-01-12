# Custom request validator

> Manually implementing validation rules for `alpha, required, email, number`

### Clone

-   Clone the repository using `git clone https://github.com/tosinibrahim96/custom-request-validator.git`

### Setup

-   Download WAMP or XAMPP to manage APACHE, MYSQL and PhpMyAdmin. This also installs PHP by default. You can follow [this ](https://youtu.be/h6DEDm7C37A)tutorial
-   Download and install [composer ](https://getcomposer.org/)globally on your system

> install all project dependencies and generate application key

```shell
$ composer install
$ php artisan key:generate
```

> start your Apache server and MySQL on WAMP or XAMPP interface
> serve your project using the default laravel PORT or manually specify a PORT

```shell
$ php artisan serve (Default PORT)
$ php artisan serve --port={PORT_NUMBER} (setting a PORT manually)
```

### Available Endpoints

<details><summary class="section-title">POST <code>/api/v1/validate_data</code> -> Validate request payload (Validation error)</summary></details>

<div class="collapsable-details">
<div class="json-object-array">
<pre>
  {
    "first_name": {
        "value": "John",
        "rules": "alpha|required"
    },
    "last_name": {
        "value": "Doe",
        "rules": "alpha|required"
    },
    "email": {
        "value": "Doe",
        "rules": "email"
    },
    "phone": {
        "value": "08175020329",
        "rules": "number"
    }
  }
</pre>
<pre>
  {
    "status": false,
    "message": "The given data is invalid",
    "errors": {
        "email": [
            "email must be a valid e-mail address"
        ]
    }
  }
</pre>

</div>
</div>
</details>




<details><summary class="section-title">POST <code>/api/v1/validate_data</code> -> Validate request payload (Success)</summary></details>

<div class="collapsable-details">
<div class="json-object-array">
<pre>
  {
    "first_name": {
        "value": "John",
        "rules": "alpha|required"
    },
    "last_name": {
        "value": "Doe",
        "rules": "alpha|required"
    },
    "email": {
        "value": "tosinibrahim92@gmail.com",
        "rules": "email"
    },
    "phone": {
        "value": "08175020329",
        "rules": "number"
    }
  }
</pre>
<pre>
  {
    "status": true
}
</pre>

</div>
</div>
</details>

### License

-   **[MIT license](http://opensource.org/licenses/mit-license.php)**
-   Copyright 2023 © <a href="https://github.com/tosinibrahim96" target="_blank">Ibrahim Alausa</a>.
