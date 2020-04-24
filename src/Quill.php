<?php

declare(strict_types=1);

namespace bizley\quill;

use bizley\quill\assets\HighlightAsset;
use bizley\quill\assets\HighlightLocalAsset;
use bizley\quill\assets\KatexAsset;
use bizley\quill\assets\KatexLocalAsset;
use bizley\quill\assets\QuillAsset;
use bizley\quill\assets\QuillLocalAsset;
use bizley\quill\assets\SmartBreakLocalAsset;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Inflector;
use yii\helpers\Json;
use yii\web\JsExpression;
use yii\web\View;
use yii\widgets\InputWidget;

/**
 * Quill editor implementation for Yii 2.
 *
 * Use it as an active field:
 * <?= $form->field($model, $attribute)->widget(\bizley\quill\Quill::class, []) ?>
 *
 * Or as a standalone widget:
 * <?= \bizley\quill\Quill::widget(['name' => 'editor']) ?>
 *
 * See the documentation for more details.
 *
 * @author Paweł Bizley Brzozowski
 * @version 3.0.0
 * @license Apache 2.0
 * https://github.com/bizley/yii2-quill
 *
 * Quill itself can be found at
 * https://quilljs.com/
 * https://github.com/quilljs/quill/
 */
class Quill extends InputWidget
{
    public const THEME_SNOW = 'snow';
    public const THEME_BUBBLE = 'bubble';

    public const TOOLBAR_FULL = 'FULL';
    public const TOOLBAR_BASIC = 'BASIC';

    public const QUILL_VERSION = '1.3.7';
    public const KATEX_VERSION = '0.11.1';
    public const HIGHLIGHTJS_VERSION = '9.18.1';

    /** {@inheritdoc} */
    public static $autoIdPrefix = 'quill-';

    /**
     * @var string Theme to be set.
     * See https://quilljs.com/docs/themes/ for more info.
     * Set it to 'snow' [THEME_SNOW] to get snow theme.
     * Set it to 'bubble' [THEME_BUBBLE] to get bubble theme.
     * Set it to false or null to remove theme.
     * This property is skipped if $configuration is set.
     */
    public $theme = self::THEME_SNOW;

    /**
     * @var bool|string|array Toolbar buttons.
     * Set true to get theme default buttons.
     * You can set it to 'BASIC' [TOOLBAR_BASIC] and 'FULL' [TOOLBAR_FULL] for predefined set of buttons.
     * For other options see README and https://quilljs.com/docs/modules/toolbar/
     * @since 2.0.0
     */
    public $toolbarOptions = true;

    /**
     * @var array Icon array
     * You can use this option to set custom icons for the buttons i.e.:
     * ['bold' => '<i class="fa fa-bold" aria-hidden="true"></i>']
     * will overwrite default icon for "bold" button.
     * @see https://github.com/quilljs/quill/issues/1099
     * @since 3.0.0
     */
    public $icons = [];

    /**
     * @var string Placeholder text to be displayed in the editor field.
     * Leave empty for default value.
     * This property is skipped if $configuration is set.
     * @since 2.0.0
     */
    public $placeholder;

    /**
     * @var string DOM Element that editor ui elements, such as tooltips, should be confined within.
     * It will be automatically wrapped in JsExpression.
     * Leave empty for default value.
     * This property is skipped if $configuration is set.
     * @since 2.0.0
     */
    public $bounds;

    /**
     * @var string Static method enabling logging messages at a given level: 'error', 'warn', 'log', or 'info'.
     * Leave empty for default value (off).
     * This property is skipped if $configuration is set.
     * @since 2.0.0
     */
    public $debug;

    /**
     * @var array Whitelist of formats to be allowed in the editor.
     * Leave empty for default list (all allowed).
     * This property is skipped if $configuration is set.
     * @since 2.0.0
     */
    public $formats;

    /**
     * @var array Collection of modules to include and respective options.
     * This property is skipped if $configuration is set.
     * Notice: if you set 'toolbar' module it will replace $toolbarOptions configuration.
     * @since 2.0.0
     */
    public $modules;

    /**
     * @var bool Whether to instantiate the editor in read-only mode.
     * Leave empty for default value (false).
     * This property is skipped if $configuration is set.
     * @since 2.0.0
     */
    public $readOnly;

    /**
     * @var string Additional JS code to be called with the editor.
     * Use placeholder '{quill}' to get the current editor object variable's name.
     * @since 1.1.0
     */
    public $js;

    /**
     * @var string Quill version to fetch from https://cdn.quilljs.com
     * Version different from default for this release might not work correctly.
     * This property is skipped if $localAssets is set to true (Quill version is as set by composer then).
     * @since 2.0.0
     */
    public $quillVersion = self::QUILL_VERSION;

    /**
     * @var array Quill options.
     * Set this to override all other parameters and configure Quill manually.
     * See https://quilljs.com/docs/configuration/ for details.
     * @since 2.0.0
     */
    public $configuration;

    /**
     * @var boolean Set true to enable smart line breaks in quill (SHIFT + Enter new lines).
     * The `npm-asset/quill-smart-break` package is needed to make this work.
     * @since 3.1.0
     */
    public $smartBreak = false;

