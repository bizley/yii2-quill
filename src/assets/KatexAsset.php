<?php

declare(strict_types=1);

namespace bizley\quill\assets;

use yii\base\InvalidConfigException;
use yii\web\AssetBundle;
use yii\web\View;

/**
 * KaTeX assets.
 *
 * KaTeX can be found at
 * https://khan.github.io/KaTeX/
 * https://github.com/Khan/KaTeX
 */
class KatexAsset extends AssetBundle
{
    /**
     * @var string CDN URL.
     * @since 2.0
     */
    public $url = 'https://cdn.jsdelivr.net/npm/katex@';

    /**
     * @var string version to fetch from CDN.
     * @since 2.0
     */
    public $version;

    /**
     * Registers CSS and JS file based on version.
     * @param View $view the view that the asset files are to be registered with.
     * @throws InvalidConfigException
     */
    public function registerAssetFiles($view): void
    {
        if ($this->version === null) {
            throw new InvalidConfigException('You must provide version for KaTeX!');
        }

        $this->css = [$this->url . $this->version . '/dist/katex.min.css'];
        $this->js = [$this->url . $this->version . '/dist/katex.min.js'];

        parent::registerAssetFiles($view);
    }
}
