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
    public $sourcePath = '@npm/highlight.js';

    /**
     * @var array
     */
    public $js = [
        'lib/highlight.js'
    ];

    /**
     * @var string stylesheet to use.
     * @since 2.0
     */
    public $style;

    /**
     * @var array
     */
    public $css = [
        'style' => 'styles/default.css'
    ];

    /**
     * Registers CSS and JS file based on version.
     * @param \yii\web\View $view the view that the asset files are to be registered with.
     */
    public function registerAssetFiles($view)
    {
        $style = preg_replace('#(\.min)?\.css$#', '', $this->style);
        $this->css['style'] = 'styles/' . $style . '.css';

        parent::registerAssetFiles($view);
    }
}