    /**
     * @var string KaTeX version to fetch from https://cdn.jsdelivr.net
     * Used when Formula module is added.
     * This property is skipped if $localAssets is set to true (KaTeX version is as set by composer then).
     * @since 2.0.0
     */
    public $katexVersion = self::KATEX_VERSION;

    /**
     * @var string Highlight.js version to fetch from https://cdn.jsdelivr.net
     * Used when Syntax module is added.
     * This property is skipped if $localAssets is set to true (Highlight.js version is as set by composer then).
     * @since 2.0.0
     */
    public $highlightVersion = self::HIGHLIGHTJS_VERSION;

    /**
     * @var string Highlight.js stylesheet to fetch from https://cdn.jsdelivr.net
     * See https://github.com/isagalaev/highlight.js/tree/master/src/styles
     * Used when Syntax module is added.
     * Provide just the name of stylesheet (skip ".css" or ".min.css").
     * Since 3.0.0 this property works also for $localAssets set to true.
     * @since 2.0.0
     */
    public $highlightStyle = 'default';

    /**
     * @var array HTML attributes for the input tag (editor box).
     * @see Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $options = ['style' => 'min-height:150px;'];

    /**
     * @var array HTML attributes for the hidden input tag (field keeping raw HTML text).
     * @see Html::renderTagAttributes() for details on how attributes are being rendered.
     * @since 2.5.0
     */
    public $hiddenOptions = [];

    /**
     * @var string HTML tag for the editor.
     * @since 2.0.0
     */
    public $tag = 'div';

    /**
     * @var bool Whether to use local versions of assets instead of CDNs.
     * @since 2.6.0
     */
    public $localAssets = false;

    /** @var string ID of the editor */
    protected $_fieldId;

    /**
     * {@inheritdoc}
     * @throws InvalidConfigException
     */
    public function init(): void
    {
        if (empty($this->quillVersion) || !is_string($this->quillVersion)) {
            throw new InvalidConfigException('The "quillVersion" property must be a non-empty string!');
        }

        if (!empty($this->configuration) && !is_array($this->configuration)) {
            throw new InvalidConfigException('The "configuration" property must be an array!');
        }

        if (!empty($this->js) && !is_string($this->js)) {
            throw new InvalidConfigException('The "js" property must be a string!');
        }

        if (!empty($this->formats) && !is_array($this->formats)) {
            throw new InvalidConfigException('The "formats" property must be an array!');
        }

        if (!empty($this->modules) && !is_array($this->modules)) {
            throw new InvalidConfigException('The "modules" property must be an array!');
        }

        if (!empty($this->icons) && (!is_array($this->icons) || !ArrayHelper::isAssociative($this->icons))) {
            throw new InvalidConfigException('The "icons" property must be an associative array!');
        }

        parent::init();

        $this->_fieldId = $this->options['id'];
        $this->options['id'] = 'editor-' . $this->id;

        $this->prepareOptions();
    }

    /**
     * @var array
     * @since 2.0.0
     */
    protected $_quillConfiguration = [];

    /**
     * Returns Quill configuration.
     * @return array
     * @since 3.0.0
     */
    public function getConfig(): array
    {
        return $this->_quillConfiguration;
    }

    /**
     * Adds new value to Quill configuration.
     * @param string $name
     * @param mixed $value
     * @since 3.0.0
     */
    public function addConfig(string $name, $value): void
    {
        $this->_quillConfiguration[$name] = $value;
    }

    /**
     * Adds new module to Quill configuration.
     * @param string $name
     * @param mixed $config
     * @since 3.0.0
     */
    public function addModule(string $name, $config): void
    {
        if (!array_key_exists('modules', $this->_quillConfiguration)) {
            $this->_quillConfiguration['modules'] = [];
        }

        $this->_quillConfiguration['modules'][$name] = $config;

        if ($name === 'formula') {
            $this->setKatex(true);
        }

        if ($name === 'syntax') {
            $this->setHighlightJs(true);
        }

        if ($name === 'smart-breaker') {
            $this->setSmartBreak(true);
        }
    }

    /**
     * Sets Quill configuration.
     * @param array $config
     * @since 3.0.0
     */
    public function setConfig(array $config): void
    {
        $this->_quillConfiguration = $config;
    }

    /** @var bool */
    private $_smartBreak = false;

    /**
     * Checks whether the Smart break needs to be added.
     * @return bool
     * @since 3.1.0
     */
    public function isSmartBreak(): bool
    {
        return $this->_smartBreak;
    }

    /**
     * Sets Smart break flag.
     * @param bool $smartBreak
     * @since 3.1.0
     */
    public function setSmartBreak(bool $smartBreak): void
    {
        $this->_smartBreak = $smartBreak;
    }

    /** @var bool */
    private $_katex = false;

    /**
     * Checks whether the Katex needs to be added.
     * @return bool
     * @since 3.0.0
     */
    public function isKatex(): bool
    {
        return $this->_katex;
    }

