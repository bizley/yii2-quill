<?php

namespace bizley\quill\assets;

use yii\base\InvalidConfigException;
use yii\web\AssetBundle;
use yii\web\View;

/**
 * Highlight.js assets.
 *
 * Highlight.js can be found at
 * https://highlightjs.org/
 * https://github.com/isagalaev/highlight.js
 */
class HighlightAsset extends AssetBundle
{
    /**
     * @var string CDN URL.
     * @since 2.0
     */
    public $url = 'https://cdn.jsdelivr.net/gh/highlightjs/cdn-release@';

    /**
     * @var string version to fetch from CDN.
     * @since 2.0
     */
    public $version;

    /**
     * @var string stylesheet to fetch from CDN.
     * @since 2.0
     */
    public $style;

    /**
     * Registers CSS and JS file based on version.
     * @param View $view the view that the asset files are to be registered with.
     * @throws InvalidConfigException
     */
    public function registerAssetFiles($view)
    {
        if ($this->version === null) {
            throw new InvalidConfigException('You must provide version for Highlight.js!');
        }

        if ($this->style === null) {
            $style = 'default.min.css';
        } else {
            $style = substr($this->style, -8) === '.min.css' ? $this->style : $this->style . '.min.css';
        }
        $this->css = [$this->url . $this->version . '/build/styles/' . $style];
        $this->js = [$this->url . $this->version . '/build/highlight.min.js'];

        parent::registerAssetFiles($view);
    }
}
