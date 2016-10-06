<?php

namespace bizley\quill;

use yii\web\AssetBundle;

/**
 * Highlight.js assets.
 * 
 * @author Paweł Bizley Brzozowski
 * @version 2.0
 * @license Apache 2.0
 * https://github.com/bizley/yii2-quill
 * 
 * Quill can be found at
 * https://quilljs.com/
 * https://github.com/quilljs/quill/
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
    public $url = 'https://cdnjs.cloudflare.com/ajax/libs/highlight.js/';
    
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
     * Register CSS and JS file based on version.
     * @param \yii\web\View $view the view that the asset files are to be registered with.
     */
    public function registerAssetFiles($view)
    {
        $this->css = [$this->url . $this->version . '/styles/' . $this->style];
        $this->js = [$this->url . $this->version . '/highlight.min.js'];
        
        parent::registerAssetFiles($view);
    }
}
