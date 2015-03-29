<?php

Route::get('example', array(
  'as' => 'exampleRoute',
  'using' => 'MyPackage\Controllers\ExampleController@getExampleAction',
));
