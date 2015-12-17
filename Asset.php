<?php

namespace bizley\quill;

use yii\web\AssetBundle;

/**
 * Quill Assets
 * 
 * @author Paweł Bizley Brzozowski <pb@human-device.com>
 */
class Asset extends AssetBundle
{
    
    /**
     * @inheritdoc
     */
    public $sourcePath = '@vendor/bizley/quill/quill/dist';
    
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
     * Register css file based on theme.
     * @param \yii\web\View $view the view that the asset files are to be registered with.
     */
    public function registerAssetFiles($view)
    {
        if (in_array($this->theme, ['base', 'snow'])) {
            $this->css[] = 'quill.' . $this->theme . '.css';
        }
        
        parent::registerAssetFiles($view);
    }
}
