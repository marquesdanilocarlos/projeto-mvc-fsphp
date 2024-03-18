<?php

namespace Source\Core;

use Source\Interface\SeoInterface;
use Source\Interface\ViewInterface;
use Source\Support\Seo;

abstract class Controller
{

    protected ViewInterface $view;
    protected SeoInterface $seo;

    public function __construct(
        protected ?string $pathToView = null
    )
    {
        $this->view = new View($this->pathToView);
        $this->seo = new Seo();
    }
}