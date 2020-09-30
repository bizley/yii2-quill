<?php

declare(strict_types=1);

namespace bizley\tests;

use bizley\quill\assets\HighlightAsset;
use bizley\quill\assets\HighlightLocalAsset;
use bizley\quill\assets\KatexAsset;
use bizley\quill\assets\KatexLocalAsset;
use bizley\quill\assets\QuillAsset;
use bizley\quill\assets\QuillLocalAsset;
use bizley\quill\assets\SmartBreakLocalAsset;
use bizley\quill\Quill;
use PHPUnit\Framework\TestCase;
use Yii;
use yii\base\DynamicModel;
use yii\base\InvalidConfigException;
use yii\web\Application;
use yii\web\View;
use yii\widgets\ActiveForm;

class QuillTest extends TestCase
{
    protected function setUp(): void
    {
        Quill::$counter = 0;
    }

    /** @throws InvalidConfigException */
    public static function setApp(): void
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

    /** @test */
    public function shouldThrowExceptionOnEmptyQuillVersion(): void
    {
        $this->expectExceptionMessage('The "quillVersion" property must be a non-empty string!');
        new Quill(
            [
                'name' => 'test',
                'quillVersion' => null
            ]
        );
    }

    /** @test */
    public function shouldThrowExceptionOnNonStringQuillVersion(): void
    {
        $this->expectExceptionMessage('The "quillVersion" property must be a non-empty string!');
        new Quill(
            [
                'name' => 'test',
                'quillVersion' => 1
            ]
        );
    }

    /** @test */
    public function shouldThrowExceptionOnNonArrayConfiguration(): void
    {
        $this->expectExceptionMessage('The "configuration" property must be an array!');
        new Quill(
            [
                'name' => 'test',
                'configuration' => 'a'
            ]
        );
    }

    /** @test */
    public function shouldThrowExceptionOnNonStringJs(): void
    {
        $this->expectExceptionMessage('The "js" property must be a string!');
        new Quill(
            [
                'name' => 'test',
                'js' => 1
            ]
        );
    }

    /** @test */
    public function shouldThrowExceptionOnNonArrayFormats(): void
    {
        $this->expectExceptionMessage('The "formats" property must be an array!');
        new Quill(
            [
                'name' => 'test',
                'formats' => 1
            ]
        );
    }

    /** @test */
    public function shouldThrowExceptionOnNonArrayModules(): void
    {
        $this->expectExceptionMessage('The "modules" property must be an array!');
        new Quill(
            [
                'name' => 'test',
                'modules' => 1
            ]
        );
    }

    /** @test */
    public function shouldThrowExceptionOnNonArrayIcons(): void
    {
        $this->expectExceptionMessage('The "icons" property must be an associative array!');
        new Quill(
            [
                'name' => 'test',
                'icons' => 1
            ]
        );
    }

    /** @test */
    public function shouldThrowExceptionOnNonAssociativeArrayIcons(): void
    {
        $this->expectExceptionMessage('The "icons" property must be an associative array!');
        new Quill(
            [
                'name' => 'test',
                'icons' => ['abc']
            ]
        );
    }

    /** @test */
    public function shouldPrepareDefaultOptions(): void
    {
        $quill = new Quill(['name' => 'test']);

        self::assertSame(Quill::THEME_SNOW, $quill->theme);
        self::assertTrue($quill->toolbarOptions);
        self::assertNull($quill->placeholder);
        self::assertNull($quill->bounds);
        self::assertNull($quill->debug);
        self::assertNull($quill->formats);
        self::assertNull($quill->modules);
        self::assertNull($quill->readOnly);
        self::assertNull($quill->js);
        self::assertSame(Quill::QUILL_VERSION, $quill->quillVersion);
        self::assertNull($quill->configuration);
        self::assertSame(Quill::KATEX_VERSION, $quill->katexVersion);
        self::assertSame(Quill::HIGHLIGHTJS_VERSION, $quill->highlightVersion);
        self::assertSame('default', $quill->highlightStyle);
        self::assertSame(
            [
                'id' => 'editor-quill-0',
                'style' => 'min-height:150px;',
            ],
            $quill->options
        );
        self::assertSame([], $quill->hiddenOptions);
        self::assertSame('div', $quill->tag);
        self::assertFalse($quill->localAssets);
        self::assertFalse($quill->isKatex());
        self::assertFalse($quill->isHighlightJs());
        self::assertSame(
            [
                'theme' => Quill::THEME_SNOW,
                'modules' => ['toolbar' => true]
            ],
            $quill->getConfig()
        );
        self::assertFalse($quill->allowResize);
    }

