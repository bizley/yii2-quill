<?php

/**
 * @author Paweł Bizley Brzozowski
 * @version 1.0
 * @license Apache 2.0
 * https://github.com/bizley-code/yii2-quill
 * 
 * Quill can be found at
 * http://quilljs.com/
 * https://github.com/quilljs/quill/
 */

namespace bizley\quill;

use yii\web\AssetBundle;

/**
 * Quill assets.
 * 
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
