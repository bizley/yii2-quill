<?php

namespace bizley\quill\assets;

use yii\web\AssetBundle;

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
    public $url = 'https://cdnjs.cloudflare.com/ajax/libs/KaTeX/';
    
    /**
     * @var string version to fetch from CDN.
     * @since 2.0
     */
    public $version;
    
    /**
     * Register CSS and JS file based on version.
     * @param \yii\web\View $view the view that the asset files are to be registered with.
     */
    public function registerAssetFiles($view)
    {
        $this->css = [$this->url . $this->version . '/katex.min.css'];
        $this->js = [$this->url . $this->version . '/katex.min.js'];
        
        parent::registerAssetFiles($view);
    }
}
