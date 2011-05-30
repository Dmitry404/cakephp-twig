###CakePhp plugin that provides an ability of usage Twig template engine

- CakePHP: The Rapid Development Framework for PHP - http://cakephp.org
- Twig, the flexible, fast, and secure template language for PHP http://www.twig-project.org/

#### How to install

* Clone this repository to your plugins directory

```bash
$ cd app/plugins 
$ git clone git://github.com/Dmitry404/cakephp-twig.git
```

* Init and update Twig library files

```bash
$ cd cakephp-twig
$ git submodule update --init
```

* Make folder for cached templates as writable

```bash
$ chmod 777 app/plugins/cakephp-twig/tmp
```

* Add to your AppController this property

```php
public $view = 'cakephp-twig.Twig';
```

* Add this code to your bootstrap.php

```php
App::import('lib', 'cakephp-twig.autoloader');
CakePhpTwig_Autoloader::register();
```

* Create your views with .htm (by default) extension