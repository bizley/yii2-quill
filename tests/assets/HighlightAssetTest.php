<?php

declare(strict_types=1);

namespace bizley\tests\assets;

use bizley\quill\assets\HighlightAsset;
use bizley\quill\Quill;
use PHPUnit\Framework\TestCase;
use Yii;
use yii\base\InvalidConfigException;
use yii\web\Application;

class HighlightAssetTest extends TestCase
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
        $this->expectExceptionMessage('You must provide version for Highlight.js!');

        $asset = new HighlightAsset();
        $asset->registerAssetFiles(Yii::$app->view);
    }

    public function providerForStyles(): array
    {
        return [
            'empty' => [
                null,
                'https://cdn.jsdelivr.net/gh/highlightjs/cdn-release@'
                . Quill::HIGHLIGHTJS_VERSION
                . '/build/highlight.min.js',
                'https://cdn.jsdelivr.net/gh/highlightjs/cdn-release@'
                . Quill::HIGHLIGHTJS_VERSION
                . '/build/styles/default.min.css'
            ],
            'without css' => [
                'test',
                'https://cdn.jsdelivr.net/gh/highlightjs/cdn-release@'
                . Quill::HIGHLIGHTJS_VERSION
                . '/build/highlight.min.js',
                'https://cdn.jsdelivr.net/gh/highlightjs/cdn-release@'
                . Quill::HIGHLIGHTJS_VERSION
                . '/build/styles/test.min.css'
            ],
            'with css' => [
                'test.min.css',
                'https://cdn.jsdelivr.net/gh/highlightjs/cdn-release@'
                . Quill::HIGHLIGHTJS_VERSION
                . '/build/highlight.min.js',
                'https://cdn.jsdelivr.net/gh/highlightjs/cdn-release@'
                . Quill::HIGHLIGHTJS_VERSION
                . '/build/styles/test.min.css'
            ]
        ];
    }

    /**
     * @test
     * @dataProvider providerForStyles
     * @param string|null $style
     * @param string $js
     * @param string $css
     * @throws InvalidConfigException
     */
    public function shouldProperlyRegisterAssetFiles(?string $style, string $js, string $css): void
    {
        $asset = new HighlightAsset();
        $asset->version = Quill::HIGHLIGHTJS_VERSION;
        $asset->style = $style;
        $asset->registerAssetFiles(Yii::$app->view);

        $this->assertSame([$js], $asset->js);
        $this->assertSame([$css], $asset->css);
    }
}
