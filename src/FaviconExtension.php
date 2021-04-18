<?php

declare(strict_types=1);

namespace Lulububu\FaviconExtension;

use Lulububu\BaseExtension\BaseExtension;
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
            'Lulububu\FaviconExtension\Controller\FaviconController::manifestJson'          => [
                'manifestJson' => '/manifest.json',
            ],
            'Lulububu\FaviconExtension\Controller\FaviconController::manifestWebApp'        => [
                'manifestWebApp' => '/manifest.webapp',
            ],
            'Lulububu\FaviconExtension\Controller\FaviconController::browserConfig'         => [
                'browserConfig' => '/browserconfig.xml',
            ],
            'Lulububu\FaviconExtension\Controller\FaviconController::yandexBrowserManifest' => [
                'manifestYandex' => '/yandex-browser-manifest.json',
            ],
            'Lulububu\FaviconExtension\Controller\FaviconController::faviconIco'            => [
                'faviconIco'     => '/favicon.ico',
                'faviconIcoSize' => '/favicon-{size}.ico',
            ],
            'Lulububu\FaviconExtension\Controller\FaviconController::faviconPng'            => [
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
