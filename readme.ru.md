# salesrender/plugin-component-info

Контейнер метаданных плагина для экосистемы плагинов SalesRender, предоставляющий структурированную информацию о типе плагина, названии, описании и разработчике.

## Обзор

`plugin-component-info` -- это базовый компонент, необходимый каждому плагину SalesRender. Он хранит и предоставляет метаданные, идентифицирующие плагин на платформе SalesRender: тип плагина, его назначение, информацию о разработчике и дополнительную конфигурацию, специфичную для типа.

Компонент построен на трёх ключевых классах: `Info` (singleton, содержащий полные метаданные плагина), `PluginType` (enum-подобный класс, определяющий поддерживаемые категории плагинов) и `Developer` (объект-значение с контактной информацией разработчика). Вместе они формируют JSON-ответ, отдаваемый endpoint-ом `/info` плагина, который платформа SalesRender использует для регистрации и отображения плагина.

Поля `name` и `description` поддерживают callable-значения, что позволяет формировать динамический контент во время выполнения -- например, для предоставления локализованного текста через компонент переводов.

## Установка

```bash
composer require salesrender/plugin-component-info
```

## Требования

- PHP >= 7.4
- Расширения: `ext-json`
- Зависимости:
  - `xakepehok/enum-helper` ^0.1.0 -- валидация значений перечислений

## Ключевые классы

### `Info`

**Namespace:** `SalesRender\Plugin\Components\Info`

Singleton-класс, содержащий полные метаданные плагина. Настраивается один раз при загрузке приложения через статический метод `config()`, затем доступен через `getInstance()`. Реализует `JsonSerializable` для прямого вывода в JSON.

**Статические методы:**

| Метод | Сигнатура | Описание |
|-------|-----------|----------|
| `config` | `static config(PluginType $type, string\|callable $name, string\|callable $description, array\|JsonSerializable $extra, Developer $developer): void` | Настроить singleton-экземпляр со всеми метаданными плагина |
| `getInstance` | `static getInstance(): self` | Получить настроенный singleton. Выбрасывает `RuntimeException`, если не настроен |

**Методы экземпляра:**

| Метод | Сигнатура | Описание |
|-------|-----------|----------|
| `getType` | `getType(): PluginType` | Получить тип плагина |
| `getName` | `getName(): string` | Получить название плагина (вызывает callable, если передан) |
| `getDescription` | `getDescription(): string` | Получить описание плагина (вызывает callable, если передан) |
| `getExtra` | `getExtra(): array\|JsonSerializable\|mixed` | Получить дополнительные метаданные (конфигурация, специфичная для типа) |
| `getDeveloper` | `getDeveloper(): Developer` | Получить информацию о разработчике |
| `jsonSerialize` | `jsonSerialize(): array` | Сериализовать в JSON (включает name, description, type, extra, languages, developer) |

**Параметры метода `config()`:**

| Параметр | Тип | Описание |
|----------|-----|----------|
| `$type` | `PluginType` | Категория плагина (MACROS, LOGISTIC, PBX, CHAT и т.д.) |
| `$name` | `string\|callable` | Отображаемое название плагина. Если передан callable, он вызывается при обращении к `getName()` |
| `$description` | `string\|callable` | Описание плагина (поддерживает Markdown). Если передан callable, он вызывается при обращении к `getDescription()` |
| `$extra` | `array\|JsonSerializable` | Метаданные, специфичные для типа (например, класс плагина, сущность, валюта, возможности) |
| `$developer` | `Developer` | Контактная информация разработчика |

**Валидация:**

- `$name` и `$description` должны быть непустыми строками или callable. Выбрасывается `InvalidArgumentException`, если значение пустое или неверного типа.
- `$extra` должен быть массивом или реализовывать `JsonSerializable`. В противном случае выбрасывается `InvalidArgumentException`.

### `PluginType`

**Namespace:** `SalesRender\Plugin\Components\Info`

Enum-подобный класс (наследует `EnumHelper`), представляющий поддерживаемые категории плагинов на платформе SalesRender.

**Константы:**

| Константа | Значение | Описание |
|-----------|----------|----------|
| `PluginType::MACROS` | `'MACROS'` | Плагины пакетной обработки и макросов |
| `PluginType::LOGISTIC` | `'LOGISTIC'` | Плагины логистики и доставки |
| `PluginType::PBX` | `'PBX'` | Плагины интеграции с телефонией |
| `PluginType::CHAT` | `'CHAT'` | Плагины чатов и обмена сообщениями |
| `PluginType::GEOCODER` | `'GEOCODER'` | Плагины геокодирования |
| `PluginType::INTEGRATION` | `'INTEGRATION'` | Плагины интеграции со сторонними сервисами |

**Конструктор:**

```php
public function __construct(string $type)
```

Выбрасывает исключение, если переданный тип не является одним из допустимых значений.

**Методы:**

| Метод | Сигнатура | Описание |
|-------|-----------|----------|
| `get` | `get(): string` | Получить значение типа в виде строки |
| `values` | `static values(): array` | Получить все допустимые значения типов |
| `__toString` | `__toString(): string` | Строковое представление типа |

### `Developer`

**Namespace:** `SalesRender\Plugin\Components\Info`

