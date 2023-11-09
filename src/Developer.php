<?php
/**
 * Created for plugin-info
 * Datetime: 25.06.2019 12:23
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace SalesRender\Plugin\Components\Info;


use InvalidArgumentException;
use JsonSerializable;

final class Developer implements JsonSerializable
{

    private string $name;

    private string $email;

    private ?string $hostname;

    /**
     * Developer constructor.
     * @param string $name of company or developer
     * @param string $email of support for this plugin
     * @param string|null $hostname hostname of company or developer
     */
    public function __construct(string $name, string $email, string $hostname = null)
    {
        $this->name = trim($name);
        $this->email = strtolower(trim($email));

        $hostname = trim(strtolower($hostname));
        if (!preg_match('~^[^.][a-z\d\-.]+[^.]$~u', $hostname)) {
            throw new InvalidArgumentException('Hostname should not contain http(s)://, or slashes. For example, it can be "example.com"');
        }

        $this->hostname = $hostname;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getHostname(): string
    {
        return $this->hostname;
    }

    public function jsonSerialize()
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'hostname' => $this->hostname,
        ];
    }
}