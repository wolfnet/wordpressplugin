<?php

class WNT_WP_TemplateEngine extends Twig_Environment
{


    public function __construct()
    {
        $twigLoader = new Twig_Loader_Filesystem(dirname(__FILE__) . '/template');

        parent::__construct($twigLoader);

        $this->addFilter('_e', new Twig_Filter_Function('_e'));

    }


}
