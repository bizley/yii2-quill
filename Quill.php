<?php

namespace bizley\quill;

use yii\base\InvalidConfigException;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\InputWidget;

/**
 * Quill editor implementation for Yii 2.
 * 
 * Use it as an active field:
 * <?= $form->field($model, $attribute)->widget(bizley\quill\Quill::className(), []) ?>
 * or as a standalone widget:
 * <?= bizley\quill\Quill::widget(['name' => 'editor']) ?>
 * 
 * Default parameters are:
 * 'theme' => 'bootstrap' adds Bootstrap theme
 * 'toolbar' => 'full' adds full toolbar
 * 
 * See the documentation for more details.
 * 
 * @author Paweł Bizley Brzozowski
 * @version 1.2.0
 * @license Apache 2.0
 * https://github.com/bizley/yii2-quill
 * 
 * Quill can be found at
 * http://quilljs.com/
 * https://github.com/quilljs/quill/
 * 
 * @property QuillToolbar $quillToolbar
 */
class Quill extends InputWidget
{
    const THEME_BOOT = 'bootstrap';
    const THEME_SNOW = 'snow';
    const THEME_BASE = 'base';
    
    /**
     * @var string theme name.
     * See http://quilljs.com/docs/themes for more info.
     * You can set this parameter here or use 'configs' array.
     * Set it to 'snow' to get 'snow' theme.
     * Set it to false or null to get 'base' theme.
     * For special Bootstrap theme (not a part of Quill itself) set it to 
     * 'bootstrap'.
     */
    public $theme = self::THEME_BOOT;
    
    const TOOLBAR_FULL = 'full';
    const TOOLBAR_BASIC = 'basic';
    
    /**
     * @var string|array toolbar configuration.
     * You can set it to 'full' to get full default toolbar as at the home page 
     * of http://quilljs.com or set it to 'basic' to get only few buttons.
     * In this is an array every array element should be a button or 
     * group of buttons definition.
     * See the documentation for more details.
     */
    public $toolbar = self::TOOLBAR_FULL;
    
    /**
     * @var array Quill configuration as in http://quilljs.com/docs/configuration
     */
    public $configs = [];
    
    /**
     * @var string additional js to be called with the editor.
     * Use placeholder {quill} to get the current editor object variable.
     * @since 1.1
     */
    public $js;
    
    /**
     * @inheritdoc
     */
    public static $autoIdPrefix = 'quill-';
    
    /**
     * @var string selected css mode.
     */
    protected $_css;
    
    /**
     * @var string ID of the editor.
     */
    protected $_fieldId;
    
    /**
     * @var QuillToolbar
     */
    protected $_quillToolbar;

    /**
     * Ensures the required module is added in configs.
     * @param string $name
     * @since 1.2.0
     */
    public function addModule($name)
    {
        if (!isset($this->configs['modules'])) {
            $this->configs['modules'] = [];
        }
        if (!isset($this->configs['modules'][$name])) {
            $this->configs['modules'][$name] = true;
        }
    }
    
    /**
     * Adds modules dependency.
     */
    public function addModules()
    {
        if ($this->quillToolbar) {
            foreach ($this->quillToolbar->getModules() as $module) {
                $this->addModule($module);
            }
        }
    }
    
    /**
     * @inheritdoc
     */
    public function init()
    {
        if (!is_array($this->configs)) {
            throw new InvalidConfigException('The "configs" property must be an array!');
        }
        if (!empty($this->js) && !is_string($this->js)) {
            throw new InvalidConfigException('The "js" property must be a string!');
        }
        parent::init();
        $this->initTheme();
        $this->setQuillToolbar($this->toolbar);
        $this->initOptions();
    }
    
    /**
     * Initiates theme option.
     * @throws InvalidConfigException
     */
    protected function initTheme()
    {
        $this->_css = self::THEME_BASE;
        if (!empty($this->theme)) {
            if (!is_string($this->theme)) {
                throw new InvalidConfigException('The "theme" property must be a string!');
            }
            $this->configs['theme'] = $this->theme;
            if ($this->theme == self::THEME_BOOT) {
                $this->configs['theme'] = self::THEME_SNOW;
            }
            if (in_array($this->theme, [self::THEME_SNOW, self::THEME_BOOT])) {
                $this->_css = self::THEME_SNOW;
            }
        }
    }
    
    /**
     * Initiates widget HTML options.
     */
    protected function initOptions()
    {
        $classes = [];
        if (!empty($this->options['class'])) {
            $classes = explode(' ', $this->options['class']);
        }
        $classes[] = 'editor';
        $this->options['class'] = implode(' ', array_unique($classes));
        $this->_fieldId = $this->options['id'];
        $this->options['id'] = 'editor-' . $this->id;
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        $toolbar = $this->renderToolbar();
        $this->addModules();
        $this->registerClientScript();
        return $this->renderEditor($toolbar);
    }
    
