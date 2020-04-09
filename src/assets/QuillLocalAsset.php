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
     * {@inheritDoc}
     */
    public $sourcePath = '@npm/quill/dist';

    /**
     * {@inheritDoc}
     */
    public $js = [
        'quill.min.js'
    ];

    /**
     * {@inheritDoc}
     */
    public $css = [
        'theme' => 'quill.core.css'
    ];

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
                $this->css['theme'] = 'quill.snow.css';
                break;

            case Quill::THEME_BUBBLE:
                $this->css['theme'] = 'quill.bubble.css';
                break;

            default:
                if (null !== $this->theme) {
                    $this->css['theme'] = $this->theme;
                }
        }

        parent::registerAssetFiles($view);
    }
}
