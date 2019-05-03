# Granatum
Laravel Package to access Granatum API

## Install

Via composer

``` bash
$ composer require emanuti/granatum
```

Open config/app.php

Add
Emanuti\Granatum\GranatumServiceProvider::class,
inside providers key of array

Add
'Granatum' => Emanuti\Granatum\Facade::class
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
\Granatum::put('any_route_available', fiedls, id);
```

where fields will be an array and id will be an integer

``` php
\Granatum::put('any_route_available', id);
```

## License

The MIT License (MIT).  