    /**
     * Registers widget assets.
     */
    public function registerClientScript()
    {
        $view = $this->getView();
        $asset = Asset::register($view);
        $asset->theme = $this->_css;
        
        $configs = !empty($this->configs) ? Json::encode($this->configs) : '';
        $var = 'q_' . preg_replace('~[^0-9_\p{L}]~u', '_', $this->id);
        $js = "var $var = new Quill('#editor-{$this->id}', $configs);";
        $js .= "$var.on('text-change', function() { jQuery('#{$this->_fieldId}').val(this.getHTML()); });";
        if (!empty($this->js)) {
            $js .= str_replace('{quill}', $var, $this->js);
        }
        $view->registerJs($js);
    }
    
    /**
     * Renders editor.
     * @property string $toolbar
     * @return string
     * @since 1.2.0
     */
    public function renderEditor($toolbar)
    {
        $bootstrap = $this->theme == self::THEME_BOOT;
        $editor = '';
        if ($bootstrap) {
            $editor .= Html::beginTag('div', ['class' => 'panel panel-default']);
        }
        if ($this->hasModel()) {
            $editor .= Html::activeHiddenInput($this->model, $this->attribute, ['id' => $this->_fieldId]);
        } else {
            $editor .= Html::hiddenInput($this->name, $this->value, ['id' => $this->_fieldId]);
        }
        if ($bootstrap) {
            $editor .= Html::beginTag('div', ['class' => 'panel-body', 'style' => 'padding:0; border-bottom:1px solid #ccc']);
        }
        $editor .= $toolbar;
        if ($bootstrap) {
            $editor .= Html::endTag('div') . Html::beginTag('div', ['class' => 'panel-body']);
        }
        if ($this->hasModel()) {
            $editor .= Html::tag('div', $this->model->{$this->attribute}, $this->options);
        } else {
            $editor .= Html::tag('div', $this->value, $this->options);
        }        
        if ($bootstrap) {
            $editor .= Html::endTag('div') . Html::endTag('div');
        }
        return $editor;
    }
    
    /**
     * Renders toolbar.
     * @return string
     * @since 1.2.0
     */
    public function renderToolbar()
    {
        if (!empty($this->quillToolbar->getElements())) {
            $toolbarId = 'toolbar-' . $this->id;
            
            if (empty($this->configs['modules'])) {
                $this->configs['modules'] = ['toolbar' => []];
            }
            $this->configs['modules']['toolbar'] = ['container' => '#' . $toolbarId];
            
            return $this->quillToolbar->render($toolbarId);
        }
        return null;
    }
    
    /**
     * Returns Quill toolbar object.
     * @return QuillToolbar
     * @since 1.2.0
     */
    public function getQuillToolbar()
    {
        return $this->_quillToolbar;
    }
    
    /**
     * Sets Quill toolbar object.
     * @param string|array|null $toolbarConfig configuration
     * @since 1.2.0
     */
    public function setQuillToolbar($toolbarConfig)
    {
        $this->_quillToolbar = new QuillToolbar($toolbarConfig);
    }
    
    /**
     * Deprecated since 1.2.0
     * -------------------------------------------------------------------------
     */
    
    /**
     * Adds button to the toolbar.
     * @deprecated 1.2.0 use getQuillToolbar()->renderButton() instead.
     */
    public function addButton($element)
    {
        return $this->quillToolbar->renderButton($element);
    }
    
    /**
     * Adds group of buttons to the toolbar.
     * @deprecated 1.2.0 use getQuillToolbar()->renderGroup() instead.
     */
    public function addGroup($elements)
    {
        return $this->quillToolbar->renderGroup($elements);
    }
    
    /**
     * Adds toolbar based on the toolbar parameter.
     * @deprecated 1.2.0 use renderToolbar() instead.
     */
    public function addToolbar()
    {
        return $this->renderToolbar();
    }
    
    /**
     * Returns default list of colours.
     * @deprecated 1.2.0 use getQuillToolbar()->getColors() instead.
     */
    public function getColors()
    {
        return $this->quillToolbar->getColors();
    }
    
    /**
     * Initiates configs array.
     * @deprecated 1.2.0
     */
    public function initConfigs()
    {
        return;
    }
    
    /**
     * Initiates toolbar option.
     * @deprecated 1.2.0 use setQuillToolbar() instead.
     */
    public function initToolbar()
    {
        $this->setQuillToolbar($this->toolbar);
    }
    
    /**
     * Ensures the required modules are added in configs.
     * @deprecated 1.2.0 use addModule() instead.
     */
    public function makeSureThereIsModule($name)
    {
        return $this->addModule($name);
    }
    
    /**
     * Sets toolbar to full.
     * @deprecated 1.2.0 use setQuillToolbar('full') 
     * or getQuillToolbar()->prepareFullToolbar() instead.
     */
    public function setFullToolbar()
    {
        return $this->quillToolbar->prepareFullToolbar();
    }
    
    /**
     * Sets toolbar to basic.
     * @deprecated 1.2.0 use setQuillToolbar('basic') 
     * or getQuillToolbar()->prepareBasicToolbar() instead.
     */
    public function setBasicToolbar()
    {
        return $this->quillToolbar->prepareBasicToolbar();
    }
}
