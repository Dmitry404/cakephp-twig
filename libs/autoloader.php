<?php
class CakePhpTwig_Autoloader
{
    static public function register($pathToTwigLibs = null)
    {
        spl_autoload_register(array(new self, 'autoload'));

        self::registerTwigAutoloader($pathToTwigLibs);
    }

    static public function registerTwigAutoloader($pathToTwigLibs = null)
    {
        if (is_null($pathToTwigLibs)) {
            App::import('vendor', 'cakephp-twig.Autoloader',
                        array('file' => 'Twig' . DS . 'lib'. DS . 'Twig' . DS . 'Autoloader.php'));
        } else {
            App::import('vendor', 'TwigAutoloader', array('file' => $pathToTwigLibs));
        }

        Twig_Autoloader::register();
    }

    static public function autoload($class)
    {
        if (0 !== strpos($class, 'Twig')) {
            return;
        }

        $userLibsDir = APP . "libs" . DS . "CakePhpTwig" . DS;
        if (file_exists($file = $userLibsDir . str_replace('_', '/', $class) . '.php')) {
            require $file;
        }
    }
}