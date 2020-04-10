<?php

declare(strict_types=1);

namespace bizley\tests\assets;

use bizley\quill\assets\QuillLocalAsset;
use bizley\quill\Quill;
use PHPUnit\Framework\TestCase;
use Yii;
use yii\base\InvalidConfigException;
use yii\web\Application;

class QuillLocalAssetTest extends TestCase
{
    /** @throws InvalidConfigException */
    public static function setUpBeforeClass()
    {
        new Application(
            [
                'id' => 'QuillTest',
                'basePath' => __DIR__ . '/../',
                'vendorPath' => __DIR__ . '/../vendor',
                'aliases' => ['@npm' => '@vendor/npm-asset'],
                'components' => [
                    'assetManager' => [
                        'linkAssets' => true,
                        'basePath' => '@bizley/tests/runtime',
                        'baseUrl' => '/'
                    ]
                ]
            ]
        );
    }

    public static function tearDownAfterClass()
    {
        Yii::$app = null;
    }

    public function providerForThemes()
    {
        return [
            'empty' => [null, 'quill.core.css'],
            'custom' => ['custom', 'custom'],
            'snow' => [Quill::THEME_SNOW, 'quill.snow.css'],
            'bubble' => [Quill::THEME_BUBBLE, 'quill.bubble.css'],
        ];
    }

    /**
     * @test
     * @dataProvider providerForThemes
     * @param $theme
     * @param $css
     */
    public function shouldProperlyRegisterAssetFiles($theme, $css)
    {
        $asset = new QuillLocalAsset();
        $asset->theme = $theme;
        $asset->registerAssetFiles(Yii::$app->view);

        $this->assertSame(['theme' => $css], $asset->css);
    }
}