    /** @test */
    public function shouldNotSetDefaultMinHeightWithAllowResize(): void
    {
        $quill = new Quill(
            [
                'name' => 'test',
                'allowResize' => true,
            ]
        );

        self::assertSame(['id' => 'editor-quill-0'], $quill->options);
        self::assertTrue($quill->allowResize);
    }

    /** @test */
    public function shouldPrepareThemeThroughConfig(): void
    {
        $quill = new Quill(
            [
                'name' => 'test',
                'configuration' => ['theme' => 'test']
            ]
        );

        self::assertSame('test', $quill->theme);
        self::assertSame(['theme' => 'test'], $quill->getConfig());
    }

    /** @test */
    public function shouldPrepareKatexThroughConfig(): void
    {
        $quill = new Quill(
            [
                'name' => 'test',
                'configuration' => ['modules' => ['formula' => true]]
            ]
        );

        self::assertTrue($quill->isKatex());
        self::assertSame(['modules' => ['formula' => true]], $quill->getConfig());
    }

    /** @test */
    public function shouldPrepareHighlightJsThroughConfig(): void
    {
        $quill = new Quill(
            [
                'name' => 'test',
                'configuration' => ['modules' => ['syntax' => true]]
            ]
        );

        self::assertTrue($quill->isHighlightJs());
        self::assertSame(['modules' => ['syntax' => true]], $quill->getConfig());
    }

    /** @test */
    public function shouldAddThemeToConfig(): void
    {
        $quill = new Quill(
            [
                'name' => 'test',
                'theme' => 'test'
            ]
        );

        self::assertSame(
            [
                'theme' => 'test',
                'modules' => ['toolbar' => true]
            ],
            $quill->getConfig()
        );
    }

    /** @test */
    public function shouldAddBoundsToConfig(): void
    {
        $quill = new Quill(
            [
                'name' => 'test',
                'bounds' => 'test'
            ]
        );

        self::assertSame(Quill::THEME_SNOW, $quill->getConfig()['theme']);
        self::assertSame(['toolbar' => true], $quill->getConfig()['modules']);
        self::assertSame('test', $quill->getConfig()['bounds']->expression);
    }

    /** @test */
    public function shouldAddDebugToConfig(): void
    {
        $quill = new Quill(
            [
                'name' => 'test',
                'debug' => 'error'
            ]
        );

        self::assertSame(
            [
                'theme' => Quill::THEME_SNOW,
                'debug' => 'error',
                'modules' => ['toolbar' => true]
            ],
            $quill->getConfig()
        );
    }

    /** @test */
    public function shouldAddPlaceholderToConfig(): void
    {
        $quill = new Quill(
            [
                'name' => 'test',
                'placeholder' => 'p'
            ]
        );

        self::assertSame(
            [
                'theme' => Quill::THEME_SNOW,
                'placeholder' => 'p',
                'modules' => ['toolbar' => true]
            ],
            $quill->getConfig()
        );
    }

    /** @test */
    public function shouldAddFormatsToConfig(): void
    {
        $quill = new Quill(
            [
                'name' => 'test',
                'formats' => ['p']
            ]
        );

        self::assertSame(
            [
                'theme' => Quill::THEME_SNOW,
                'formats' => ['p'],
                'modules' => ['toolbar' => true]
            ],
            $quill->getConfig()
        );
    }

    /** @test */
    public function shouldAddReadOnlyToConfig(): void
    {
        $quill = new Quill(
            [
                'name' => 'test',
                'readOnly' => true
            ]
        );

        self::assertSame(
            [
                'theme' => Quill::THEME_SNOW,
                'readOnly' => true,
                'modules' => ['toolbar' => true]
            ],
            $quill->getConfig()
        );
    }

    /** @test */
    public function shouldAddKatexToConfig(): void
    {
        $quill = new Quill(
            [
                'name' => 'test',
                'modules' => ['formula' => true]
            ]
        );

        self::assertSame(
            [
                'theme' => Quill::THEME_SNOW,
                'modules' => [
                    'formula' => true,
                    'toolbar' => true
                ]
            ],
            $quill->getConfig()
        );
        self::assertTrue($quill->isKatex());
    }

