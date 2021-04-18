<?php

declare(strict_types=1);

namespace Lulububu\FaviconExtension\Type;

/**
 * Class IconType
 *
 * @author Joshua Schumacher <joshua@lulububu.de>
 * @package Lulububu\FaviconExtension\Type
 */
class IconType
{
    const ICO   = 'ico';
    const PNG32 = 'png32';

    /**
     * @return array
     * @throws \ReflectionException
     */
    static public function getConstants()
    {
        $className = get_class();
        $class     = new \ReflectionClass($className);

        return $class->getConstants();
    }
}
