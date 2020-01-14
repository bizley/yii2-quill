<?php

namespace bizley\quill\assets;

use yii\web\AssetBundle;

/**
 * Local Highlight.js assets (9.17.1).
 *
 * Highlight.js can be found at
 * https://highlightjs.org/
 * https://github.com/isagalaev/highlight.js
 */
class HighlightLocalAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@bizley/quill/assets/highlightjs';

    /**
     * @var array
     */
    public $js = ['highlight.min.js'];

    /**
     * @var array
     */
    public $css = ['default.min.css'];
}
