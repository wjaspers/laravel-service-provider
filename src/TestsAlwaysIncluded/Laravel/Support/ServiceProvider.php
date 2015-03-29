<?php

namespace TestsAlwaysIncluded\Laravel\Support;

use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

abstract class ServiceProvider extends BaseServiceProvider
{
	/**
	 * Bind commands using our configuration.
	 *
	 * @param array|string $map
	 */
	public function bindCommands($map)
	{
		$map = $this->configOrArray($map);
		$this->commands($map);
	}

	/**
	 * Bind Services from our configuration.
	 *
	 * @param array|string $map
	 */
	public function bindServices($map)
	{
		// We want access to helper functions.
		$self = $this;

		// Get the configuration.
		$map = $this->configOrArray($map);

		// Walk each service entry.
		foreach ($map as $alias => $entry) {
			// Find the classPath for the service.
			$classPath = array_get($entry, 'class', null);

			// Ignore array indicies so aliases dont wind up being '1', '2', etc.
			$alias = is_numeric($alias) ? null : $alias;

			// Watch out for recursion.
			// If the alias and the classPath are one in the same, the app container will blow up.
			$alias = $alias === $classPath ? null : $alias;

			// Determine if the service is shared.
			$prototype = array_get($entry, 'prototype', true);

			// NOTE: Symfony uses 'prototype', which would be the opposite of shared.
			$shared = array_get($entry, 'shared', ($prototype === false));

			// Gather constructor arguments.
			$constructorArgs = array_get($config, 'arguments', null);

			// If constructor arguments are provided, we want to check them for aliases.
			if ($constructorArgs) {
				$this->app->bind($alias ?: $classPath, function ($app) use ($classPath, $constructorArgs, $self) {
					$constructorArgs = $self->replaceArguments($constructorArgs, $app);
					return $app->make($classPath, $constructorArgs);
				}, $shared);
			} elseif ($alias) {
				// If an alias exists, bind it to the app container.
				$this->app->bind($alias, $classPath, $shared);
			} else {
				// Otherwise, just bind the classPath and let Laravel do the rest of the work.
				$this->app->bind($classPath, null, $shared);
			}

			// Determine if there's any work to be done when the service is created.
			$calls = array_get($entry, 'calls', array());

			// Tell the app container how we want our service built.
			if (! empty($calls)) {
				$this->app->resolving($alias ?: $classPath, function ($service, $app) use ($calls, $self) {
					// Sort execution by index.
					ksort($calls);

					// Walk each call to be executed.
					foreach ($calls as $definition) {
						// The method should always be the first value.
						$method = array_shift($definition);

						// Arguments are second.
						$arguments = (array) array_shift($definition);
				
						// Replace any arguments with container aliases or config entries.
						$arguments = $self->replaceArguments($arguments, $app);

						// Call the method the user requested with the arguments supplied.
						call_user_func_array(array($service, $method)), $arguments);
					}
				});
			}
		}
	}

	/**
	 * Binds view composers and creators from configuration.
	 *
	 * @param array|string $map
	 */
	public function bindViews($map)
	{
		$composers = array_get($map, 'composers', array());

		foreach ($composers as $viewName => $composer) {
			if (! is_array($composer)) {
				$composer = array($composer);
			}

			$this->app->views->composer($viewName, $composer);
		}

		$creators = array_get($map, 'creators', array());

		foreach ($creators as $viewName => $creator) {
			if (! is_array($creator)) {
				$creator = array($creator);
			}

			$this->app->views->creator($viewName, $creator);
		}
	}

	/**
	 * Attempts to find a configuration entry matching the path provided
	 * and return its contents.
	 * @param array|string $configPath
	 * @return array
	 */
	public function configOrArray($configPath)
	{
		if (is_string($configPath)) {
			return $this->app->config->get($configPath, array());
		}

		return (array) $configPath;
	}

	/**
	 * Attempts to replace arguments with container aliases or configuration entries.
	 * and return the updated argument list.
	 *
	 * @param array|string $arguments
	 * @param Application $app
	 * @return array
	 */
	public function replaceArguments($arguments, Application $app)
	{
		// Walk the argument list.
		foreach ($arguments as $index => $value) {
			// Look for values which can be replaced.
			if (is_string($value)) {
				// Look for container aliases.
				if (strpos($value, '@') === 0) {
					$value = substr($value, 1);
					if ($app->bound($value) || $app->isAlias($value)) {
						// Replace the argument.
						$arguments[$index] = $app->make($value);
					}
				
				// Look for configuration entries.
				} elseif (strpos($value, '%') === 0) {
					$value = substr($value, 1);

					// Replace the argument.
					$arguments[$index] = $app->config->get($value, null);
				}
			}
		}

		return $arguments;	
	}
}
