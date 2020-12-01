<?php
/**
 * Created for plugin-component-info
 * Date: 02.12.2020
 * @author Timur Kasumov (XAKEPEHOK)
 */

namespace Leadvertex\Plugin\Components\Info;

use InvalidArgumentException;
use Leadvertex\Plugin\Components\Translations\Translator;
use PHPUnit\Framework\TestCase;

class InfoTest extends TestCase
{

    private PluginType $type;

    private Developer $developer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->type = new PluginType(PluginType::MACROS);
        $this->developer = new Developer('name', 'email@example.com', 'example.com');
    }

    public function testConfigEmptyName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Info::config($this->type, '', 'description', [], $this->developer);
    }

    public function testConfigEmptyDescription(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Info::config($this->type, 'name', '', [], $this->developer);
    }

    public function testConfigInvalidExtra(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Info::config($this->type, 'name', '', '', $this->developer);
    }

    public function testGetType()
    {
        Info::config($this->type, 'string name', 'description', [], $this->developer);
        $this->assertSame($this->type, Info::getInstance()->getType());
    }

    public function testGetName(): void
    {
        Info::config($this->type, 'string name', 'description', [], $this->developer);
        $this->assertSame('string name', Info::getInstance()->getName());

        Info::config($this->type, fn() => 'callable name', 'description', [], $this->developer);
        $this->assertSame('callable name', Info::getInstance()->getName());

        Info::config($this->type, fn() => '', 'description', [], $this->developer);
        $this->expectException(InvalidArgumentException::class);
        Info::getInstance()->getName();

    }

    public function testGetDescription(): void
    {
        Info::config($this->type, 'name', 'string description',[], $this->developer);
        $this->assertSame('string description', Info::getInstance()->getDescription());

        Info::config($this->type, 'name', fn() => 'callable description',[], $this->developer);
        $this->assertSame('callable description', Info::getInstance()->getDescription());

        Info::config($this->type, 'name', fn() => '', [], $this->developer);
        $this->expectException(InvalidArgumentException::class);
        Info::getInstance()->getDescription();
    }

    public function testGetExtra(): void
    {
        //array
        Info::config($this->type, 'name', 'description',['key' => 'value'], $this->developer);
        $this->assertSame(['key' => 'value'], Info::getInstance()->getExtra());

        //JsonSerializable
        Info::config($this->type, 'name', 'description',$this->developer, $this->developer);
        $this->assertSame($this->developer, Info::getInstance()->getExtra());
    }

    public function testGetDeveloper(): void
    {
        Info::config($this->type, 'name', 'description',[], $this->developer);
        $this->assertSame($this->developer, Info::getInstance()->getDeveloper());
    }

    public function testJsonSerialize()
    {
        Translator::config('ru_RU');
        Info::config($this->type, 'name', 'description',['key' => 'value'], $this->developer);
        $this->assertEquals(
            '{"name":"name","description":"description","type":"MACROS","extra":{"key":"value"},"languages":{"current":"ru_RU","default":"ru_RU","available":["ru_RU"]},"developer":{"name":"name","email":"email@example.com","hostname":"example.com"}}',
            json_encode(Info::getInstance())
        );
    }

}
