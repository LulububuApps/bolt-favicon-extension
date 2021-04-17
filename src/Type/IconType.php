<?php

//  _        _      _         _
// | |  _  _| |_  _| |__ _  _| |__ _  _
// | |_| || | | || | '_ \ || | '_ \ || |
// |____\_,_|_|\_,_|_.__/\_,_|_.__/\_,_|
// 
// Copyright © Lulububu Software GmbH - All Rights Reserved
// https://lulububu.de
// 
// Unauthorized copying of this file, via any medium is strictly prohibited!
// Proprietary and confidential.

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
