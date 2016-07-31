<?php

namespace bizley\quill;

use yii\web\AssetBundle;

/**
 * Quill assets.
 * 
 * @author Paweł Bizley Brzozowski
 * @version 1.2.0
 * @license Apache 2.0
 * https://github.com/bizley/yii2-quill
 * 
 * Quill can be found at
 * http://quilljs.com/
 * https://github.com/quilljs/quill/
 */
class Asset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = '@bower/quill/dist';
    
    /**
     * @inheritdoc
     */
    public $css = [];
    
    /**
     * @inheritdoc
     */
    public $js = ['quill.min.js'];
    
    /**
     * @var string editor theme
     */
    public $theme;
    
    /**
     * @inheritdoc
     */
    public $depends = ['yii\web\JqueryAsset'];
    
    /**
     * Register css file based on theme.
     * @param \yii\web\View $view the view that the asset files are to be registered with.
     */
    public function registerAssetFiles($view)
    {
        if (in_array($this->theme, [Quill::THEME_BASE, Quill::THEME_SNOW])) {
            $this->css[] = 'quill.' . $this->theme . '.css';
        }
        parent::registerAssetFiles($view);
    }
}
