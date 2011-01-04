<?php
/**
 * This class - an extension that implements a Twig filters specific to your site
 * Please add your Twig filters here
 */
class Twig_Extension_SpecificToProject extends Twig_Extension
{
    /**
     * Returns a list of filters to add to the existing list.
     *
     * @return array An array of filters
     */
    public function getFilters()
    {
        return array(
            'nothing' => new Twig_Filter_Method($this, 'nothing_filter'),);
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'specific_to_project';
    }

    /**
     * It's example Twig filter
     * @return string
     */
    public function nothing_filter()
    {
        return null;
    }
}