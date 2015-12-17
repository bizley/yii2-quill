<?php

namespace bizley\quill;

use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\InputWidget;


class Quill extends InputWidget
{

    public $theme = 'snow';
    
    public $toolbar = 'full';
    
    public $configs = [];
    
    public static $autoIdPrefix = 'quill-';
    
    protected $_css;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->initConfigs();
        $this->initOptions();
    }
    
    public function initConfigs()
    {
        if (!is_array($this->configs)) {
            throw new InvalidConfigException('The "configs" property must be an array!');
        }
        
        $this->initTheme();
        $this->initToolbar();
    }
    
    public function initTheme()
    {
        if (!empty($this->theme)) {
            if (!is_string($this->theme)) {
                throw new InvalidConfigException('The "theme" property must be a string!');
            }
            
            $this->configs['theme'] = $this->theme;
            if ($this->theme == 'snow') {
                $this->_css = 'snow';
            }
        }
        else {
            $this->_css = 'base';
        }
    }
    
    public function initToolbar()
    {
        if (!empty($this->toolbar)) {
            if (!is_string($this->toolbar) && !is_array($this->toolbar)) {
                throw new InvalidConfigException('The "toolbar" property must be a string or an array!');
            }
            
            switch ($this->toolbar) {
                case 'full':
                    $this->setFullToolbar();
                    break;
                case 'basic':
                    $this->setBasicToolbar();
                    break;
            }
        }
    }
    
    public function initOptions()
    {
        if (!is_array($this->options)) {
            $this->options = [];
        }
        if (empty($this->options['class'])) {
            $this->options['class'] = 'editor';
        }
        else {
            $classes = explode(' ', $this->options['class']);
            $classes[] = 'editor';
            $this->options['class'] = implode(' ', array_unique($classes));
        }
        $this->options['id'] = 'editor-' . $this->id;
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        if ($this->hasModel()) {
            $editor = Html::activeHiddenInput($this->model, $this->attribute);
        }
        else {
            $editor = Html::hiddenInput($this->name, $this->value);
        }
        
        $editor .= $this->addToolbar();
        $editor .= Html::tag('div', $this->model->{$this->attribute}, $this->options);
        
        $this->registerClientScript();
        
        return $editor;
    }
    
    /**
     * Register widget asset.
     */
    public function registerClientScript()
    {
        $view = $this->getView();
        $asset = Asset::register($view);
        $asset->theme = $this->_css;
        
        $configs = !empty($this->configs) ? Json::encode($this->configs) : '';
        $view->registerJs("var editor = new Quill('#editor-{$this->id}', $configs);");
    }
    
    public function setFullToolbar()
    {
        $this->toolbar = [
            ['font', 'size'],
            ['b', '|', 'i', '|', 'u', '|', 's'],
            ['textColor', '|', 'backColor'],
            ['ol', '|', 'ul', '|', 'alignment'],
            ['link', '|', 'image']
        ];
    }
    
    public function setBasicToolbar()
    {
        $this->toolbar = [
            ['b', '|', 'i', '|', 'u', '|', 's'],
            ['ol', '|', 'ul', '|', 'alignment'],
            ['link']
        ];
    }
    
    public function addToolbar()
    {
        $toolbar = '';
        
        if (!empty($this->toolbar)) {
            $toolbarId = 'toolbar-' . $this->id;
            
            if (empty($this->configs['modules'])) {
                $this->configs['modules'] = ['toolbar' => []];
            }
            $this->configs['modules']['toolbar'] = ['container' => '#' . $toolbarId];
            
            $toolbar .= Html::beginTag('div', ['id' => $toolbarId, 'class' => 'toolbar']);
            if (is_string($this->toolbar)) {
                $toolbar = $this->toolbar;
            }
            elseif (is_array($this->toolbar)) {
                foreach ($this->toolbar as $bar) {
                    if (is_string($bar)) {
                        $toolbar .= $this->addButton($bar);
                    }
                    elseif (is_array($bar)) {
                        $toolbar .= $this->addGroup($bar);
                    }
                }
            }
            $toolbar .= Html::endTag('div');
        }
        
        return $toolbar;
    }
    
    public function addGroup($group)
    {
        $html = Html::beginTag('span', ['class' => 'ql-format-group']);
        foreach ($group as $button) {
            if (is_string($button)) {
                $html .= $this->addButton($button);
            }
        }
        $html .= Html::endTag('span');
        
        return $html;
    }
    
    public function addButton($button)
    {
        switch ($button) {
            case '|':
                $html = Html::tag('span', '', ['class' => 'ql-format-separator']);
                break;
            case 'b':
                $html = Html::tag('span', '', ['title' => Yii::t('app', 'Bold'), 'class' => 'ql-format-button ql-bold']);
                break;
            case 'i':
                $html = Html::tag('span', '', ['title' => Yii::t('app', 'Italic'), 'class' => 'ql-format-button ql-italic']);
                break;
            case 'u':
                $html = Html::tag('span', '', ['title' => Yii::t('app', 'Underline'), 'class' => 'ql-format-button ql-underline']);
                break;
            case 's':
                $html = Html::tag('span', '', ['title' => Yii::t('app', 'Strikethrough'), 'class' => 'ql-format-button ql-strike']);
                break;
            case 'font':
                $html = Html::dropDownList('', 'sans-serif', [
                                'sans-serif' => 'Sans Serif',
                                'serif'      => 'Serif',
                                'monospace'  => 'Monospace',
                            ], ['title' => Yii::t('app', 'Font'), 'class' => 'ql-font']);
                break;
            case 'size':
                $html = Html::dropDownList('', '13px', [
                                '10px' => Yii::t('app', 'Small'),
                                '13px' => Yii::t('app', 'Normal'),
                                '18px' => Yii::t('app', 'Large'),
                                '32px' => Yii::t('app', 'Huge'),
                            ], ['title' => Yii::t('app', 'Size'), 'class' => 'ql-size']);
                break;
            case 'textColor':
                $html = Html::dropDownList('', 'rgb(0, 0, 0)', $this->getColors(), ['title' => Yii::t('app', 'Text Color'), 'class' => 'ql-color']);
                break;
            case 'backColor':
                $html = Html::dropDownList('', 'rgb(0, 0, 0)', $this->getColors(), ['title' => Yii::t('app', 'Background Color'), 'class' => 'ql-background']);
                break;
            case 'ol':
                $html = Html::tag('span', '', ['title' => Yii::t('app', 'List'), 'class' => 'ql-format-button ql-list']);
                break;
            case 'ul':
                $html = Html::tag('span', '', ['title' => Yii::t('app', 'Bullet'), 'class' => 'ql-format-button ql-bullet']);
                break;
            case 'alignment':
                $html = Html::dropDownList('', 'left', [
                                'left' => '',
                                'center' => '',
                                'right' => '',
                                'justify' => '',
                            ], ['title' => Yii::t('app', 'Text Alignment'), 'class' => 'ql-align']);
                break;
            case 'link':
                $this->makeSureThereIsModule('link-tooltip');
                $html = Html::tag('span', '', ['title' => Yii::t('app', 'Link'), 'class' => 'ql-format-button ql-link']);
                break;
            case 'image':
                $this->makeSureThereIsModule('image-tooltip');
                $html = Html::tag('span', '', ['title' => Yii::t('app', 'Image'), 'class' => 'ql-format-button ql-image']);
                break;
            default:
                $html = $button;
        }
        
        return $html;
    }
    
    public function getColors()
    {
        return [
            'rgb(0, 0, 0)' => '',
            'rgb(230, 0, 0)' => '',
            'rgb(255, 153, 0)' => '',
            'rgb(255, 255, 0)' => '',
            'rgb(0, 138, 0)' => '',
            'rgb(0, 102, 204)' => '',
            'rgb(153, 51, 255)' => '',
            'rgb(255, 255, 255)' => '',
            'rgb(250, 204, 204)' => '',
            'rgb(255, 235, 204)' => '',
            'rgb(255, 255, 204)' => '',
            'rgb(204, 232, 204)' => '',
            'rgb(204, 224, 245)' => '',
            'rgb(235, 214, 255)' => '',
            'rgb(187, 187, 187)' => '',
            'rgb(240, 102, 102)' => '',
            'rgb(255, 194, 102)' => '',
            'rgb(255, 255, 102)' => '',
            'rgb(102, 185, 102)' => '',
            'rgb(102, 163, 224)' => '',
            'rgb(194, 133, 255)' => '',
            'rgb(136, 136, 136)' => '',
            'rgb(161, 0, 0)' => '',
            'rgb(178, 107, 0)' => '',
            'rgb(178, 178, 0)' => '',
            'rgb(0, 97, 0)' => '',
            'rgb(0, 71, 178)' => '',
            'rgb(107, 36, 178)' => '',
            'rgb(68, 68, 68)' => '',
            'rgb(92, 0, 0)' => '',
            'rgb(102, 61, 0)' => '',
            'rgb(102, 102, 0)' => '',
            'rgb(0, 55, 0)' => '',
            'rgb(0, 41, 102)' => '',
            'rgb(61, 20, 102)' => '',
        ];
    }
    
    public function makeSureThereIsModule($name)
    {
        if (isset($this->configs['modules'])) {
            if (!isset($this->configs['modules'][$name])) {
                $this->configs['modules'][$name] = true;
            }
        }
        else {
            $this->configs['modules'] = [];
            $this->configs['modules'][$name] = true;
        }
    }
}