    /** @test */
    public function shouldAddHighlightJsToConfig(): void
    {
        $quill = new Quill(
            [
                'name' => 'test',
                'modules' => ['syntax' => true]
            ]
        );

        self::assertSame(
            [
                'theme' => Quill::THEME_SNOW,
                'modules' => [
                    'syntax' => true,
                    'toolbar' => true
                ]
            ],
            $quill->getConfig()
        );
        self::assertTrue($quill->isHighlightJs());
    }

    /** @test */
    public function shouldAddModulesToConfig(): void
    {
        $quill = new Quill(
            [
                'name' => 'test',
                'modules' => [
                    'custom1' => true,
                    'custom2' => true,
                ]
            ]
        );

        self::assertSame(
            [
                'theme' => Quill::THEME_SNOW,
                'modules' => [
                    'custom1' => true,
                    'custom2' => true,
                    'toolbar' => true
                ]
            ],
            $quill->getConfig()
        );
        self::assertFalse($quill->isKatex());
        self::assertFalse($quill->isHighlightJs());
    }

    /** @test */
    public function shouldNotAddToolbarToConfigWhenToolbarOptionsAreEmpty(): void
    {
        $quill = new Quill(
            [
                'name' => 'test',
                'toolbarOptions' => null
            ]
        );

        self::assertSame(['theme' => Quill::THEME_SNOW], $quill->getConfig());
    }

    /** @test */
    public function shouldAddBasicToolbarToConfig(): void
    {
        $quill = new Quill(
            [
                'name' => 'test',
                'toolbarOptions' => Quill::TOOLBAR_BASIC
            ]
        );

        self::assertSame(
            [
                'theme' => Quill::THEME_SNOW,
                'modules' => [
                    'toolbar' => [
                        [
                            'bold',
                            'italic',
                            'underline',
                            'strike',
                        ],
                        [
                            ['list' => 'ordered'],
                            ['list' => 'bullet'],
                        ],
                        [
                            ['align' => []],
                        ],
                        ['link'],
                    ]
                ]
            ],
            $quill->getConfig()
        );
    }

    /** @test */
    public function shouldAddFullToolbarToConfig(): void
    {
        $quill = new Quill(
            [
                'name' => 'test',
                'toolbarOptions' => Quill::TOOLBAR_FULL
            ]
        );

        self::assertSame(
            [
                'theme' => Quill::THEME_SNOW,
                'modules' => [
                    'toolbar' => [
                        [
                            ['font' => []],
                            [
                                'size' => [
                                    'small',
                                    false,
                                    'large',
                                    'huge',
                                ],
                            ],
                        ],
                        [
                            'bold',
                            'italic',
                            'underline',
                            'strike',
                        ],
                        [
                            ['color' => []],
                            ['background' => []],
                        ],
                        [
                            ['script' => 'sub'],
                            ['script' => 'super'],
                        ],
                        [
                            ['header' => 1],
                            ['header' => 2],
                            'blockquote',
                            'code-block',
                        ],
                        [
                            ['list' => 'ordered'],
                            ['list' => 'bullet'],
                            ['indent' => '-1'],
                            ['indent' => '+1'],
                        ],
                        [
                            ['direction' => 'rtl'],
                            ['align' => []],
                        ],
                        [
                            'link',
                            'image',
                            'video',
                        ],
                        ['clean'],
                    ]
                ]
            ],
            $quill->getConfig()
        );
    }

    /**
     * @test
     * @throws InvalidConfigException
     */
    public function shouldRegisterOnlyCDNQuill(): void
    {
        static::setApp();

        self::assertSame(
            '<input type="hidden" id="quill-0" name="test"><div id="editor-quill-0" style="min-height:150px;"></div>',
            (new Quill(['name' => 'test']))->run()
        );
        self::assertSame([QuillAsset::class], array_keys(Yii::$app->view->assetBundles));
        self::assertSame(Quill::QUILL_VERSION, Yii::$app->view->assetBundles[QuillAsset::class]->version);
        self::assertSame(Quill::THEME_SNOW, Yii::$app->view->assetBundles[QuillAsset::class]->theme);
        self::assertSame('https://cdn.quilljs.com/', Yii::$app->view->assetBundles[QuillAsset::class]->url);
        self::assertSame(
            [
                'var q_quill_0=new Quill("#editor-quill-0",{"theme":"snow","modules":{"toolbar":true}});'
                . 'q_quill_0.on(\'text-change\',function(){'
                . 'document.getElementById("quill-0").value=q_quill_0.root.innerHTML;});'
            ],
            array_values(Yii::$app->view->js[View::POS_END])
        );
        Yii::$app = null;
    }

