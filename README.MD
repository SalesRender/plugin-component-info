- `\SalesRender\Plugin\Components\Info\Info` - component for represent information about plugin developer
- `\SalesRender\Plugin\Components\Info\Developer` - component for represent information about plugin itself

Installation
```shell script
composer require salesrender/plugin-component-info
```

Configuration
```php
$type = new \SalesRender\Plugin\Components\Info\PluginType(PluginType::MACROS);

//PluginPurpose shown only for example 
//@see https://github.com/salesrender/plugin-component-purpose
$purpose = new PluginPurpose(
    new PluginClass(PluginClass::CLASS_EXPORTER),
    new PluginEntity(PluginEntity ::ENTITY_ORDER),
);

$developer = new \SalesRender\Plugin\Components\Info\Developer(
    'Tony Stark', 
    'support@starkindustries.com', 
    'starkindustries.com'
);

$info = \SalesRender\Plugin\Components\Info\Info::config(
    $type,
    'Excel',
    'This plugin can be used for export your orders to Excel',
    $purpose,
    $developer
);

//Info name and description also can be a callable
$info = \SalesRender\Plugin\Components\Info\Info::config(
    $type,
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

echo json_encode(Info::getInstance());         //can be serialized into JSON
```