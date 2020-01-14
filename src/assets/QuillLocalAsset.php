<?php

namespace bizley\quill\assets;

use bizley\quill\Quill;
use yii\web\AssetBundle;
use yii\web\View;

/**
 * Local Quill assets (1.3.7).
 *
 * Quill can be found at
 * https://quilljs.com/
 * https://github.com/quilljs/quill/
 */
class QuillLocalAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@bizley/quill/assets/quill';

    /**
     * @var array
     */
    public $js = ['quill.min.js'];

    /**
     * @var string editor theme
     */
    public $theme;

    /**
     * Registers CSS file based on theme.
     * @param View $view the view that the asset files are to be registered with.
     */
    public function registerAssetFiles($view)
    {
        switch ($this->theme) {
            case Quill::THEME_SNOW:
                $this->css = ['quill.snow.css'];
                break;

            case Quill::THEME_BUBBLE:
                $this->css = ['quill.bubble.css'];
                break;

            default:
                $this->css = ['quill.core.css'];
        }

        parent::registerAssetFiles($view);
    }
}
