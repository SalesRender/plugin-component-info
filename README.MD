# salesrender/plugin-component-info

Plugin metadata container for the SalesRender plugin ecosystem, providing structured information about the plugin's type, name, description, and developer.

## Overview

`plugin-component-info` is a core component required by every SalesRender plugin. It stores and exposes the metadata that identifies a plugin within the SalesRender platform: what type of plugin it is, what it does, who developed it, and any additional type-specific configuration.

The component is built around three key classes: `Info` (a singleton that holds the complete plugin metadata), `PluginType` (an enum-like class defining the supported plugin categories), and `Developer` (a value object with the developer's contact information). Together, they produce the JSON response served by the plugin's `/info` endpoint, which the SalesRender platform uses to register and display the plugin.

Both `name` and `description` fields support callable values, enabling dynamic content that can be resolved at runtime -- for example, to provide localized text via a translation component.

## Installation

```bash
composer require salesrender/plugin-component-info
```

## Requirements

- PHP >= 7.4
- Extensions: `ext-json`
- Dependencies:
  - `xakepehok/enum-helper` ^0.1.0 -- enum value validation

## Key Classes

### `Info`

**Namespace:** `SalesRender\Plugin\Components\Info`

Singleton class that holds the complete plugin metadata. Configured once during bootstrap via the static `config()` method, then accessed via `getInstance()`. Implements `JsonSerializable` for direct JSON output.

**Static methods:**

| Method | Signature | Description |
|--------|-----------|-------------|
| `config` | `static config(PluginType $type, string\|callable $name, string\|callable $description, array\|JsonSerializable $extra, Developer $developer): void` | Configure the singleton instance with all plugin metadata |
| `getInstance` | `static getInstance(): self` | Get the configured singleton. Throws `RuntimeException` if not configured |

**Instance methods:**

| Method | Signature | Description |
|--------|-----------|-------------|
| `getType` | `getType(): PluginType` | Get the plugin type |
| `getName` | `getName(): string` | Get the plugin name (resolves callable if provided) |
| `getDescription` | `getDescription(): string` | Get the plugin description (resolves callable if provided) |
| `getExtra` | `getExtra(): array\|JsonSerializable\|mixed` | Get the extra metadata (type-specific configuration) |
| `getDeveloper` | `getDeveloper(): Developer` | Get the developer information |
| `jsonSerialize` | `jsonSerialize(): array` | Serialize to JSON (includes name, description, type, extra, languages, developer) |

**`config()` parameter details:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$type` | `PluginType` | Plugin category (MACROS, LOGISTIC, PBX, CHAT, etc.) |
| `$name` | `string\|callable` | Plugin display name. If callable, it is invoked when `getName()` is called |
| `$description` | `string\|callable` | Plugin description (supports Markdown). If callable, it is invoked when `getDescription()` is called |
| `$extra` | `array\|JsonSerializable` | Type-specific metadata (e.g., plugin class, entity, currency, capabilities) |
| `$developer` | `Developer` | Developer contact information |

**Validation:**

- `$name` and `$description` must be non-empty strings or callables. An `InvalidArgumentException` is thrown if the value is empty or of an invalid type.
- `$extra` must be an array or implement `JsonSerializable`. An `InvalidArgumentException` is thrown otherwise.

### `PluginType`

**Namespace:** `SalesRender\Plugin\Components\Info`

Enum-like class (extends `EnumHelper`) representing the supported plugin categories in the SalesRender platform.

**Constants:**

| Constant | Value | Description |
|----------|-------|-------------|
| `PluginType::MACROS` | `'MACROS'` | Macro/batch processing plugins |
| `PluginType::LOGISTIC` | `'LOGISTIC'` | Logistics and delivery plugins |
| `PluginType::PBX` | `'PBX'` | Telephony/PBX integration plugins |
| `PluginType::CHAT` | `'CHAT'` | Chat/messaging plugins |
| `PluginType::GEOCODER` | `'GEOCODER'` | Geocoding plugins |
| `PluginType::INTEGRATION` | `'INTEGRATION'` | Third-party integration plugins |

**Constructor:**

```php
public function __construct(string $type)
```

Throws an exception if the provided type is not one of the valid values.

**Methods:**

| Method | Signature | Description |
|--------|-----------|-------------|
| `get` | `get(): string` | Get the type value as a string |
| `values` | `static values(): array` | Get all valid type values |
| `__toString` | `__toString(): string` | String representation of the type |

### `Developer`

**Namespace:** `SalesRender\Plugin\Components\Info`

Immutable value object containing the plugin developer's contact information. Implements `JsonSerializable`.

**Constructor:**

```php
public function __construct(string $name, string $email, string $hostname = null)
```

| Parameter | Type | Description |
|-----------|------|-------------|
| `$name` | `string` | Developer or company name |
| `$email` | `string` | Support email address for this plugin |
| `$hostname` | `string\|null` | Developer's website hostname (without `http://` or `https://`, e.g., `"example.com"`) |

**Validation:**

- `$hostname` must be a valid domain name without protocol or slashes. An `InvalidArgumentException` is thrown if the format is invalid.

**Methods:**

| Method | Signature | Description |
|--------|-----------|-------------|
| `getName` | `getName(): string` | Get the developer name |
| `getEmail` | `getEmail(): string` | Get the support email (lowercased) |
| `getHostname` | `getHostname(): string` | Get the website hostname |
| `jsonSerialize` | `jsonSerialize(): array` | Serialize to JSON array |

## Usage

### Basic Configuration

Configure the plugin info in your `bootstrap.php`:

```php
use SalesRender\Plugin\Components\Info\Info;
use SalesRender\Plugin\Components\Info\PluginType;
use SalesRender\Plugin\Components\Info\Developer;

Info::config(
    new PluginType(PluginType::MACROS),
    'Excel Export',
    'This plugin can be used for exporting your orders to Excel',
    [
        'class' => 'exporter',
        'entity' => 'order',
    ],
    new Developer(
        'Example Company',
        'support@example.com',
        'example.com',
    )
);
```

### Using Callables for Localized Names

Use callables for `$name` and `$description` to support runtime localization via the translations component:

```php
use SalesRender\Plugin\Components\Info\Info;
use SalesRender\Plugin\Components\Info\PluginType;
use SalesRender\Plugin\Components\Info\Developer;
use SalesRender\Plugin\Components\Translations\Translator;

Info::config(
    new PluginType(PluginType::LOGISTIC),
    fn() => Translator::get('info', 'Example logistic'),
    fn() => Translator::get('info', 'Example **logistic** description'),
    [
        'class' => 'delivery',
        'entity' => 'order',
        'currency' => ['RUB'],
        'codename' => 'SR_LOGISTIC_EXAMPLE',
    ],
    new Developer(
        'Example company',
        'support.for.plugin@example.com',
        'example.com',
    )
);
```

### Chat Plugin with Capabilities

For chat plugins, the `$extra` array typically describes messaging capabilities:

```php
use SalesRender\Plugin\Components\Info\Info;
use SalesRender\Plugin\Components\Info\PluginType;
use SalesRender\Plugin\Components\Info\Developer;
use SalesRender\Plugin\Components\Translations\Translator;

Info::config(
    new PluginType(PluginType::CHAT),
    fn() => Translator::get('info', 'Example chat plugin'),
    fn() => Translator::get('info', 'This plugin created only for demo purposes'),
    [
        'contactType' => 'telegram',
        'capabilities' => [
            'subject' => true,
            'typing' => false,
            'messages' => [
                'formats' => ['text'],
                'incoming' => true,
                'outgoing' => true,
                'writeFirst' => true,
                'statuses' => ['sent', 'delivered', 'read', 'error'],
            ],
            'attachments' => ['image', 'file'],
        ],
    ],
    new Developer(
        'LEADVERTEX',
        'support@salesrender.com',
        'salesrender.com',
    )
);
```

### PBX Plugin Configuration

```php
use SalesRender\Plugin\Components\Info\Info;
use SalesRender\Plugin\Components\Info\PluginType;
use SalesRender\Plugin\Components\Info\Developer;
use SalesRender\Plugin\Components\Translations\Translator;

Info::config(
    new PluginType(PluginType::PBX),
    fn() => Translator::get('info', 'Plugin name'),
    fn() => Translator::get('info', 'Plugin markdown description'),
    [
        'class' => 'SIP',
        'entity' => 'UNSPECIFIED',
        'currency' => 'USD',
        'pricing' => [
            'encryption' => '0.01',
            'record' => '0.005',
        ],
        'codename' => 'SR_PBX_EXAMPLE',
    ],
    new Developer(
        'Your (company) name',
        'support.for.plugin@example.com',
        'example.com',
    )
);
```

### Accessing Plugin Info at Runtime

```php
use SalesRender\Plugin\Components\Info\Info;

$info = Info::getInstance();

// Get individual fields
echo $info->getName();           // "Excel Export" (or resolved callable result)
echo $info->getDescription();    // "This plugin can be used for..."
echo $info->getType()->get();    // "MACROS"

// Get extra data
$extra = $info->getExtra();

// Get developer info
$developer = $info->getDeveloper();
echo $developer->getName();      // "Example Company"
echo $developer->getEmail();     // "support@example.com"
echo $developer->getHostname();  // "example.com"

// Serialize to JSON (used by the /info endpoint)
echo json_encode($info);
```

### JSON Output Structure

When serialized to JSON (as returned by the `/info` endpoint), the output has the following structure:

```json
{
    "name": "Excel Export",
    "description": "This plugin can be used for exporting your orders to Excel",
    "type": "MACROS",
    "extra": {
        "class": "exporter",
        "entity": "order"
    },
    "languages": {
        "current": "en_US",
        "default": "ru_RU",
        "available": ["ru_RU", "en_US"]
    },
    "developer": {
        "name": "Example Company",
        "email": "support@example.com",
        "hostname": "example.com"
    }
}
```

## Configuration

The `Info` component is configured once during the plugin bootstrap phase, typically in `bootstrap.php`. The `config()` static method must be called before any access to `getInstance()`.

**Configuration order in `bootstrap.php`:**

1. Configure the database connection (`Connector::config(...)`)
2. Configure the translator (`Translator::config(...)`)
3. Configure plugin info (`Info::config(...)`)
4. Configure settings, forms, and other components

## API Reference

### `Info`

```php
static config(PluginType $type, string|callable $name, string|callable $description, array|JsonSerializable $extra, Developer $developer): void
static getInstance(): self

getType(): PluginType
getName(): string
getDescription(): string
getExtra(): array|JsonSerializable|mixed
getDeveloper(): Developer
jsonSerialize(): array
```

### `PluginType`

```php
__construct(string $type)
get(): string
static values(): array
__toString(): string
```

**Valid values:** `MACROS`, `LOGISTIC`, `PBX`, `CHAT`, `GEOCODER`, `INTEGRATION`, `RESALE`

### `Developer`

```php
__construct(string $name, string $email, string $hostname = null)
getName(): string
getEmail(): string
getHostname(): string
jsonSerialize(): array
```

## Dependencies

| Package | Version | Purpose |
|---------|---------|---------|
| `xakepehok/enum-helper` | ^0.1.0 | Enum validation for `PluginType` values |

**Dev dependencies:**

| Package | Version | Purpose |
|---------|---------|---------|
| `salesrender/plugin-component-translations` | ^0.1.2 | Used in tests for verifying localization support |

## See Also

- [`salesrender/plugin-component-translations`](https://github.com/SalesRender/plugin-component-translations) -- translation component used with callable `$name`/`$description`
- [`salesrender/plugin-component-purpose`](https://github.com/SalesRender/plugin-component-purpose) -- `PluginPurpose`, `PluginEntity`, and plugin class definitions often used as `$extra`
- [`salesrender/plugin-component-settings`](https://github.com/SalesRender/plugin-component-settings) -- plugin settings, typically configured after `Info`