    /**
     * Sets Katex flag.
     * @param bool $katex
     * @since 3.0.0
     */
    public function setKatex(bool $katex): void
    {
        $this->_katex = $katex;
    }

    /** @var bool */
    private $_highlightJs = false;

    /**
     * Checks whether the Highlight.js needs to be added.
     * @return bool
     * @since 3.0.0
     */
    public function isHighlightJs(): bool
    {
        return $this->_highlightJs;
    }

    /**
     * Sets Highlight.js flag.
     * @param bool $highlightJs
     * @since 3.0.0
     */
    public function setHighlightJs(bool $highlightJs): void
    {
        $this->_highlightJs = $highlightJs;
    }

    /** Prepares Quill configuration */
    protected function prepareOptions(): void
    {
        if (!empty($this->configuration)) {
            if (isset($this->configuration['theme'])) {
                $this->theme = $this->configuration['theme'];
            }

            if (isset($this->configuration['modules']['formula'])) {
                $this->setKatex(true);
            }

            if (isset($this->configuration['modules']['syntax'])) {
                $this->setHighlightJs(true);
            }

            $this->setConfig($this->configuration);
        } else {
            if (!empty($this->theme)) {
                $this->addConfig('theme', $this->theme);
            }

            if (!empty($this->bounds)) {
                $this->addConfig('bounds', new JsExpression($this->bounds));
            }

            if (!empty($this->debug)) {
                $this->addConfig('debug', $this->debug);
            }

            if (!empty($this->placeholder)) {
                $this->addConfig('placeholder', $this->placeholder);
            }

            if (!empty($this->formats)) {
                $this->addConfig('formats', $this->formats);
            }

            if ($this->readOnly !== null && (bool)$this->readOnly) {
                $this->addConfig('readOnly', true);
            }

            if ($this->smartBreak !== null && (bool)$this->smartBreak) {
                $this->modules['smart-breaker'] = true;
            }

            if (!empty($this->modules)) {
                foreach ($this->modules as $module => $config) {
                    $this->addModule($module, $config);
                }
            }

            if (!empty($this->toolbarOptions)) {
                $this->addModule('toolbar', $this->renderToolbar());
            }
        }
    }

    /**
     * Prepares predefined set of buttons.
     * @return bool|array|string
     */
    public function renderToolbar()
    {
        if ($this->toolbarOptions === self::TOOLBAR_BASIC) {
            return [
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
                [
                    'link',
                ],
            ];
        }

        if ($this->toolbarOptions === self::TOOLBAR_FULL) {
            return [
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
                [
                    'clean',
                ],
            ];
        }

        return $this->toolbarOptions;
    }

    /** {@inheritdoc} */
    public function run(): string
    {
        $this->registerClientScript();

        $hiddenOptions = array_merge($this->hiddenOptions, ['id' => $this->_fieldId]);

        if ($this->hasModel()) {
            return Html::activeHiddenInput($this->model, $this->attribute, $hiddenOptions)
                . Html::tag($this->tag, $this->model->{$this->attribute}, $this->options);
        }

        return Html::hiddenInput($this->name, $this->value, $hiddenOptions)
            . Html::tag($this->tag, $this->value, $this->options);
    }

    /**
     * Registers widget assets.
     * Note that Quill works without jQuery.
     */
    public function registerClientScript(): void
    {
        $view = $this->view;

        if ($this->isKatex()) {
            if ($this->localAssets) {
                KatexLocalAsset::register($view);
            } else {
                $katexAsset = KatexAsset::register($view);
                $katexAsset->version = $this->katexVersion;
            }
        }

        if ($this->isHighlightJs()) {
            if ($this->localAssets) {
                $highlightAsset = HighlightLocalAsset::register($view);
                $highlightAsset->style = $this->highlightStyle;
            } else {
                $highlightAsset = HighlightAsset::register($view);
                $highlightAsset->version = $this->highlightVersion;
                $highlightAsset->style = $this->highlightStyle;
            }
        }

        if ($this->localAssets) {
            $asset = QuillLocalAsset::register($view);
        } else {
            $asset = QuillAsset::register($view);
            $asset->version = $this->quillVersion;
        }
        $asset->theme = $this->theme;

        if ($this->isSmartBreak() && $this->localAssets) {
            SmartBreakLocalAsset::register($view);
        }

        $configs = Json::encode($this->getConfig());
        $editor = 'q_' . Inflector::slug($this->id, '_');

        $js = '';
        if (!empty($this->icons)) {
            $js .= "var {$editor}_icons=Quill.import('ui/icons');";
            foreach ($this->icons as $key => $icon) {
                $icon = Json::encode($icon);
                $key = Inflector::slug($key);
                $js .= "{$editor}_icons['$key']=$icon;";
            }
        }
        $js .= "var $editor=new Quill(\"#editor-{$this->id}\",$configs);";
        $js .= "$editor.on('text-change',function()";
        $js .= "{document.getElementById(\"{$this->_fieldId}\").value=$editor.root.innerHTML;});";

        if (!empty($this->js)) {
            $js .= str_replace('{quill}', $editor, $this->js);
        }

        $view->registerJs($js, View::POS_END);
    }
}
