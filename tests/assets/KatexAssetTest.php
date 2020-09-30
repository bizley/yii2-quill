<?php

declare(strict_types=1);

namespace bizley\tests\assets;

use bizley\quill\assets\KatexAsset;
use bizley\quill\Quill;
use PHPUnit\Framework\TestCase;
use Yii;
use yii\base\InvalidConfigException;
use yii\web\Application;

class KatexAssetTest extends TestCase
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
        $this->expectExceptionMessage('You must provide version for KaTeX!');

        $asset = new KatexAsset();
        $asset->registerAssetFiles(Yii::$app->view);
    }

    /**
     * @test
     * @throws InvalidConfigException
     */
    public function shouldProperlyRegisterAssetFiles(): void
    {
        $asset = new KatexAsset();
        $asset->version = Quill::KATEX_VERSION;
        $asset->registerAssetFiles(Yii::$app->view);

        self::assertSame(
            ['https://cdn.jsdelivr.net/npm/katex@' . Quill::KATEX_VERSION . '/dist/katex.min.js'],
            $asset->js
        );
        self::assertSame(
            ['https://cdn.jsdelivr.net/npm/katex@' . Quill::KATEX_VERSION . '/dist/katex.min.css'],
            $asset->css
        );
    }
}
