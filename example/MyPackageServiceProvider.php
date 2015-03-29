<?php

namespace MyPackage\Services;

use TestsAlwaysIncluded\Laravel\Support\ServiceProvider;

class MyPackageServiceProvider extends ServiceProvider
{
	public function boot()
	{
		$this->package('MyPackage', 'mypackage', __DIR__.'/../Resources');

		$this->bindCommands('mypackage::commands');

		include __DIR__.'/../Resources/routes.php';
	}

	public function register()
	{
		$this->package('MyPackage', 'mypackage', __DIR__.'/../Resources');
		// Bind services from configuration.
		$this->bindServices('mypackage::services');
	}
}
