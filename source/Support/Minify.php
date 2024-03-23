<?php

namespace Source\Support;

use MatthiasMullie\Minify\CSS;
use MatthiasMullie\Minify\JS;
use Source\Interface\MinifyInterface;

class Minify implements MinifyInterface
{
    public function __construct(
        private string $themePath = __DIR__ . '/../../themes/' . CONF_VIEW_THEME . '/assets',
        private string $applicationPath = __DIR__ . '/../../assets'
    ) {
    }

    public function minifyCss(): void
    {
        $minCSS = new CSS();

        $cssPath = "{$this->themePath}/css";
        $cssFiles = scandir($cssPath);

        $this->proccessMinify($cssPath, $cssFiles, $minCSS, 'styles.css');
    }

    public function minifyJs(): void
    {
        $minJS = new JS();

        $jsPath = "{$this->themePath}/js";
        $jsFiles = scandir($jsPath);

        $this->proccessMinify($jsPath, $jsFiles, $minJS, 'scripts.js');
    }

    private function proccessMinify(
        string $assetsPath,
        array $assetsFiles,
        \MatthiasMullie\Minify\Minify $minify,
        string $outputFile
    ) {
        $extension = str_replace('.', '', strstr($outputFile, '.'));

        foreach ($assetsFiles as $file) {
            $addFile = "{$assetsPath}/{$file}";
            $fileInfo = pathinfo($addFile);
            $fileExtension = $fileInfo['extension'] ?? null;

            if (is_file($addFile) && $fileExtension === $extension) {
                $minify->add($addFile);
            }

            $minify->minify("{$this->themePath}/{$outputFile}");
        }
    }
}