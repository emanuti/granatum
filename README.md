<p align="center">
  <a href="https://laravel.com" target="_blank"><img src="https://laravel.com/assets/img/components/logo-laravel.svg"></a>
<!--  <a href="https://www.granatum.com.br/financeiro/api/" target="_blank"><span style="background-color:#00579B;"><img src="https://www.granatum.com.br/financeiro/img/logo-granatum-financeiro.png"></span></a>-->
</p>
# Granatum
Laravel Package to access Granatum API

## Requirements

curl extension

## Install

Via composer

``` bash
$ composer require emanuti/granatum
```

Open config/app.php

Add
``` php
Emanuti\Granatum\GranatumServiceProvider::class,
```
inside providers key of array

Add
``` php
'Granatum' => Emanuti\Granatum\Facade::class
```
inside aliases key of array

Run 

``` bash
$ php artisan vendor:publish
```
will generate config/granatum.php

## Configuration

Open generated file granatum.php by vendor:publish comand and fill:
env will determine the enviroment that you are
token dev will be used to test and prod, you know :-)

## Usage

Available routes: https://www.granatum.com.br/financeiro/api/

To get a collection related with route

``` php
\Granatum::get('any_route_available');
```

To get an item of collection by id

``` php
\Granatum::get('any_route_available', id);
```

where id will be an integer

To add one item

``` php
\Granatum::post('any_route_available', fields);
```

where fields will be an array

To edit one item

``` php
\Granatum::put('any_route_available', id, fiedls);
```

where fields will be an array and id will be an integer

To delete one item
``` php
\Granatum::delete('any_route_available', id);
```

## License

The MIT License (MIT).  
