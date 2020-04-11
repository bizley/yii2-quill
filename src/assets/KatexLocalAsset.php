<?php

declare(strict_types=1);

namespace bizley\quill\assets;

use yii\web\AssetBundle;

/**
 * Local KaTeX assets.
 *
 * KaTeX can be found at
 * https://khan.github.io/KaTeX/
 * https://github.com/Khan/KaTeX
 */
class KatexLocalAsset extends AssetBundle
{
    /** {@inheritdoc} */
    public $sourcePath = '@npm/katex/dist';

    /** {@inheritdoc} */
    public $js = ['katex.min.js'];

    /** {@inheritdoc} */
    public $css = ['katex.min.css'];
}
