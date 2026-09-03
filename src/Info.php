<?php
/**
 * Created for plugin-component-info
 * Date: 01.12.2020
 * @author Timur Kasumov (XAKEPEHOK)
 */

namespace SalesRender\Plugin\Components\Info;


use InvalidArgumentException;
use JsonSerializable;
use RuntimeException;
use SalesRender\Plugin\Components\Translations\Translator;

final class Info implements JsonSerializable
{

    private PluginType $type;

    /** @var string|callable */
    private $name;

    /** @var string|callable */
    private $description;

    private JsonSerializable|array $extra;

    private Developer $developer;

    private static self $instance;

    private function __construct()
    {
    }

    public static function config(
        PluginType             $type,
        string|callable        $name,
        string|callable        $description,
        array|JsonSerializable $extra,
        Developer              $developer
    ): void
    {
        $instance = new self();

        $instance->type = $type;

        self::guardEmpty($name, 'name');
        $instance->name = is_string($name) ? trim($name) : $name;

        self::guardEmpty($description, 'description');
        $instance->description = is_string($description) ? trim($description) : $description;

        $instance->extra = $extra;

        $instance->developer = $developer;

        self::$instance = $instance;
    }

    public function getType(): PluginType
    {
        return $this->type;
    }

    public function getName(): string
    {
        $value = is_callable($this->name) ? trim(($this->name)()) : $this->name;
        self::guardEmpty($value, 'name');
        return $value;
    }

    public function getDescription(): string
    {
        $value = is_callable($this->description) ? trim(($this->description)()) : $this->description;
        self::guardEmpty($value, 'description');
        return $value;
    }

    public function getExtra(): JsonSerializable|array
    {
        return $this->extra;
    }

    public function getDeveloper(): Developer
    {
        return $this->developer;
    }

    public static function getInstance(): self
    {
        if (!isset(self::$instance)) {
            throw new RuntimeException('Plugin developer is not configured');
        }
        return self::$instance;
    }

    private static function guardEmpty(mixed $value, string $argument): void
    {
        if (is_callable($value)) {
            return;
        }

        if (!is_string($value)) {
            throw new InvalidArgumentException("Argument '{$argument}' in " . self::class . " should be string or callable", 1);
        }

        $value = trim($value);
        if ($value === '') {
            throw new InvalidArgumentException("Argument '{$argument}' in " . self::class . " should not be empty", 2);
        }
    }

    public function jsonSerialize(): array
    {
        return [
            'name' => $this->getName(),
            'description' => $this->getDescription(),
            'type' => (string)$this->getType(),
            'extra' => $this->getExtra(),
            'languages' => [
                'current' => Translator::getLang(),
                'default' => Translator::getDefaultLang(),
                'available' => Translator::getLanguages(),
            ],
            'developer' => $this->getDeveloper(),
        ];
    }
}