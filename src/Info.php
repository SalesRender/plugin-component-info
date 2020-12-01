<?php
/**
 * Created for plugin-component-info
 * Date: 01.12.2020
 * @author Timur Kasumov (XAKEPEHOK)
 */

namespace Leadvertex\Plugin\Components\Info;


use InvalidArgumentException;
use JsonSerializable;
use RuntimeException;

final class Info
{

    /** @var string|callable */
    private $name;

    /** @var string|callable */
    private $description;

    /** @var array|mixed */
    private $extra;

    private Developer $developer;

    private static self $instance;

    private function __construct() {}

    public static function config($name, $description, $extra, Developer $developer): void
    {
        $instance = new self();

        self::guardEmpty($name, 'name');
        $instance->name = is_string($name) ? trim($name) : $name;

        self::guardEmpty($description, 'description');
        $instance->description = is_string($description) ? trim($description) : $description;

        if (!is_array($extra) && !($extra instanceof JsonSerializable)) {
            throw new InvalidArgumentException("Argument 'extra' in " . self::class . " should be array or JsonSerializable", 2);
        }
        $instance->extra = $extra;

        $instance->developer = $developer;

        self::$instance = $instance;
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

    /**
     * @return array|mixed
     */
    public function getExtra()
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

    private static function guardEmpty($value, string $argument): void
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

}