Неизменяемый объект-значение, содержащий контактную информацию разработчика плагина. Реализует `JsonSerializable`.

**Конструктор:**

```php
public function __construct(string $name, string $email, string $hostname = null)
```

| Параметр | Тип | Описание |
|----------|-----|----------|
| `$name` | `string` | Имя разработчика или название компании |
| `$email` | `string` | Адрес электронной почты поддержки для данного плагина |
| `$hostname` | `string\|null` | Доменное имя сайта разработчика (без `http://` или `https://`, например, `"example.com"`) |

**Валидация:**

- `$hostname` должен быть валидным доменным именем без протокола и слешей. Выбрасывается `InvalidArgumentException`, если формат неверный.

**Методы:**

| Метод | Сигнатура | Описание |
|-------|-----------|----------|
| `getName` | `getName(): string` | Получить имя разработчика |
| `getEmail` | `getEmail(): string` | Получить email поддержки (в нижнем регистре) |
| `getHostname` | `getHostname(): string` | Получить доменное имя сайта |
| `jsonSerialize` | `jsonSerialize(): array` | Сериализовать в JSON-массив |

## Использование

### Базовая конфигурация

Настройте информацию о плагине в файле `bootstrap.php`:

```php
use SalesRender\Plugin\Components\Info\Info;
use SalesRender\Plugin\Components\Info\PluginType;
use SalesRender\Plugin\Components\Info\Developer;

Info::config(
    new PluginType(PluginType::MACROS),
    'Экспорт в Excel',
    'Этот плагин позволяет экспортировать заказы в формат Excel',
    [
        'class' => 'exporter',
        'entity' => 'order',
    ],
    new Developer(
        'Пример компании',
        'support@example.com',
        'example.com',
    )
);
```

### Использование callable для локализованных названий

Используйте callable для `$name` и `$description` для поддержки локализации во время выполнения через компонент переводов:

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

### Плагин чата с описанием возможностей

Для чат-плагинов массив `$extra` обычно описывает возможности обмена сообщениями:

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

### Конфигурация плагина PBX

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
        'Название вашей компании',
        'support.for.plugin@example.com',
        'example.com',
    )
);
```

### Доступ к информации о плагине во время выполнения

```php
use SalesRender\Plugin\Components\Info\Info;

$info = Info::getInstance();

// Получение отдельных полей
echo $info->getName();           // "Экспорт в Excel" (или результат вызова callable)
echo $info->getDescription();    // "Этот плагин позволяет..."
echo $info->getType()->get();    // "MACROS"

// Получение дополнительных данных
$extra = $info->getExtra();

// Получение информации о разработчике
$developer = $info->getDeveloper();
echo $developer->getName();      // "Пример компании"
echo $developer->getEmail();     // "support@example.com"
echo $developer->getHostname();  // "example.com"

// Сериализация в JSON (используется endpoint-ом /info)
echo json_encode($info);
```

### Структура JSON-ответа

При сериализации в JSON (как возвращает endpoint `/info`) результат имеет следующую структуру:

```json
{
    "name": "Экспорт в Excel",
    "description": "Этот плагин позволяет экспортировать заказы в формат Excel",
    "type": "MACROS",
    "extra": {
        "class": "exporter",
        "entity": "order"
    },
    "languages": {
        "current": "ru_RU",
        "default": "ru_RU",
        "available": ["ru_RU", "en_US"]
    },
    "developer": {
        "name": "Пример компании",
        "email": "support@example.com",
        "hostname": "example.com"
    }
}
```

## Конфигурация

Компонент `Info` настраивается один раз на этапе загрузки плагина, обычно в файле `bootstrap.php`. Статический метод `config()` должен быть вызван до любого обращения к `getInstance()`.

**Порядок конфигурации в `bootstrap.php`:**

1. Настройка подключения к базе данных (`Connector::config(...)`)
2. Настройка компонента переводов (`Translator::config(...)`)
3. Настройка информации о плагине (`Info::config(...)`)
4. Настройка форм настроек и других компонентов

## Справочник API

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

**Допустимые значения:** `MACROS`, `LOGISTIC`, `PBX`, `CHAT`, `GEOCODER`, `INTEGRATION`, `RESALE`

### `Developer`

```php
__construct(string $name, string $email, string $hostname = null)
getName(): string
getEmail(): string
getHostname(): string
jsonSerialize(): array
```

## Зависимости

| Пакет | Версия | Назначение |
|-------|--------|------------|
| `xakepehok/enum-helper` | ^0.1.0 | Валидация значений перечисления для `PluginType` |

**Dev-зависимости:**

| Пакет | Версия | Назначение |
|-------|--------|------------|
| `salesrender/plugin-component-translations` | ^0.1.2 | Используется в тестах для проверки поддержки локализации |

## Смотрите также

- [`salesrender/plugin-component-translations`](https://github.com/SalesRender/plugin-component-translations) -- компонент переводов, используемый с callable для `$name`/`$description`
- [`salesrender/plugin-component-purpose`](https://github.com/SalesRender/plugin-component-purpose) -- `PluginPurpose`, `PluginEntity` и определения классов плагинов, часто используемые в качестве `$extra`
- [`salesrender/plugin-component-settings`](https://github.com/SalesRender/plugin-component-settings) -- настройки плагина, обычно конфигурируемые после `Info`
