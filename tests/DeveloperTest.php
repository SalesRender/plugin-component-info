<?php
/**
 * Created for plugin-info
 * Datetime: 25.06.2019 17:00
 * @author Timur Kasumov aka XAKEPEHOK
 */

namespace SalesRender\Plugin\Components\Info;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class DeveloperTest extends TestCase
{

    private Developer $developer;

    private string $name = 'Tony';

    private string $email = 'Tony@starkindustries.com';

    private string $hostname = 'starkindustries.com';

    protected function setUp(): void
    {
        parent::setUp();
        $this->developer = new Developer($this->name, $this->email, $this->hostname);
    }

    public function testConfigWithInvalidHostname(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Developer($this->name, $this->email, 'https://example.com');
    }

    public function testGetName(): void
    {
        $this->assertEquals($this->name, $this->developer->getName());
    }

    public function testGetEmail(): void
    {
        $this->assertEquals('tony@starkindustries.com', $this->developer->getEmail());
    }

    public function testJsonSerialize(): void
    {
        $this->assertEquals(
            '{"name":"Tony","email":"tony@starkindustries.com","hostname":"starkindustries.com"}',
            json_encode($this->developer)
        );
    }

}
