<?php

App::import('vendor', 'Twig',
		array('file' => 'Twig' . DS . 'Autoloader.php'));

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

		$cacheDir = CACHE . 'twig';
		$debugMode = (bool)Configure::read('debug');

		$this->twig = new Twig_Environment($loader, array(
			'cache' => $cacheDir,
			'debug' => $debugMode
		));

		$this->ext = '.htm';

		$this->twig->addExtension(new Twig_Extension_Project());
	}

	/**
	 * Renders and returns output for given view filename with its
	 * array of data.
	 *
	 * @param string $___viewFn Filename of the view
	 * @param array $___dataForView Data to include in rendered view
	 * @param boolean $loadHelpers Boolean to indicate that helpers should be loaded.
	 * @param boolean $cached Whether or not to trigger the creation of a cache file.
	 * @return string Rendered output
	 * @access protected
	 */
	function _render($___viewFn, $___dataForView, $loadHelpers = true, $cached = false) {
		$loadedHelpers = array();

		if ($this->helpers != false && $loadHelpers === true) {
			$loadedHelpers = $this->_loadHelpers($loadedHelpers, $this->helpers);
			$helpers = array_keys($loadedHelpers);
			$helperNames = array_map(array('Inflector', 'variable'), $helpers);

			for ($i = count($helpers) - 1; $i >= 0; $i--) {
				$name = $helperNames[$i];
				$helper =& $loadedHelpers[$helpers[$i]];

				if (!isset($___dataForView[$name])) {
					//${$name} =& $helper;
					$___dataForView[$name] =& $helper;
				}
				$this->loaded[$helperNames[$i]] =& $helper;
				$this->{$helpers[$i]} =& $helper;
			}
			$this->_triggerHelpers('beforeRender');
			unset($name, $loadedHelpers, $helpers, $i, $helperNames, $helper);
		}

		$___dataForView['view'] =& $this;

		ob_start();

		if (Configure::read() > 0) {
			include ($___viewFn);
		} else {
			@include ($___viewFn);
		}

		if ($loadHelpers === true) {
			$this->_triggerHelpers('afterRender');
		}

		$template = $this->twig->loadTemplate(ob_get_clean());
		$out = $template->render($___dataForView);
		//$out = ob_get_clean();

		$caching = (
			isset($this->loaded['cache']) &&
			(($this->cacheAction != false)) && (Configure::read('Cache.check') === true)
		);

		if ($caching) {
			if (is_a($this->loaded['cache'], 'CacheHelper')) {
				$cache =& $this->loaded['cache'];
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
}