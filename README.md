CakePHP view class for the Twig template engine

- CakePHP: The Rapid Development Framework for PHP - http://cakephp.org
- Twig, the flexible, fast, and secure template language for PHP http://www.twig-project.org/

=========================== How to install =================================================

1. Clone this repository to your plugins directory (default app/plugins)
    $ cd app/plugins && git clone git://github.com/Dmitry404/cakephp-twig.git

2. Init and update Twig library files
    $ cd cakephp-twig && git submodule update --init

3. Make folder for cached templates as writable

4. Add to your AppController this property
    public $view = 'cakephp-twig.TwigView';

5. Create your views with .htm (by default) extension

============================================================================================