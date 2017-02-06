<?php

namespace bizley\quill;

use yii\base\InvalidConfigException;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\JsExpression;
use yii\web\View;
use yii\widgets\InputWidget;

/**
 * Quill 1.2 editor implementation for Yii 2.
 * 
 * Use it as an active field:
 * <?= $form->field($model, $attribute)->widget(\bizley\quill\Quill::className(), []) ?>
 * or as a standalone widget:
 * <?= \bizley\quill\Quill::widget(['name' => 'editor']) ?>
 * 
 * See the documentation for more details.
 * 
 * @author Paweł Bizley Brzozowski
 * @version 2.2
 * @license Apache 2.0
 * https://github.com/bizley/yii2-quill
 * 
 * Quill can be found at
 * https://quilljs.com/
 * https://github.com/quilljs/quill/
 * 
 * For previous Quill beta version install bizley/quill ^1.0.
 */
class Quill extends InputWidget
{
    const THEME_SNOW = 'snow';
    const THEME_BUBBLE = 'bubble';
    
    /**
     * @var string Theme to be set.
     * See https://quilljs.com/docs/themes/ for more info.
     * Set it to 'snow' [Quill::THEME_SNOW] to get snow theme.
     * Set it to 'bubble' [Quill::THEME_BUBBLE] to get bubble theme.
     * Set it to false or null to remove theme.
     * This property is skipped if $configuration is set.
     */
    public $theme = self::THEME_SNOW;
    
    const TOOLBAR_FULL = 'FULL';
    const TOOLBAR_BASIC = 'BASIC';
    
    /**
     * @var bool|string|array Toolbar buttons.
     * Set true to get theme default buttons.
     * You can use above constants for predefined set of buttons.
     * For other options see README and https://quilljs.com/docs/modules/toolbar/
     * @since 2.0
     */
    public $toolbarOptions = true;
    
    /**
     * @var string Placeholder text to be displayed in the editor field.
     * Leave empty for default value.
     * This property is skipped if $configuration is set.
     * @since 2.0
     */
    public $placeholder;
    
    /**
     * @var string DOM Element that editor ui elements, such as tooltips, should be confined within.
     * It will be automatically wrapped in JsExpression.
     * Leave empty for default value.
     * This property is skipped if $configuration is set.
     * @since 2.0
     */
    public $bounds;
    
    /**
     * @var string Static method enabling logging messages at a given level: 'error', 'warn', 'log', or 'info'.
     * Leave empty for default value (false).
     * This property is skipped if $configuration is set.
     * @since 2.0
     */
    public $debug;
    
    /**
     * @var array Whitelist of formats to allow in the editor.
     * Leave empty for default list (all allowed).
     * This property is skipped if $configuration is set.
     * @since 2.0
     */
    public $formats;
    
    /**
     * @var array Collection of modules to include and respective options.
     * This property is skipped if $configuration is set.
     * Notice: if you set 'toolbar' module it will replace $toolbarOptions configuration.
     * @since 2.0
     */
    public $modules;
    
    /**
     * @var bool Whether to instantiate the editor in read-only mode.
     * Leave empty for default value (false).
     * This property is skipped if $configuration is set.
     * @since 2.0
     */
    public $readOnly;
    
    /**
     * @var string Additional JS code to be called with the editor.
     * Use placeholder {quill} to get the current editor object variable's name.
     * @since 1.1
     */
    public $js;
    
    /**
     * @var string Quill version to fetch from https://cdn.quilljs.com
     * Version different from default for this release might not work correctly.
     * @since 2.0
     */
    public $quillVersion = '1.2.0';
    
    /**
     * @var array Quill options.
     * Set this to override all other parameters and configure Quill manually.
     * See https://quilljs.com/docs/configuration/ for details.
     * @since 2.0
     */
    public $configuration;
    
    /**
     * @var string KaTeX version to fetch from https://cdnjs.cloudflare.com
     * Used when Formula module is added.
     * @since 2.0
     */
    public $katexVersion = '0.7.1';
    
    /**
     * @var string Highlight.js version to fetch from https://cdnjs.cloudflare.com
     * Used when Syntax module is added.
     * @since 2.0
     */
    public $highlightVersion = '9.9.0';
    
    /**
     * @var string Highlight.js stylesheet to fetch from https://cdnjs.cloudflare.com
     * See https://github.com/isagalaev/highlight.js/tree/master/src/styles
     * Used when Syntax module is added.
     * @since 2.0
     */
    public $highlightStyle = 'default.min.css';
    
    /**
     * @var array HTML attributes for the input tag.
     * @see Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $options = ['style' => 'min-height:150px;'];
    
    /**
     * @var string HTML tag for the editor.
     * @since 2.0
     */
    public $tag = 'div';

    /**
     * @inheritdoc
     */
    public static $autoIdPrefix = 'quill-';
    
    /**
     * @var string ID of the editor.
     */
    protected $_fieldId;
    
    /**
     * @var array
     * @since 2.0
     */
    protected $_quillConfiguration = [];

