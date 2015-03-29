<?php

namespace MyPackage\Services;

use TestsAlwaysIncluded\Laravel\Support\ServiceProvider;

class MyDeferredPackageServiceProvider extends ServiceProvider
{
	/** @var boolean */
	protected $defer = true;

	public function register()
	{
		$this->package('MyPackage', 'mypackage', __DIR__.'/../Resources');
		// Bind services from configuration.
		$this->bindServices('mypackage::services');
	}

	public function provides()
	{
		// TODO Configuration lookup doesnt seem to work correctly.
		// For now, just manually return the aliases you need.
		return array(
			'mypackage.service.name',
			'mypackage.command.something',
		);
	}
}
