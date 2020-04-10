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
     * {@inheritDoc}
     */
    public $sourcePath = '@npm/katex/dist';

    /**
     * {@inheritDoc}
     */
    public $js = [
        'katex.min.js'
    ];

    /**
     * {@inheritDoc}
     */
    public $css = [
        'katex.min.css'
    ];
}