    /**
     * @inheritdoc
     */
    public function init()
    {
        if (empty($this->quillVersion) && !is_string($this->quillVersion)) {
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
        
        parent::init();
        
        $this->_fieldId = $this->options['id'];
        $this->options['id'] = 'editor-' . $this->id;
        
        $this->prepareOptions();
    }
    
    private $_katex = false;
    private $_highlight = false;
    
    /**
     * Prepares Quill configuration.
     */
    protected function prepareOptions()
    {
        if (!empty($this->configuration)) {
            if (isset($this->configuration['theme'])) {
                $this->theme = $this->configuration['theme'];
            }
            if (isset($this->configuration['modules']['formula'])) {
                $this->_katex = true;
            }
            if (isset($this->configuration['modules']['syntax'])) {
                $this->_highlight = true;
            }
            $this->_quillConfiguration = $this->configuration;
        } else {
            if (!empty($this->theme)) {
                $this->_quillConfiguration['theme'] = $this->theme;
            }
            if (!empty($this->bounds)) {
                $this->_quillConfiguration['bounds'] = new JsExpression($this->bounds);
            }
            if (!empty($this->debug)) {
                $this->_quillConfiguration['debug'] = $this->debug;
            }
            if (!empty($this->placeholder)) {
                $this->_quillConfiguration['placeholder'] = $this->placeholder;
            }
            if (!empty($this->formats)) {
                $this->_quillConfiguration['formates'] = $this->formats;
            }
            
            if (!empty($this->modules)) {
                foreach ($this->modules as $module => $config) {
                    $this->_quillConfiguration['modules'][$module] = $config;
                    if ($module == 'formula') {
                        $this->_katex = true;
                    }
                    if ($module == 'syntax') {
                        $this->_highlight = true;
                    }
                }
            }
            if (!empty($this->toolbarOptions)) {
                $this->_quillConfiguration['modules']['toolbar'] = $this->renderToolbar();
            }
        }
    }
    
    /**
     * @inheritdoc
     */
    public function run()
    {
        $this->registerClientScript();
        if ($this->hasModel()) {
            return Html::activeHiddenInput(
                $this->model, $this->attribute, ['id' => $this->_fieldId]
            ) . Html::tag(
                $this->tag, $this->model->{$this->attribute}, $this->options
            );
        }
        return Html::hiddenInput(
            $this->name, $this->value, ['id' => $this->_fieldId]
        ) . Html::tag(
            $this->tag, $this->value, $this->options
        );
    }
    
    /**
     * Registers widget assets.
     * Note that Quill works without jQuery.
     */
    public function registerClientScript()
    {
        $view = $this->view;
        
        if ($this->_katex) {
            $katexAsset = KatexAsset::register($view);
            $katexAsset->version = $this->katexVersion;
        }
        if ($this->_highlight) {
            $highlightAsset = HighlightAsset::register($view);
            $highlightAsset->version = $this->highlightVersion;
            $highlightAsset->style = $this->highlightStyle;
        }
        
        $asset = QuillAsset::register($view);
        $asset->theme = $this->theme;
        $asset->version = $this->quillVersion;
        
        $configs = Json::encode($this->_quillConfiguration);
        $editor = 'q_' . preg_replace('~[^0-9_\p{L}]~u', '_', $this->id);
        
        $js = "var $editor=new Quill(\"#editor-{$this->id}\",$configs);";
        $js .= "document.getElementById(\"editor-{$this->id}\").onclick=function(e){document.querySelector(\"#editor-{$this->id} .ql-editor\").focus();};";
        $js .= "$editor.on('text-change',function(){document.getElementById(\"{$this->_fieldId}\").value=$editor.root.innerHTML;});";
        if (!empty($this->js)) {
            $js .= str_replace('{quill}', $editor, $this->js);
        }
        $view->registerJs($js, View::POS_END);
    }
    
    /**
     * Prepares predefined set of buttons.
     * @return boolean|array
     */
    public function renderToolbar()
    {
        if ($this->toolbarOptions == self::TOOLBAR_BASIC) {
            return [
                ['bold', 'italic', 'underline', 'strike'], 
                [['list' => 'ordered'], ['list' => 'bullet']], 
                [['align' => []]], 
                ['link']
            ];
        }
        if ($this->toolbarOptions == self::TOOLBAR_FULL) {
            return [
                [['font' => []], ['size' => ['small', false, 'large', 'huge']]],
                ['bold', 'italic', 'underline', 'strike'],
                [['color' => []], ['background' => []]],
                [['script' => 'sub'], ['script' => 'super']],
                [['header' => 1], ['header' => 2], 'blockquote', 'code-block'],
                [['list' => 'ordered'], ['list' => 'bullet'], ['indent' => '-1'], ['indent' => '+1']],
                [['direction' => 'rtl'], ['align' => []]],
                ['link', 'image', 'video'],
                ['clean']
            ];
        }
        return $this->toolbarOptions;
    }
}
