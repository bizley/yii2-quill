<?php

declare(strict_types=1);

namespace bizley\tests\assets;

use bizley\quill\assets\QuillAsset;
use bizley\quill\Quill;
use PHPUnit\Framework\TestCase;
use Yii;
use yii\base\InvalidConfigException;
use yii\web\Application;

class QuillAssetTest extends TestCase
{
    /** @throws InvalidConfigException */
    public static function setUpBeforeClass(): void
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

    public static function tearDownAfterClass(): void
    {
        Yii::$app = null;
    }

    /**
     * @test
     * @throws InvalidConfigException
     */
    public function shouldThrowExceptionWhenNoVersionProvided(): void
    {
        $this->expectExceptionMessage('You must provide version for Quill!');

        $asset = new QuillAsset();
        $asset->registerAssetFiles(Yii::$app->view);
    }

    public function providerForThemes(): array
    {
        return [
            'default' => [
                null,
                'https://cdn.quilljs.com/' . Quill::QUILL_VERSION . '/quill.min.js',
                'https://cdn.quilljs.com/' . Quill::QUILL_VERSION . '/quill.core.css'
            ],
            'snow' => [
                Quill::THEME_SNOW,
                'https://cdn.quilljs.com/' . Quill::QUILL_VERSION . '/quill.min.js',
                'https://cdn.quilljs.com/' . Quill::QUILL_VERSION . '/quill.snow.css'
            ],
            'bubble' => [
                Quill::THEME_BUBBLE,
                'https://cdn.quilljs.com/' . Quill::QUILL_VERSION . '/quill.min.js',
                'https://cdn.quilljs.com/' . Quill::QUILL_VERSION . '/quill.bubble.css'
            ]
        ];
    }

    /**
     * @test
     * @dataProvider providerForThemes
     * @param string|null $theme
     * @param string $js
     * @param string $css
     * @throws InvalidConfigException
     */
    public function shouldProperlyRegisterAssetFiles(?string $theme, string $js, string $css): void
    {
        $asset = new QuillAsset();
        $asset->version = Quill::QUILL_VERSION;
        $asset->theme = $theme;
        $asset->registerAssetFiles(Yii::$app->view);

        $this->assertSame([$js], $asset->js);
        $this->assertSame([$css], $asset->css);
    }
}
