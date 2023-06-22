<?php

class api {
    static function routers(){

        $routes = [

            'GET' => [
               
                '/products'     => 'ProductController@index',
                '/product/{id}' => 'ProductController@show',
                '/products-active' => 'ProductController@getAllActive',

                '/product-types'      => 'ProductTypeController@index',
                '/product-type/{id}' => 'ProductTypeController@show',
                '/product-type-active' => 'ProductTypeController@getAllActive',

                '/sales'     => 'SaleController@index'

            ],

            'POST' => [

                '/product' => 'ProductController@store',

                '/product-type' => 'ProductTypeController@store',

                '/sale' => 'SaleController@store',

            ],

            'PUT' => [

                '/product/{id}' => 'ProductController@update',

                '/product-type/{id}' => 'ProductTypeController@update',

            ],

            'DELETE' => [

                '/product/{id}' => 'ProductController@destroy',

                '/product-type/{id}' => 'ProductTypeController@destroy',

                '/sale/{id}' => 'SaleController@destroy',

            ],
        ];

        return $routes;

    }
}

