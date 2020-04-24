<?php
/**
 * @package yii2-quill
 * @author Simon Karlen <simi.albi@gmail.com>
 */

namespace bizley\quill\assets;


use yii\web\AssetBundle;

class SmartBreakLocalAsset extends AssetBundle
{
    /**
     * {@inheritDoc}
     */
    public $sourcePath = '@npm/quill-smart-break/dist';

    /**
     * {@inheritDoc}
     */
    public $js = ['smart-breaker.min.js'];
}