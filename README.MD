- `\Leadvertex\Plugin\Components\Info\Info` - component for represent information about plugin developer
- `\Leadvertex\Plugin\Components\Info\Developer` - component for represent information about plugin itself

Installation
```shell script
composer require leadvertex/plugin-component-info
```

Configuration
```php
$developer = new \Leadvertex\Plugin\Components\Info\Developer(
    'Tony Stark', 
    'support@starkindustries.com', 
    'starkindustries.com'
);

//PluginPurpose shown only for example 
//@see https://github.com/leadvertex/plugin-component-purpose
$purpose = new PluginPurpose(
    new PluginClass(PluginClass::CLASS_EXPORTER),
    new PluginEntity(PluginEntity ::ENTITY_ORDER),
);

$info = \Leadvertex\Plugin\Components\Info\Info::config(
    'Excel',
    'This plugin can be used for export your orders to Excel',
    $purpose,
    $developer
);

//Info name and description also can be a callable
$info = \Leadvertex\Plugin\Components\Info\Info::config(
    fn() => 'Excel',
    fn() => 'This plugin can be used for export your orders to Excel',
    $purpose,
    $developer
);
```

Usage
```php
echo Info::getInstance()->getName();           //print "Excel", also for callable
echo Info::getInstance()->getDescription();    //print "This plugin can be used for export your orders to Excel", also for callable 

$extra = Info::getInstance()->getExtra();
$developer = Info::getInstance()->getDeveloper();
```