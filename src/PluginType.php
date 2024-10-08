<?php
/**
 * Created for plugin-component-info
 * Date: 02.12.2020
 * @author Timur Kasumov (XAKEPEHOK)
 */

namespace SalesRender\Plugin\Components\Info;


use XAKEPEHOK\EnumHelper\EnumHelper;

final class PluginType extends EnumHelper
{

    private string $type;

    const MACROS = 'MACROS';
    const LOGISTIC = 'LOGISTIC';
    const PBX = 'PBX';
    const CHAT = 'CHAT';
    const GEOCODER = 'GEOCODER';
    const INTEGRATION = 'INTEGRATION';
    const RESALE = 'RESALE';

    public function __construct(string $type)
    {
        self::guardValidValue($type);
        $this->type = $type;
    }

    public static function values(): array
    {
        return [
            self::MACROS,
            self::LOGISTIC,
            self::PBX,
            self::CHAT,
            self::GEOCODER,
            self::INTEGRATION,
            self::RESALE,
        ];
    }

    public function get(): string
    {
        return $this->type;
    }

    public function __toString(): string
    {
        return $this->type;
    }
}