    /**
     * @test
     * @throws InvalidConfigException
     */
    public function shouldRegisterOnlyLocalQuill(): void
    {
        static::setApp();

        self::assertSame(
            '<input type="hidden" id="quill-0" name="test"><div id="editor-quill-0" style="min-height:150px;"></div>',
            (
                new Quill(
                    [
                        'name' => 'test',
                        'localAssets' => true,
                    ]
                )
            )->run()
        );
        self::assertSame([QuillLocalAsset::class], array_keys(Yii::$app->view->assetBundles));
        self::assertSame(['quill.min.js'], Yii::$app->view->assetBundles[QuillLocalAsset::class]->js);
        self::assertSame(['theme' => 'quill.core.css'], Yii::$app->view->assetBundles[QuillLocalAsset::class]->css);
        self::assertSame(Quill::THEME_SNOW, Yii::$app->view->assetBundles[QuillLocalAsset::class]->theme);
        self::assertSame(
            [
                'var q_quill_0=new Quill("#editor-quill-0",{"theme":"snow","modules":{"toolbar":true}});'
                . 'q_quill_0.on(\'text-change\',function(){'
                . 'document.getElementById("quill-0").value=q_quill_0.root.innerHTML;});'
            ],
            array_values(Yii::$app->view->js[View::POS_END])
        );
        Yii::$app = null;
    }

    /**
     * @test
     * @throws InvalidConfigException
     */
    public function shouldRegisterCDNQuillAndKatex(): void
    {
        static::setApp();

        self::assertSame(
            '<input type="hidden" id="quill-0" name="test"><div id="editor-quill-0" style="min-height:150px;"></div>',
            (
                new Quill(
                    [
                        'name' => 'test',
                        'modules' => ['formula' => true],
                        'toolbarOptions' => [['formula']],
                    ]
                )
            )->run()
        );
        self::assertSame(
            [
                KatexAsset::class,
                QuillAsset::class
            ],
            array_keys(Yii::$app->view->assetBundles)
        );
        self::assertSame(Quill::KATEX_VERSION, Yii::$app->view->assetBundles[KatexAsset::class]->version);
        self::assertSame('https://cdn.jsdelivr.net/npm/katex@', Yii::$app->view->assetBundles[KatexAsset::class]->url);
        self::assertSame(
            [
                'var q_quill_0=new Quill("#editor-quill-0",'
                . '{"theme":"snow","modules":{"formula":true,"toolbar":[["formula"]]}});'
                . 'q_quill_0.on(\'text-change\',function(){'
                . 'document.getElementById("quill-0").value=q_quill_0.root.innerHTML;});'
            ],
            array_values(Yii::$app->view->js[View::POS_END])
        );
        Yii::$app = null;
    }

    /**
     * @test
     * @throws InvalidConfigException
     */
    public function shouldRegisterLocalQuillAndKatex(): void
    {
        static::setApp();

        self::assertSame(
            '<input type="hidden" id="quill-0" name="test"><div id="editor-quill-0" style="min-height:150px;"></div>',
            (
            new Quill(
                [
                    'name' => 'test',
                    'localAssets' => true,
                    'modules' => ['formula' => true],
                    'toolbarOptions' => [['formula']],
                ]
            )
            )->run()
        );
        self::assertSame(
            [
                KatexLocalAsset::class,
                QuillLocalAsset::class
            ],
            array_keys(Yii::$app->view->assetBundles)
        );
        self::assertSame(['katex.min.js'], Yii::$app->view->assetBundles[KatexLocalAsset::class]->js);
        self::assertSame(['katex.min.css'], Yii::$app->view->assetBundles[KatexLocalAsset::class]->css);
        self::assertSame(
            [
                'var q_quill_0=new Quill("#editor-quill-0",'
                . '{"theme":"snow","modules":{"formula":true,"toolbar":[["formula"]]}});'
                . 'q_quill_0.on(\'text-change\',function(){'
                . 'document.getElementById("quill-0").value=q_quill_0.root.innerHTML;});'
            ],
            array_values(Yii::$app->view->js[View::POS_END])
        );
        Yii::$app = null;
    }

