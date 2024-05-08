<?php

namespace Source\Core;

use Source\Interface\MessageInterface;
use Source\Interface\SeoInterface;
use Source\Interface\ViewInterface;
use Source\Support\Seo;

abstract class Controller
{

    protected ViewInterface $view;

    public function __construct(
        protected ?string $pathToView = null,
        protected SeoInterface $seo = new Seo(),
        protected MessageInterface $message = new Message()
    ) {
        $this->view = new View($this->pathToView);
    }
}