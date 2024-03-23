<?php

namespace Source\Interface;

interface MinifyInterface
{
    public function minifyCss(): void;

    public function minifyJs(): void;
}