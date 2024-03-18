<?php

namespace Source\Interface;

interface SeoInterface
{
    public function getOptimizer();
    public function getData(string $title = null, string $desc = null, string $url = null, string $image = null): ?object;
    public function render(string $title, string $description, string $url, string $image, bool $follow = true): string;
}