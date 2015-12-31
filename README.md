# yii2-quill
[![Latest Stable Version](https://poser.pugx.org/bizley/quill/v/stable)](https://packagist.org/packages/bizley/quill) 
[![Total Downloads](https://poser.pugx.org/bizley/quill/downloads)](https://packagist.org/packages/bizley/quill) 
[![Latest Unstable Version](https://poser.pugx.org/bizley/quill/v/unstable)](https://packagist.org/packages/bizley/quill) 
[![License](https://poser.pugx.org/bizley/quill/license)](https://packagist.org/packages/bizley/quill)

*Yii 2 implementation of Quill, modern WYSIWYG editor.*

## Quill
You can find Quill at http://quilljs.com  
- [Documentation](http://quilljs.com/docs/quickstart)
- [Examples](http://quilljs.com/examples)
- [GitHub](https://github.com/quilljs/quill)

## yii2-quill

### Installation

Easiest way to install this extension is through the [Composer](https://getcomposer.org).  
Add in your ```composer.json```:  
```"bizley/quill": "*"```  
or run console command:  
```php composer.phar require bizley/quill "*"```

### Usage

Use it as an active field extension  
```<?= $form->field($model, $attribute)->widget(bizley\quill\Quill::className(), []) ?>```

or as a standalone widget  
```<?= bizley\quill\Quill::widget(['name' => 'editor']) ?>```

### Parameters
- **theme** *string* default ```'bootstrap'```  
  ```false``` or ```null``` for Quill's default theme with quill.base.css,  
  ```'snow'``` for Quill's snow theme with quill.snow.css,  
  ```'bootstrap'``` for snow theme with editor wrapped in [Bootstrap's panel](http://getbootstrap.com/components/#panels)  
  You can set theme in ```configs``` array instead but this is the only way to set ```'bootstrap'``` theme.  
  See [Quill's documentation for themes](http://quilljs.com/docs/themes).
  
- **toolbar** *string* or *array* default ```'full'```  
  In case of *string*:  
  ```false``` or ```null``` to switch toolbar off,  
  ```'full'``` for full Quill's toolbar as seen [here](http://quilljs.com),  
  ```'basic'``` for few basic toolbar options,  
  *anything else* for single button (see below).  
  In case of *array*:  
  *string element* for single button (see below),  
  *array element* for buttons grouped together - every element of this array should be *string* (a single button).
  
- **configs** *array* default ```[]```  
  Array of Quill's configuration. This is the equivalent of [Quill's configs variable](http://quilljs.com/docs/configuration)

- **options** *array* default ```[]```  
  Array of HTML options passed to the editor's div.

- **js** *string* default ```null```  
  Additional js to be called with the editor.
  Use placeholder ```{quill}``` to get the current editor object variable.
  
### Toolbar
Quill allows you to add your own HTML toolbar for the editor. This is very flexible solution but in most cases you just need to 
get few standard buttons without worrying about the HTML tags and where and how to properly place them.  
With **yii2-quill** it is quite simple - there are predefined buttons you can use:  

- ```'|'``` separator,
- ```'b'``` bold,
- ```'i'``` italic,
- ```'u'``` underline,
- ```'s'``` strikethrough,
- ```'font'``` font family,
- ```'size'``` font size,
- ```'textColor'``` font colour,
- ```'backColor'``` background colour,
- ```'ol'``` ordered list,
- ```'ul'``` bullet list,
- ```'alignment'``` text alignment,
- ```'link'``` link,
- ```'image'``` image  

With **toolbar** parameter set to ```'full'``` all these buttons are added. ```'basic'``` gives you only 
```'b', 'i', 'u', 's', 'ol', 'ul', 'alignment', 'link'```.

In case you want totally different set of buttons you can set them like:  
```'toolbar' => ['b', 'i', 'u', '|', 'font', '|', 'alignment'],```

If you want to group some buttons together use nested arrays:  
```'toolbar' => [['b', 'i', 'u'], ['font', 'alignment']],```

And don't worry about adding modules in **configs** for ```'link'``` and ```'image'``` options - this is done automatically.

You may wonder what to do in case of adding some button that is not listed here - simply just put the HTML code of the button 
inside the **toolbar** array. *And there is still this option to create separate toolbar from the scratch - you can add toolbar 
module with its container's ID in the __configs__ array as [seen here](http://quilljs.com/docs/quickstart).*
