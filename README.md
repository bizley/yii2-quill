# yii2-quill

![Latest Stable Version](https://img.shields.io/packagist/v/bizley/quill.svg)
![Total Downloads](https://img.shields.io/packagist/dt/bizley/quill.svg)
![License](https://img.shields.io/packagist/l/bizley/quill.svg)

*Yii 2 implementation of Quill, modern WYSIWYG editor.*

## Quill

You can find Quill at https://quilljs.com/  
- [Documentation](https://quilljs.com/docs/quickstart/)
- [Guides](https://quilljs.com/guides/why-quill/)
- [Playground](https://quilljs.com/playground/)
- [GitHub](https://github.com/quilljs/quill)

## yii2-quill

### Installation

Easiest way to install this extension is through the [Composer](https://getcomposer.org).  
Add in your `composer.json`:  
`"bizley/quill": "^2.0"`  
or run console command:  
`php composer.phar require "bizley/quill ^2.0"`

If you want to install Quill beta version add:  
`"bizley/quill": "^1.0"`

### Usage

Use it as an active field extension  
`<?= $form->field($model, $attribute)->widget(\bizley\quill\Quill::className(), []) ?>`

or as a standalone widget  
`<?= \bizley\quill\Quill::widget(['name' => 'editor', 'value' => '']) ?>`

### Basic parameters

 - **theme** *string* default `'snow'`  
   `'snow'` (`Quill::THEME_SNOW`) for Quill's [snow theme](https://quilljs.com/docs/themes/#snow),  
   `'bubble'` (`Quill::THEME_BUBBLE`) for Quill's [bubble theme](https://quilljs.com/docs/themes/#bubble),  
   `false` or `null` to remove theme.
   See [Quill's documentation for themes](https://quilljs.com/docs/themes/).

 - **toolbarOptions** *boolean|string|array* default `true`  
   `true` for theme's default toolbar,  
   `'FULL'` (`Quill::TOOLBAR_FULL`) for full Quill's toolbar,  
   `'BASIC'` (`Quill::TOOLBAR_BASIC`) for few basic toolbar options,  
   *array* for toolbar configuration (see below).  

### Toolbar

Quill's toolbar from version 1.0 can be easily configured with custom set of buttons.  
See [Toolbar module](https://quilljs.com/docs/modules/toolbar/) documentation for details.

You can pass PHP array to `'toolbarOptions'` parameter to configure this module (it will be JSON-encoded).

For example, to get:

```js
new Quill('#editor', {
    modules: {
        toolbar: [['bold', 'italic', 'underline'], [{'color': []}]]
    }
});
```

add the following code in widget configuration:

```php
[
    'toolbarOptions' => [['bold', 'italic', 'underline'], [['color' => []]]],
],
```

Toolbar configuration for previous yii2-quill version (**^1.0** with Quill *beta*) is deprecated.

## Additional information

### Container and form's input

Quill editor is rendered in `div` container (this can be changed by setting `'tag'` parameter) 
and edited content is copied to hidden input field so it can be used in forms.

### Editor box's height

Default editor height is *150px* (this can be changed by setting `'options'` parameter) and 
its box extends as new text lines are added.

### Quill source

Quill's JS code is provided by CDN. You can change the Quill's version set with the current yii2-quill's 
release by changing `'quillVersion'` parameter but some options may not work correctly in this case.

### Additional JavaScript code

You can use parameter `'js'` to append additional JS code.  
For example, to disable user input Quill's API provides this JS:

```js
quill.enable(false);
```

To get the same through widget's configuration add the following code:

```php
[
    'js' => '{quill}.enable(false);',
],
```

`{quill}` placeholder will be automatically replaced with the editor's object variable name.  
For more details about Quill's API visit https://quilljs.com/docs/api/

### Formula module

Quill can render math formulas using the [KaTeX](https://khan.github.io/KaTeX/) library.  
To add this option configure widget with [Formula module](https://quilljs.com/docs/modules/formula/):

```php
[
    'modules' => [
        'formula' => true // Include formula module
    ],
    'toolbarOptions' => [['formula']] // Include button in toolbar
]
```

You can change the version of KaTeX by setting the `'katexVersion'` parameter.

### Syntax Highlighter module

Quill can automatically detect and apply syntax highlighting using the [highlight.js](https://highlightjs.org/) library.
To add this option configure widget with [Syntax Highlighter module](https://quilljs.com/docs/modules/syntax/):

```php
[
    'modules' => [
        'syntax' => true // Include syntax module
    ],
    'toolbarOptions' => [['code-block']] // Include button in toolbar
]
```

You can change the version of highlight.js by setting the `'highlightVersion'` parameter.  
You can change the default highlight.js stylesheet by setting the `'highlightStyle'` parameter. 
See [the list of possible styles](https://github.com/isagalaev/highlight.js/tree/master/src/styles) (all files ending with `.min.css`).
