<?php

namespace bizley\quill;

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\helpers\Html;

/**
 * Quill Toolbar helper class.
 * 
 * @author Paweł Bizley Brzozowski
 * @version 1.2.0.1
 * @license Apache 2.0
 * https://github.com/bizley/yii2-quill
 * 
 * Quill can be found at
 * http://quilljs.com/
 * https://github.com/quilljs/quill/
 * 
 * @since 1.2.0
 * @property array $colors
 * @property array $elements
 * @property array $modules
 */
class QuillToolbar extends Component
{
    /**
     * @var array toolbar's elements.
     */
    protected $_elements = [];
    
    /**
     * @var array modules required by buttons.
     */
    protected $_modules = [];
    
    /**
     * Creates new Quill toolbar.
     * @param string|aray|null $configuration
     * @param array $componentConfig name-value pairs that will be used to initialize the object properties
     * @throws InvalidConfigException
     */
    public function __construct($configuration, $componentConfig = [])
    {
        if (!empty($configuration)) {
            if (!is_string($configuration) && !is_array($configuration)) {
                throw new InvalidConfigException('Toolbar configuration must be a string or an array!');
            }
            if (is_string($configuration)) {
                switch ($configuration) {
                    case Quill::TOOLBAR_FULL:
                        $this->prepareFullToolbar();
                        break;
                    case Quill::TOOLBAR_BASIC:
                        $this->prepareBasicToolbar();
                        break;
                    default:
                        $this->_elements = (array)$configuration;
                }
            }
            if (is_array($configuration)) {
                $this->_elements = $configuration;
            }
        }
        parent::__construct($componentConfig);
    }
    
    /**
     * Adds module dependency.
     * @param string $module
     */
    public function addModule($module)
    {
        $this->_modules[] = $module;
    }
    
    /**
     * Returns list of colors.
     * @return array
     */
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
    
    /**
     * Returns elements.
     * @return array
     */
    public function getElements()
    {
        return $this->_elements;
    }
    
    /**
     * Returns required modules.
     * @return array
     */
    public function getModules()
    {
        return $this->_modules;
    }
    
    /**
     * Prepares full toolbar.
     */
    public function prepareFullToolbar()
    {
        $this->_modules = [];
        $this->_elements = [
            ['font', 'size'],
            ['b', '|', 'i', '|', 'u', '|', 's'],
            ['textcolor', '|', 'backcolor'],
            ['ol', '|', 'ul', '|', 'alignment'],
            ['link', '|', 'image']
        ];
    }
    
    /**
     * Prepares basic toolbar.
     */
    public function prepareBasicToolbar()
    {
        $this->_modules = [];
        $this->_elements = [
            ['b', '|', 'i', '|', 'u', '|', 's'],
            ['ol', '|', 'ul', '|', 'alignment'],
            ['link']
        ];
    }
    
    /**
     * Renders toolbar.
     * @param string toolbar's ID
     * @return string
     */
    public function render($id)
    {
        $toolbar = Html::beginTag('div', ['id' => $id, 'class' => 'toolbar']);
        foreach ($this->_elements as $element) {
            if (is_array($element)) {
                $toolbar .= $this->renderGroup($element);
            } else {
                $toolbar .= $this->renderButton($element);
            }
        }
        $toolbar .= Html::endTag('div');
        
        return $toolbar;
    }
    
    /**
     * Renders alignment list.
     * @return string
     */
    public function renderAlignment()
    {
        return $this->renderDropdown('left', [
                'left'    => '',
                'center'  => '',
                'right'   => '',
                'justify' => '',
            ], 
            [
                'title' => Yii::t('app', 'Text Alignment'), 
                'class' => 'ql-align'
            ]
        );
    }
    
    /**
     * Renders background color list.
     * @return string
     */
    public function renderBackColor()
    {
        return $this->renderDropdown('rgb(0, 0, 0)', $this->colors, 
            [
                'title' => Yii::t('app', 'Background Color'), 
                'class' => 'ql-background'
            ]
        );
    }
    
    /**
     * Renders bold button.
     * @return string
     */
    public function renderBold()
    {
        return $this->renderSpan([
            'title' => Yii::t('app', 'Bold'), 
            'class' => 'ql-format-button ql-bold']
        );
    }
    
    /**
     * Renders bullet list button.
     * @return string
     */
    public function renderBulletList()
    {
        return $this->renderSpan([
            'title' => Yii::t('app', 'Bullet'), 
            'class' => 'ql-format-button ql-bullet'
        ]);
    }
    