    /**
     * @test
     * @throws InvalidConfigException
     */
    public function shouldRegisterLocalQuillAndSmartBreak()
    {
        static::setApp();

        self::assertSame(
            '<input type="hidden" id="quill-0" name="test"><div id="editor-quill-0" style="min-height:150px;"></div>',
            (
            new Quill(
                [
                    'name' => 'test',
                    'localAssets' => true,
                    'modules' => ['smart-breaker' => true]
                ]
            )
            )->run()
        );
        self::assertSame(
            [
                QuillLocalAsset::class,
                SmartBreakLocalAsset::class
            ],
            array_keys(Yii::$app->view->assetBundles)
        );
        self::assertSame(['smart-breaker.min.js'], Yii::$app->view->assetBundles[SmartBreakLocalAsset::class]->js);
        self::assertSame(
            [
                'var q_quill_0=new Quill("#editor-quill-0",'
                . '{"theme":"snow","modules":{"smart-breaker":true,"toolbar":true}});'
                . 'q_quill_0.on(\'text-change\',function(){'
                . 'document.getElementById("quill-0").value=q_quill_0.root.innerHTML;});'
            ],
            array_values(Yii::$app->view->js[View::POS_END])
        );
        Yii::$app = null;
    }

    /**
     * @test
     * @throws InvalidConfigException
     */
    public function shouldRegisterCDNQuillAndHighlightJs(): void
    {
        static::setApp();

        self::assertSame(
            '<input type="hidden" id="quill-0" name="test"><div id="editor-quill-0" style="min-height:150px;"></div>',
            (
            new Quill(
                [
                    'name' => 'test',
                    'modules' => ['syntax' => true],
                    'toolbarOptions' => [['code-block']],
                ]
            )
            )->run()
        );
        self::assertSame(
            [
                HighlightAsset::class,
                QuillAsset::class
            ],
            array_keys(Yii::$app->view->assetBundles)
        );
        self::assertSame(Quill::HIGHLIGHTJS_VERSION, Yii::$app->view->assetBundles[HighlightAsset::class]->version);
        self::assertSame(
            'https://cdn.jsdelivr.net/gh/highlightjs/cdn-release@',
            Yii::$app->view->assetBundles[HighlightAsset::class]->url
        );
        self::assertSame(
            [
                'var q_quill_0=new Quill("#editor-quill-0",'
                . '{"theme":"snow","modules":{"syntax":true,"toolbar":[["code-block"]]}});'
                . 'q_quill_0.on(\'text-change\',function(){'
                . 'document.getElementById("quill-0").value=q_quill_0.root.innerHTML;});'
            ],
            array_values(Yii::$app->view->js[View::POS_END])
        );
        Yii::$app = null;
    }

    /**
     * @test
     * @throws InvalidConfigException
     */
    public function shouldRegisterLocalQuillAndHighlightJs(): void
    {
        static::setApp();

        self::assertSame(
            '<input type="hidden" id="quill-0" name="test"><div id="editor-quill-0" style="min-height:150px;"></div>',
            (
            new Quill(
                [
                    'name' => 'test',
                    'localAssets' => true,
                    'modules' => ['syntax' => true],
                    'toolbarOptions' => [['code-block']],
                ]
            )
            )->run()
        );
        self::assertSame(
            [
                HighlightLocalAsset::class,
                QuillLocalAsset::class
            ],
            array_keys(Yii::$app->view->assetBundles)
        );
        self::assertSame(['lib/highlight.js'], Yii::$app->view->assetBundles[HighlightLocalAsset::class]->js);
        self::assertSame('default', Yii::$app->view->assetBundles[HighlightLocalAsset::class]->style);
        self::assertSame(
            ['style' => 'styles/default.css'],
            Yii::$app->view->assetBundles[HighlightLocalAsset::class]->css
        );
        self::assertSame(
            [
                'var q_quill_0=new Quill("#editor-quill-0",'
                . '{"theme":"snow","modules":{"syntax":true,"toolbar":[["code-block"]]}});'
                . 'q_quill_0.on(\'text-change\',function(){'
                . 'document.getElementById("quill-0").value=q_quill_0.root.innerHTML;});'
            ],
            array_values(Yii::$app->view->js[View::POS_END])
        );
        Yii::$app = null;
    }

