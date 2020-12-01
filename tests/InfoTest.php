<?php
/**
 * Created for plugin-component-info
 * Date: 02.12.2020
 * @author Timur Kasumov (XAKEPEHOK)
 */

namespace Leadvertex\Plugin\Components\Info;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class InfoTest extends TestCase
{

    private Developer $developer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->developer = new Developer('name', 'email@example.com', 'example.com');
    }

    public function testConfigEmptyName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Info::config('', 'description', [], $this->developer);
    }

    public function testConfigEmptyDescription(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Info::config('name', '', [], $this->developer);
    }

    public function testConfigInvalidExtra(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Info::config('name', '', '', $this->developer);
    }

    public function testGetName(): void
    {
        Info::config('string name', 'description', [], $this->developer);
        $this->assertSame('string name', Info::getInstance()->getName());

        Info::config(fn() => 'callable name', 'description', [], $this->developer);
        $this->assertSame('callable name', Info::getInstance()->getName());

        Info::config(fn() => '', 'description', [], $this->developer);
        $this->expectException(InvalidArgumentException::class);
        Info::getInstance()->getName();

    }

    public function testGetDescription(): void
    {
        Info::config('name', 'string description',[], $this->developer);
        $this->assertSame('string description', Info::getInstance()->getDescription());

        Info::config('name', fn() => 'callable description',[], $this->developer);
        $this->assertSame('callable description', Info::getInstance()->getDescription());

        Info::config('name', fn() => '', [], $this->developer);
        $this->expectException(InvalidArgumentException::class);
        Info::getInstance()->getDescription();
    }

    public function testGetExtra(): void
    {
        //array
        Info::config('name', 'description',['key' => 'value'], $this->developer);
        $this->assertSame(['key' => 'value'], Info::getInstance()->getExtra());

        //JsonSerializable
        Info::config('name', 'description',$this->developer, $this->developer);
        $this->assertSame($this->developer, Info::getInstance()->getExtra());
    }

    public function testGetDeveloper(): void
    {
        Info::config('name', 'description',[], $this->developer);
        $this->assertSame($this->developer, Info::getInstance()->getDeveloper());
    }

}
