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

        $this->assertSame(Quill::THEME_SNOW, $quill->theme);
        $this->assertTrue($quill->toolbarOptions);
        $this->assertNull($quill->placeholder);
        $this->assertNull($quill->bounds);
        $this->assertNull($quill->debug);
        $this->assertNull($quill->formats);
        $this->assertNull($quill->modules);
        $this->assertNull($quill->readOnly);
        $this->assertNull($quill->js);
        $this->assertSame(Quill::QUILL_VERSION, $quill->quillVersion);
        $this->assertNull($quill->configuration);
        $this->assertSame(Quill::KATEX_VERSION, $quill->katexVersion);
        $this->assertSame(Quill::HIGHLIGHTJS_VERSION, $quill->highlightVersion);
        $this->assertSame('default', $quill->highlightStyle);
        $this->assertSame(
            [
                'style' => 'min-height:150px;',
                'id' => 'editor-quill-0'
            ],
            $quill->options
        );
        $this->assertSame([], $quill->hiddenOptions);
        $this->assertSame('div', $quill->tag);
        $this->assertFalse($quill->localAssets);
        $this->assertFalse($quill->isKatex());
        $this->assertFalse($quill->isHighlightJs());
        $this->assertSame(
            [
                'theme' => Quill::THEME_SNOW,
                'modules' => ['toolbar' => true]
            ],
            $quill->getConfig()
        );
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

        $this->assertSame('test', $quill->theme);
        $this->assertSame(['theme' => 'test'], $quill->getConfig());
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

        $this->assertTrue($quill->isKatex());
        $this->assertSame(['modules' => ['formula' => true]], $quill->getConfig());
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

        $this->assertTrue($quill->isHighlightJs());
        $this->assertSame(['modules' => ['syntax' => true]], $quill->getConfig());
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

        $this->assertSame(
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

        $this->assertSame(Quill::THEME_SNOW, $quill->getConfig()['theme']);
        $this->assertSame(['toolbar' => true], $quill->getConfig()['modules']);
        $this->assertSame('test', $quill->getConfig()['bounds']->expression);
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

        $this->assertSame(
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

        $this->assertSame(
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

        $this->assertSame(
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

        $this->assertSame(
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

        $this->assertSame(
            [
                'theme' => Quill::THEME_SNOW,
                'modules' => [
                    'formula' => true,
                    'toolbar' => true
                ]
            ],
            $quill->getConfig()
        );
        $this->assertTrue($quill->isKatex());
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

        $this->assertSame(
            [
                'theme' => Quill::THEME_SNOW,
                'modules' => [
                    'syntax' => true,
                    'toolbar' => true
                ]
            ],
            $quill->getConfig()
        );
        $this->assertTrue($quill->isHighlightJs());
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

        $this->assertSame(
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
        $this->assertFalse($quill->isKatex());
        $this->assertFalse($quill->isHighlightJs());
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

        $this->assertSame(['theme' => Quill::THEME_SNOW], $quill->getConfig());
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

        $this->assertSame(
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

        $this->assertSame(
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

        $this->assertSame(
            '<input type="hidden" id="quill-0" name="test"><div id="editor-quill-0" style="min-height:150px;"></div>',
            (new Quill(['name' => 'test']))->run()
        );
        $this->assertSame([QuillAsset::class], array_keys(Yii::$app->view->assetBundles));
        $this->assertSame(Quill::QUILL_VERSION, Yii::$app->view->assetBundles[QuillAsset::class]->version);
        $this->assertSame(Quill::THEME_SNOW, Yii::$app->view->assetBundles[QuillAsset::class]->theme);
        $this->assertSame('https://cdn.quilljs.com/', Yii::$app->view->assetBundles[QuillAsset::class]->url);
        $this->assertSame(
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

        $this->assertSame(
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
        $this->assertSame([QuillLocalAsset::class], array_keys(Yii::$app->view->assetBundles));
        $this->assertSame(['quill.min.js'], Yii::$app->view->assetBundles[QuillLocalAsset::class]->js);
        $this->assertSame(['theme' => 'quill.core.css'], Yii::$app->view->assetBundles[QuillLocalAsset::class]->css);
        $this->assertSame(Quill::THEME_SNOW, Yii::$app->view->assetBundles[QuillLocalAsset::class]->theme);
        $this->assertSame(
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

        $this->assertSame(
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
        $this->assertSame(
            [
                KatexAsset::class,
                QuillAsset::class
            ],
            array_keys(Yii::$app->view->assetBundles)
        );
        $this->assertSame(Quill::KATEX_VERSION, Yii::$app->view->assetBundles[KatexAsset::class]->version);
        $this->assertSame('https://cdn.jsdelivr.net/npm/katex@', Yii::$app->view->assetBundles[KatexAsset::class]->url);
        $this->assertSame(
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

        $this->assertSame(
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
        $this->assertSame(
            [
                KatexLocalAsset::class,
                QuillLocalAsset::class
            ],
            array_keys(Yii::$app->view->assetBundles)
        );
        $this->assertSame(['katex.min.js'], Yii::$app->view->assetBundles[KatexLocalAsset::class]->js);
        $this->assertSame(['katex.min.css'], Yii::$app->view->assetBundles[KatexLocalAsset::class]->css);
        $this->assertSame(
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

        $this->assertSame(
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
        $this->assertSame(
            [
                QuillLocalAsset::class,
                SmartBreakLocalAsset::class
            ],
            array_keys(Yii::$app->view->assetBundles)
        );
        $this->assertSame(['smart-breaker.min.js'], Yii::$app->view->assetBundles[SmartBreakLocalAsset::class]->js);
        $this->assertSame(
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

        $this->assertSame(
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
        $this->assertSame(
            [
                HighlightAsset::class,
                QuillAsset::class
            ],
            array_keys(Yii::$app->view->assetBundles)
        );
        $this->assertSame(Quill::HIGHLIGHTJS_VERSION, Yii::$app->view->assetBundles[HighlightAsset::class]->version);
        $this->assertSame(
            'https://cdn.jsdelivr.net/gh/highlightjs/cdn-release@',
            Yii::$app->view->assetBundles[HighlightAsset::class]->url
        );
        $this->assertSame(
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

        $this->assertSame(
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
        $this->assertSame(
            [
                HighlightLocalAsset::class,
                QuillLocalAsset::class
            ],
            array_keys(Yii::$app->view->assetBundles)
        );
        $this->assertSame(['lib/highlight.js'], Yii::$app->view->assetBundles[HighlightLocalAsset::class]->js);
        $this->assertSame('default', Yii::$app->view->assetBundles[HighlightLocalAsset::class]->style);
        $this->assertSame(
            ['style' => 'styles/default.css'],
            Yii::$app->view->assetBundles[HighlightLocalAsset::class]->css
        );
        $this->assertSame(
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

        $this->assertSame(
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
        $this->assertSame(
            [
                KatexAsset::class,
                HighlightAsset::class,
                QuillAsset::class
            ],
            array_keys(Yii::$app->view->assetBundles)
        );
        $this->assertSame(
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

        $this->assertSame(
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
        $this->assertSame(
            [
                KatexLocalAsset::class,
                HighlightLocalAsset::class,
                QuillLocalAsset::class
            ],
            array_keys(Yii::$app->view->assetBundles)
        );
        $this->assertSame(
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

        $this->assertSame(
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
        $this->assertSame(
            [
                KatexLocalAsset::class,
                HighlightLocalAsset::class,
                QuillLocalAsset::class,
                SmartBreakLocalAsset::class
            ],
            array_keys(Yii::$app->view->assetBundles)
        );
        $this->assertSame(
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

        $this->assertSame(
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
        $this->assertSame([QuillAsset::class], array_keys(Yii::$app->view->assetBundles));
        $this->assertSame(
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

        $this->assertSame(
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
        $this->assertSame([QuillAsset::class], array_keys(Yii::$app->view->assetBundles));
        $this->assertSame(
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

        $this->assertSame(
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
        $this->assertSame([QuillAsset::class], array_keys(Yii::$app->view->assetBundles));
        $this->assertSame(
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
}
