<?php

declare(strict_types=1);

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

namespace Lulububu\FaviconExtension;

use App\Extension\BaseExtension;
use Symfony\Component\Routing\Route;

/**
 * Class FaviconExtension
 *
 * @author Joshua Schumacher <joshua@lulububu.de>
 * @package Lulububu\FaviconExtension
 */
class FaviconExtension extends BaseExtension
{
    /**
     * @return string
     */
    public function getName(): string
    {
        return 'Lulububu Favicon Extension';
    }

    /**
     * @return Route[]
     */
    protected function getBetterRoutes(): array
    {
        return [
            'Lulububu\FaviconExtension\FaviconController::manifestJson'          => [
                'manifestJson' => '/manifest.json',
            ],
            'Lulububu\FaviconExtension\FaviconController::manifestWebApp'        => [
                'manifestWebApp' => '/manifest.webapp',
            ],
            'Lulububu\FaviconExtension\FaviconController::browserConfig'         => [
                'browserConfig' => '/browserconfig.xml',
            ],
            'Lulububu\FaviconExtension\FaviconController::yandexBrowserManifest' => [
                'manifestYandex' => '/yandex-browser-manifest.json',
            ],
            'Lulububu\FaviconExtension\FaviconController::faviconIco'            => [
                'faviconIco'     => '/favicon.ico',
                'faviconIcoSize' => '/favicon-{size}.ico',
            ],
            'Lulububu\FaviconExtension\FaviconController::faviconPng'            => [
                'faviconPngAndroid'    => '/android-chrome-{size}.png',
                'faviconPngAppleIcon'  => '/apple-icon-{size}.png',
                'faviconPngAppleTouch' => '/apple-touch-startup-image-{size}.png',
                'faviconPngCoast'      => '/coast-{size}.png',
                'faviconPngSize'       => '/favicon-{size}.png',
                'faviconPngFirefox'    => '/firefox_app_{size}.png',
                'faviconPngMsTile'     => '/mstile-{size}.png',
                'faviconPngYandex'     => '/yandex-browser-{size}.png',
            ],
        ];
    }

    /**
     * @param false $cli
     */
    public function initialize($cli = false): void
    {
        $this->addTwigNamespace('favicon-extension');
    }
}
