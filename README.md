# laravel-service-provider
A symfony like approach to Laravel's service providers.

## Why oh why?
* I got fed up with creating the same service provider logic over...and over...and over again.
* Laravel's approach to dependency injection is unit testable, which makes me happy. 
* If I have a common approach to service provider logic, I need fewer unit tests, which makes our final product easier to maintain.
* Laravel's approach to dependency injection is a bit harder to grasp for those of us who started with Symfony2 or no DI at all.
* Configuration is much easier to update than code.

## How does it all work?
* Tell Laravel about your package, by updating `app/config/app.php`.
* Tell your Package's Service Provider what alias it should have, and where resources exist.
  * `$this->package('MyPackage\Path', 'mypackage', __DIR__.'/../Resources');`
* Our service provider discovers the configuration provided (or you can offer an array), and creates the bindings necessary on the app container.

## Usage
```
<?php
namespace MyPackage\Path;

class MyPackageProvider extends \TestsAlwaysIncluded\Laravel\ServiceProvider
{
  /** {@inheritdoc} */
  public function boot()
  {
    $this->package('MyPackage\Path', 'mypackage', __DIR__.'/../Resources');
    $this->bindCommands('mypackage::commands');
  }
  
  /** {@inheritdoc} */
  public function register()
  {
    $this->package('MyPackage\Path', 'mypackage', __DIR__.'/../Resources');
    $this->bindServices('mypackage::services');
  }
}
```

## Gotchas
* Watch out for services that depend on defered services.
* This won't handle things like `- imports`
* How to handle global overrides are up to you
  * (i.e. `app/config/local/mypackage.php` wont automatically override `MyPackage\Path\Resources\config\mypackage.php`)
* Why are you calling `$this->package()` twice?
  * Laravel's `boot()` and `register()` functions seem to be exclusive of one another.
  * Package alias lookups will fail otherwise.
    * (i.e. `mypackage::config.entry' might exist in `MyPackage\Resources\config\config.php` but Laravel wont know its there.)

## Compatibility
* This is intended for Laravel 4.x projects.

## Needs work
* Laravel 4's `provides()` doesn't discover the config correctly. For now, just manually list the ones you need.
