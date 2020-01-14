<?php

namespace bizley\quill\assets;

use yii\web\AssetBundle;

/**
 * Local KaTeX assets (0.11.1).
 *
 * KaTeX can be found at
 * https://khan.github.io/KaTeX/
 * https://github.com/Khan/KaTeX
 */
class KatexLocalAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@bizley/quill/assets/katex';

    /**
     * @var array
     */
    public $js = ['katex.min.js'];

    /**
     * @var array
     */
    public $css = ['katex.min.css'];
}
