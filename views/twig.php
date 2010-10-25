<?php

App::import('vendor', 'TwigView.Twig',
		array('file' => 'Twig' . DS . 'lib'. DS . 'Twig' . DS . 'Autoloader.php'));

class TwigView extends View
{
	/**
	 * Twig environment object
	 * @access private
	 * @var Object
	 */
	private $twig = null;

	public function  __construct($controller)
	{
		parent::__construct($controller);

		Twig_Autoloader::register();

		$loader = new Twig_Loader_String();

		$cacheDir = APP . 'plugins' . DS . 'twig_view' . DS . 'tmp';
		$debugMode = (bool)Configure::read('debug');

		$this->twig = new Twig_Environment($loader, array(
			'cache' => $cacheDir,
            'strict_variables' => false,
			'debug' => $debugMode
		));

		$this->ext = '.htm';

		//$this->twig->addExtension(new Twig_Extension_Project());
	}

	/**
	 * Renders and returns output for given view filename with its array of data.
	 */
	function _render($___viewFn, $___dataForView, $loadHelpers = true, $cached = false)
    {
        if(pathinfo($___viewFn, PATHINFO_EXTENSION) == 'ctp') {
            return parent::_render($___viewFn, $___dataForView, $loadHelpers, $cached);
        }

        $loadedHelpers = array();

		if ($this->helpers != false && $loadHelpers === true) {
			$loadedHelpers = $this->_loadHelpers($loadedHelpers, $this->helpers);
			$helpers = array_keys($loadedHelpers);
			$helperNames = array_map(array('Inflector', 'variable'), $helpers);

			for ($i = count($helpers) - 1; $i >= 0; $i--) {
				$name = $helperNames[$i];
				$helper = $loadedHelpers[$helpers[$i]];

				if (!isset($___dataForView[$name])) {
					$___dataForView[$name] = $helper;
				}
				$this->loaded[$helperNames[$i]] = $helper;
				$this->{$helpers[$i]} = $helper;
			}
			$this->_triggerHelpers('beforeRender');
			unset($name, $loadedHelpers, $helpers, $i, $helperNames, $helper);
		}

		$___dataForView['view'] = $___dataForView['this'] = $this;

		ob_start();
		readfile($___viewFn);

        $template = $this->twig->loadTemplate(ob_get_clean());
        $out = $template->render($___dataForView);

        if ($loadHelpers === true) {
            $this->_triggerHelpers('afterRender');
        }

		$caching = (
			isset($this->loaded['cache']) &&
			(($this->cacheAction != false)) && (Configure::read('Cache.check') === true)
		);

		if ($caching) {
			if (is_a($this->loaded['cache'], 'CacheHelper')) {
				$cache = $this->loaded['cache'];
				$cache->base = $this->base;
				$cache->here = $this->here;
				$cache->helpers = $this->helpers;
				$cache->action = $this->action;
				$cache->controllerName = $this->name;
				$cache->layout = $this->layout;
				$cache->cacheAction = $this->cacheAction;
				$cache->cache($___viewFn, $out, $cached);
			}
		}

		return $out;
	}

 /**
 * Renders a piece of PHP with provided parameters and returns HTML, XML, or any other string. *
 */
	function element($name, $params = array(), $loadHelpers = false) {
		$file = $plugin = $key = null;

		if (isset($params['plugin'])) {
			$plugin = $params['plugin'];
		}

		if (isset($this->plugin) && !$plugin) {
			$plugin = $this->plugin;
		}

		if (isset($params['cache'])) {
			$expires = '+1 day';

			if (is_array($params['cache'])) {
				$expires = $params['cache']['time'];
				$key = Inflector::slug($params['cache']['key']);
			} elseif ($params['cache'] !== true) {
				$expires = $params['cache'];
				$key = implode('_', array_keys($params));
			}

			if ($expires) {
				$cacheFile = 'element_' . $key . '_' . $plugin . Inflector::slug($name);
				$cache = cache('views' . DS . $cacheFile, null, $expires);

				if (is_string($cache)) {
					return $cache;
				}
			}
		}
		$paths = $this->_paths($plugin);

        $exts = array($this->ext);
		if ($this->ext !== '.ctp') {
			array_push($exts, '.ctp');
		}

		foreach ($exts as $ext) {
			foreach ($paths as $path) {
                if (file_exists($path . 'elements' . DS . $name . $ext)) {
                    $file = $path . 'elements' . DS . $name . $ext;
                    break 2;
                }
			}
		}

		if (is_file($file)) {
			$params = array_merge_recursive($params, $this->loaded);
			$element = $this->_render($file, array_merge($this->viewVars, $params), $loadHelpers);
			if (isset($params['cache']) && isset($cacheFile) && isset($expires)) {
				cache('views' . DS . $cacheFile, $element, $expires);
			}
			return $element;
		}
		$file = $paths[0] . 'elements' . DS . $name . $this->ext;

		if (Configure::read() > 0) {
			return "Not Found: " . $file;
		}
	}
}