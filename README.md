<p align="center">
  <br/>
  <h3 align="center">Point of Sale REST-API</h3>
  <p align="center">A laravel based Point of Sale Rest API</p>
  <br/>
  <br/>
</p>



## About The Project

This project intend to show REST API implementation using Laravel 10.x. We pick (simple) point of sale as it already widely knows, and we try to solve its problem by only code in as default as posible, no 3rd party library, no custom library, also we try implement laravel feature in documentation as much as posible.

## Built With

Only using laravel framework with its features.

## Features

Several laravel feature implementations:
* Database structure, we design simple database structure, it might be not strictly normal, but we believe its enough to production requirements.
* All database record has been covered by migration, simply run migration.
* Middlewares, only accept json request and content
* Validation by using form request, with validation input preparation (in sales and purchase route) and passes validation input manipulation.
* Rules, we add some rules to check product availability to sell and/or purchase
* We made controller as simple as possible, with only responsible to interact with database and return resource
* All controller must return (related, if needed) json resource
* Model relationship, we cover relationships as much and as standard as possible, including many-to-many with pivot (in sale and purchase item models)

## Roadmap

See the [open issues](https://github.com/bramaningds/Laravel-Point-of-Sale-REST-Api/issues) for a list of proposed features (and known issues).

## Contributing

Contributions are what make the open source community such an amazing place to be learn, inspire, and create. Any contributions you make are **greatly appreciated**.
* If you have suggestions for adding or removing projects, feel free to [open an issue](https://github.com/bramaningds/Laravel-Point-of-Sale-REST-Api/issues/new) to discuss it, or directly create a pull request after you edit the *README.md* file with necessary changes.
* Please make sure you check your spelling and grammar.
* Create individual PR for each suggestion.
* Please also read through the [Code Of Conduct](https://github.com/bramaningds/Laravel-Point-of-Sale-REST-Api/blob/main/CODE_OF_CONDUCT.md) before posting your first idea as well.

## License

Distributed under the MIT License. See [LICENSE](https://github.com/bramaningds/Laravel-Point-of-Sale-REST-Api/blob/main/LICENSE.md) for more information.

## Authors

* **Bramaning DS** - *Backend engineer enthusiast* - [Bramaning DS](https://github.com/bramaningds) - **