    /**
     * @test
     * @throws InvalidConfigException
     */
    public function shouldRegisterCDNQuillKatexAndHighlightJs(): void
    {
        static::setApp();

        self::assertSame(
            '<input type="hidden" id="quill-0" name="test"><div id="editor-quill-0" style="min-height:150px;"></div>',
            (
            new Quill(
                [
                    'name' => 'test',
                    'modules' => [
                        'syntax' => true,
                        'formula' => true
                    ],
                    'toolbarOptions' => [['code-block', 'formula']],
                ]
            )
            )->run()
        );
        self::assertSame(
            [
                KatexAsset::class,
                HighlightAsset::class,
                QuillAsset::class
            ],
            array_keys(Yii::$app->view->assetBundles)
        );
        self::assertSame(
            [
                'var q_quill_0=new Quill("#editor-quill-0",'
                . '{"theme":"snow","modules":{"syntax":true,"formula":true,"toolbar":[["code-block","formula"]]}});'
                . 'q_quill_0.on(\'text-change\',function(){'
                . 'document.getElementById("quill-0").value=q_quill_0.root.innerHTML;});'
            ],
            array_values(Yii::$app->view->js[View::POS_END])
        );
        Yii::$app = null;
    }

    /**
     * @test
     * @throws InvalidConfigException
     */
    public function shouldRegisterLocalQuillKatexAndHighlightJs(): void
    {
        static::setApp();

        self::assertSame(
            '<input type="hidden" id="quill-0" name="test"><div id="editor-quill-0" style="min-height:150px;"></div>',
            (
            new Quill(
                [
                    'name' => 'test',
                    'localAssets' => true,
                    'modules' => [
                        'syntax' => true,
                        'formula' => true
                    ],
                    'toolbarOptions' => [['code-block', 'formula']],
                ]
            )
            )->run()
        );
        self::assertSame(
            [
                KatexLocalAsset::class,
                HighlightLocalAsset::class,
                QuillLocalAsset::class
            ],
            array_keys(Yii::$app->view->assetBundles)
        );
        self::assertSame(
            [
                'var q_quill_0=new Quill("#editor-quill-0",'
                . '{"theme":"snow","modules":{"syntax":true,"formula":true,"toolbar":[["code-block","formula"]]}});'
                . 'q_quill_0.on(\'text-change\',function(){'
                . 'document.getElementById("quill-0").value=q_quill_0.root.innerHTML;});'
            ],
            array_values(Yii::$app->view->js[View::POS_END])
        );
        Yii::$app = null;
    }

    /**
     * @test
     * @throws InvalidConfigException
     */
    public function shouldRegisterLocalQuillKatexAndHighlightJsAndSmartBreak(): void
    {
        static::setApp();

        self::assertSame(
            '<input type="hidden" id="quill-0" name="test"><div id="editor-quill-0" style="min-height:150px;"></div>',
            (
            new Quill(
                [
                    'name' => 'test',
                    'localAssets' => true,
                    'modules' => [
                        'syntax' => true,
                        'formula' => true,
                        'smart-breaker' => true
                    ],
                    'toolbarOptions' => [['code-block', 'formula']],
                ]
            )
            )->run()
        );
        self::assertSame(
            [
                KatexLocalAsset::class,
                HighlightLocalAsset::class,
                QuillLocalAsset::class,
                SmartBreakLocalAsset::class
            ],
            array_keys(Yii::$app->view->assetBundles)
        );
        self::assertSame(
            [
                'var q_quill_0=new Quill("#editor-quill-0",'
                . '{"theme":"snow","modules":{"syntax":true,"formula":true,"smart-breaker":true,"toolbar":[["code-block","formula"]]}});'
                . 'q_quill_0.on(\'text-change\',function(){'
                . 'document.getElementById("quill-0").value=q_quill_0.root.innerHTML;});'
            ],
            array_values(Yii::$app->view->js[View::POS_END])
        );
        Yii::$app = null;
    }

