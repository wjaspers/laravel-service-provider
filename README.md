# laravel-service-provider
A symfony like approach to Laravel's service providers.

## NOTICE
Upon later review, it has been determined that loading services via config is painstakingly slow.
Unless you've made changes to the bootstrapping process and cached the totality of your configurations in a single source, the added hits to disk can become detrimental to performance.

## Why oh why?
* I got fed up with creating the same service provider logic over...and over...and over again.
* Laravel's approach to dependency injection is unit testable, which makes me happy. 
* If I have a common approach to service provider logic, I need fewer unit tests, which makes our final product easier to maintain.
* Laravel's approach to dependency injection is a bit harder to grasp for those of us who started with Symfony2 or no DI at all.
* Configuration is much easier to update than code.

## How does it all work?
* Tell Laravel about your package, by updating `app/config/app.php`.
* Tell your Package's Service Provider what alias it should have, and where resources exist.
  * `$this->package('MyPackage/Path', 'mypackage', __DIR__.'/../Resources');`
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
    $this->bindCommands('mypackage::commands');
  }
  
  /** {@inheritdoc} */
  public function register()
  {
    $this->package('MyPackage/Path', 'mypackage', __DIR__.'/../Resources');
    $this->bindServices('mypackage::services');
  }
}
```

## Gotchas
* Watch out for services that depend on defered services.
* This won't handle things like `- imports`
* No concept of `tagging` services.

## Compatibility
* This is intended for Laravel 4.x projects.

## Needs work
* Laravel 4's `provides()` doesn't discover the config correctly. For now, just manually list the ones you need.
