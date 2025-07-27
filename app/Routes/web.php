<?php

use App\Core\Router;

Router::get('', 'HomeController@index');

Router::get('post', 'PostController@index')->middleware('Auth');