    /**
     * @test
     * @throws InvalidConfigException
     */
    public function shouldWorkWithModel(): void
    {
        static::setApp();

        self::assertSame(
            '<input type="hidden" id="dynamicmodel-test" name="DynamicModel[test]">'
            . '<div id="editor-quill-0" style="min-height:150px;"></div>',
            (
                new Quill(
                    [
                        'model' => new DynamicModel(['test' => null]),
                        'attribute' => 'test'
                    ]
                )
            )->run()
        );
        self::assertSame([QuillAsset::class], array_keys(Yii::$app->view->assetBundles));
        self::assertSame(
            [
                'var q_quill_0=new Quill("#editor-quill-0",{"theme":"snow","modules":{"toolbar":true}});'
                . 'q_quill_0.on(\'text-change\',function(){'
                . 'document.getElementById("dynamicmodel-test").value=q_quill_0.root.innerHTML;});'
            ],
            array_values(Yii::$app->view->js[View::POS_END])
        );
        Yii::$app = null;
    }

    /**
     * @test
     * @throws InvalidConfigException
     */
    public function shouldReplacePlaceholdersInJs(): void
    {
        static::setApp();

        self::assertSame(
            '<input type="hidden" id="quill-0" name="test"><div id="editor-quill-0" style="min-height:150px;"></div>',
            (
                new Quill(
                    [
                        'name' => 'test',
                        'js' => '{quill}.test = 1'
                    ]
                )
            )->run()
        );
        self::assertSame([QuillAsset::class], array_keys(Yii::$app->view->assetBundles));
        self::assertSame(
            [
                'var q_quill_0=new Quill("#editor-quill-0",{"theme":"snow","modules":{"toolbar":true}});'
                . 'q_quill_0.on(\'text-change\',function(){'
                . 'document.getElementById("quill-0").value=q_quill_0.root.innerHTML;});q_quill_0.test = 1'
            ],
            array_values(Yii::$app->view->js[View::POS_END])
        );
        Yii::$app = null;
    }

    /**
     * @test
     * @throws InvalidConfigException
     */
    public function shouldAddIcons(): void
    {
        static::setApp();

        self::assertSame(
            '<input type="hidden" id="quill-0" name="test"><div id="editor-quill-0" style="min-height:150px;"></div>',
            (
            new Quill(
                [
                    'name' => 'test',
                    'icons' => ['bold' => '<i class="fa fa-bold" aria-hidden="true"></i>']
                ]
            )
            )->run()
        );
        self::assertSame([QuillAsset::class], array_keys(Yii::$app->view->assetBundles));
        self::assertSame(
            [
                'var q_quill_0_icons=Quill.import(\'ui/icons\');'
                . 'q_quill_0_icons[\'bold\']="<i class=\"fa fa-bold\" aria-hidden=\"true\"></i>";'
                . 'var q_quill_0=new Quill("#editor-quill-0",{"theme":"snow","modules":{"toolbar":true}});'
                . 'q_quill_0.on(\'text-change\',function(){'
                . 'document.getElementById("quill-0").value=q_quill_0.root.innerHTML;});'
            ],
            array_values(Yii::$app->view->js[View::POS_END])
        );
        Yii::$app = null;
    }

    /**
     * @test
     * @throws InvalidConfigException
     */
    public function shouldWorkWithActiveField(): void
    {
        static::setApp();

        self::assertSame(
            '<div class="form-group field-dynamicmodel-test">
<label class="control-label" for="dynamicmodel-test">Test</label>
<input type="hidden" id="dynamicmodel-test" name="DynamicModel[test]"><div id="editor-quill-1" class="" style="min-height:150px;"></div>

<div class="help-block"></div>
</div>',
            (new ActiveForm())
                ->field(new DynamicModel(['test']), 'test')
                ->widget(Quill::class)
                ->render()
        );

        ob_get_clean();

        Yii::$app = null;
    }

    /**
     * @test
     * @throws InvalidConfigException
     */
    public function shouldAddCssForResizing(): void
    {
        static::setApp();

        self::assertSame(
            '<input type="hidden" id="quill-0" name="test"><div id="editor-quill-0"></div>',
            (
            new Quill(
                [
                    'name' => 'test',
                    'localAssets' => true,
                    'allowResize' => true,
                ]
            )
            )->run()
        );

        $css = array_values(Yii::$app->view->css);
        self::assertSame('<style>.ql-editor{resize:vertical;overflow-y:scroll}</style>', $css[0]);
        Yii::$app = null;
    }
}