    /**
     * Renders button.
     * Required modules are automatically added.
     * @param string $element button definition.
     * @return string
     */
    public function renderButton($element)
    {
        if (!is_string($element)) {
            return null;
        }
        switch (strtolower($element)) {
            case '|':
            case ' ':
            case '-':
            case '.':
            case ',':
                return $this->renderSeparator();
            case 'b':
            case 'bold':
                return $this->renderBold();
            case 'i':
            case 'italic':
                return $this->renderItalic();
            case 'u':
            case 'underline':
                return $this->renderUnderline();
            case 's':
            case 'strike':
            case 'strikethrough':
                return $this->renderStrikethrough();
            case 'font':
            case 'fontface':
            case 'font-face':
            case 'fontfamily':
            case 'font-family':
                return $this->renderFont();
            case 'size':
            case 'fontsize':
            case 'font-size':
                return $this->renderSize();
            case 'textcolor':
            case 'text-color':
                return $this->renderTextColor();
            case 'backcolor':
            case 'back-color':
                return $this->renderBackColor();
            case 'ol':
            case 'list':
            case 'orderedlist':
            case 'ordered-list':
                return $this->renderOrderedList();
            case 'ul':
            case 'bullet':
            case 'unorderedlist':
            case 'unordered-list':
                return $this->renderBulletList();
            case 'alignment':
                return $this->renderAlignment();
            case 'link':
                return $this->renderLink();
            case 'image':
                return $this->renderImage();
            default:
                return $this->renderCustom($element);
        }
    }
    
    /**
     * Renders custom element.
     * @param string $element
     * @return string
     */
    public function renderCustom($element)
    {
        return $element;
    }
    
    /**
     * Renders dropdown element.
     * @param string $selected
     * @param array $list
     * @param array $options
     * @return string
     */
    public function renderDropdown($selected, $list, $options)
    {
        return Html::dropDownList('', $selected, $list, $options);
    }
    
    /**
     * Renders font list.
     * @return string
     */
    public function renderFont()
    {
        return $this->renderDropdown('sans-serif', [
                'sans-serif' => 'Sans Serif',
                'serif'      => 'Serif',
                'monospace'  => 'Monospace',
            ], 
            [
                'title' => Yii::t('app', 'Font'), 
                'class' => 'ql-font'
            ]
        );
    }
    
    /**
     * Renders buttons group.
     * @param array $elements
     * @return string
     */
    public function renderGroup($elements)
    {
        $html = Html::beginTag('span', ['class' => 'ql-format-group']);
        foreach ($elements as $element) {
            $html .= $this->renderButton($element);
        }
        $html .= Html::endTag('span');
        return $html;
    }
    
    /**
     * Renders image button.
     * @return string
     */
    public function renderImage()
    {
        $this->addModule('image-tooltip');
        return $this->renderSpan([
            'title' => Yii::t('app', 'Image'), 
            'class' => 'ql-format-button ql-image'
        ]);
    }
    
    /**
     * Renders italic button.
     * @return string
     */
    public function renderItalic()
    {
        return $this->renderSpan([
            'title' => Yii::t('app', 'Italic'), 
            'class' => 'ql-format-button ql-italic'
        ]);
    }
    
    /**
     * Renders link button.
     * @return string
     */
    public function renderLink()
    {
        $this->addModule('link-tooltip');
        return $this->renderSpan([
            'title' => Yii::t('app', 'Link'), 
            'class' => 'ql-format-button ql-link'
        ]);
    }
    
    /**
     * Renders ordered list button.
     * @return string
     */
    public function renderOrderedList()
    {
        return $this->renderSpan([
            'title' => Yii::t('app', 'List'), 
            'class' => 'ql-format-button ql-list'
        ]);
    }
    
    /**
     * Renders separator button.
     * @return string
     */
    public function renderSeparator()
    {
        return $this->renderSpan(['class' => 'ql-format-separator']);
    }
    
    /**
     * Renders size list.
     * @return string
     */
    public function renderSize()
    {
        return $this->renderDropdown('13px', [
                '10px' => Yii::t('app', 'Small'),
                '13px' => Yii::t('app', 'Normal'),
                '18px' => Yii::t('app', 'Large'),
                '32px' => Yii::t('app', 'Huge'),
            ], 
            [
                'title' => Yii::t('app', 'Size'), 
                'class' => 'ql-size'
            ]
        );
    }
    
    /**
     * Renders span element.
     * @param array $options
     * @return string
     */
    public function renderSpan($options)
    {
        return Html::tag('span', '', $options);
    }
    
    /**
     * Renders strikethrough button.
     * @return string
     */
    public function renderStrikethrough()
    {
        return $this->renderSpan([
            'title' => Yii::t('app', 'Strikethrough'), 
            'class' => 'ql-format-button ql-strike'
        ]);
    }
    
    /**
     * Renders text color list.
     * @return string
     */
    public function renderTextColor()
    {
        return $this->renderDropdown('rgb(0, 0, 0)', $this->colors, 
            [
                'title' => Yii::t('app', 'Text Color'), 
                'class' => 'ql-color'
            ]
        );
    }
    
    /**
     * Renders underline button.
     * @return string
     */
    public function renderUnderline()
    {
        return $this->renderSpan([
            'title' => Yii::t('app', 'Underline'), 
            'class' => 'ql-format-button ql-underline'
        ]);
    }
}
