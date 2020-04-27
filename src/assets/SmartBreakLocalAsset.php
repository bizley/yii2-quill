<?php

declare(strict_types=1);

namespace bizley\quill\assets;

use yii\web\AssetBundle;

/**
 * Smart break assets.
 *
 * smart-breaker.js can be found at
 * https://github.com/simialbi/quill-smart-break
 * @author Simon Karlen <simi.albi@outlook.com>
 */
class SmartBreakLocalAsset extends AssetBundle
{
    /** {@inheritdoc} */
    public $sourcePath = '@npm/quill-smart-break/dist';

    /** {@inheritdoc} */
    public $js = ['smart-breaker.min.js'];
}
