<?php
interface CakePhpTwig_Bootstrapable
{
    public static function bootstrapOptions(array &$options);
    public static function bootstrapEnvironment(Twig_Environment $env);
}