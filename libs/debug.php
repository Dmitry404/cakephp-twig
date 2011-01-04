<?php

class Twig_Extension_Debug extends Twig_Extension
{
    /**
     * Returns a list of filters to add to the existing list.
     *
     * @return array An array of filters
     */
    public function getFilters()
    {
        return array(
            'var_dump' => new Twig_Filter_Function('var_dump'),
            'pr' => new Twig_Filter_Function('pr'),
        );
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'debug';
    }
}