<?php

// Services available to all ENV's.

return array(
	// We can provide classes.
	'MyPackage\Services\MyService' => array(),

	// Or we can provide instances.
	'mypackage.service.name' => array(
		// Provide your class path.
		'class' => 'MyPackage\Services\MyService',
		// Symfony2 prototype option is supported.
		// This means we want a new instance every time.
		// If the option is not provided, it will default to 'true'.
		'prototype' => true,
	),
	'mypackage.service.two' => array(
		// Service definitions can extend one another.
		'class' => 'mypackage.service.name',
		// Laravel 'shared' services are supported.
		// This means we want the same instance every time.
		'shared' => true,
	),
	'mypackage.service.three' => array(
		// Show me some voodoo!
		'class' => 'Illuminate\Support\Collection',
		// The 'shared' flag will override the 'prototype' flag.
		'prototype' => true,
		'shared' => true,
		// We want to inject our services when this one is requested.
		'calls' => array(
			array('put', array('one', '@mypackage.service.name')),
			array('put', array('two', '@mypackage.service.two')),
		),
	),
	'mypackage.service.four' => array(
		'class' => 'Illuminate\Support\Collection',
		// We can optionally pass along constructor arguments.
		// If omitted, Laravel will automatically do the heavy lifting.
		'arguments' => array(
			array('arg1'),
		),
	),
	'mypackage.service.five' => array(
		'class' => 'Illuminate\Support\Collection',
		// We can also use configuration entries as arguments.
		'arguments' => array(
			'%mypackage::five',
		),
		// Or in the argument lists for 'calls'.
		'calls' => array(
			array('put', array('five', '%mypackage::five')),
		),
	),
	// Create an instance of our DoSomethingCommand.
	'mypackage.command.something' => array(
		'class' => 'MyPackage\Commands\DoSomethingCommand',
	),
);
