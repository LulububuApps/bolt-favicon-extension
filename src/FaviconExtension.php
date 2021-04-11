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

namespace Bolt\Extension\Lulububu\FaviconExtension;

use Bolt\Application;
use Bolt\Filesystem\Filesystem;
use Bolt\Filesystem\Handler\File;
use Bundle\Site\BaseExtension;
use Imagick;
use ImagickPixel;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class FaviconExtension
 *
 * @author Joshua Schumacher <joshua@lulububu.de>
 */
class FaviconExtension extends BaseExtension
{
    /**
     * @param ControllerCollection $collection
     * @return array
     */
    protected function betterRegisterFrontendRoutes(ControllerCollection $collection)
    {
        return [
            'callbackManifestJson'          => '/manifest.json',
            'callbackManifestWebApp'        => '/manifest.webapp',
            'callbackBrowserConfig'         => '/browserconfig.xml',
            'callbackYandexBrowserManifest' => '/yandex-browser-manifest.json',
            'callbackFaviconIco'            => [
                '/favicon.ico',
                '/favicon-{size}.ico',
            ],
            'callbackFavicon'               => [
                '/android-chrome-{size}.png',
                '/apple-icon-{size}.png',
                '/apple-touch-startup-image-{size}.png',
                '/coast-{size}.png',
                '/favicon-{size}.png',
                '/firefox_app_{size}.png',
                '/mstile-{size}.png',
                '/yandex-browser-{size}.png',
            ],
        ];
    }

    /**
     * @param Application $app
     * @param Request $request
     * @param $size
     * @return RedirectResponse
     * @throws \ImagickException
     */
    public function callbackFavicon(Application $app, Request $request, $size = '32x32')
    {
        $filename = $this->getUploadedFaviconFilename($app, $size);

        if ($filename) {
            return new RedirectResponse('/thumbs/' . $size . 'r/' . $filename);
        }

        return new RedirectResponse('theme/app/images/meta/favicons/favicon-' . $size . '.png');
    }

    /**
     * @param Application $app
     * @param Request $request
     * @return RedirectResponse
     * @throws \ImagickException
     */
    public function callbackFaviconIco(Application $app, Request $request, $size = '32x32')
    {
        $filename = $this->getUploadedFaviconFilename($app, $size, true);

        if ($filename) {
            return new RedirectResponse('/files/' . $filename);
        }

        return new RedirectResponse('theme/app/images/meta/favicons/favicon.ico');
    }

    /**
     * @return Response
     */
    public function callbackBrowserConfig()
    {
        return $this->getXMLResponseForTemplatePath('browserconfig.xml.twig');
    }

    /**
     * @return Response
     */
    public function callbackYandexBrowserManifest()
    {
        return $this->getJSONManifestResponseForTemplatePath('yandex-browser-manifest.json.twig');
    }

    /**
     * @return Response
     */
    public function callbackManifestJson()
    {
        return $this->getJSONManifestResponseForTemplatePath('manifest.json.twig');
    }

    /**
     * @param $templatePath
     * @return Response
     */
    protected function getJSONManifestResponseForTemplatePath($templatePath)
    {
        return $this->getResponseForTemplatePath(
            $templatePath,
            'application/manifest+json'
        );
    }

    /**
     * @param $templatePath
     * @return Response
     */
    protected function getXMLResponseForTemplatePath($templatePath)
    {
        return $this->getResponseForTemplatePath(
            $templatePath,
            'application/xml'
        );
    }

    /**
     * @param $templatePath string
     * @param $contentType string
     * @return Response
     */
    protected function getResponseForTemplatePath($templatePath, $contentType)
    {
        $manifest = $this->renderTemplate($templatePath);

        return Response::create(
            $manifest,
            Response::HTTP_OK,
            [
                'Content-Type' => $contentType,
            ]
        );
    }

    /**
     * @return Response
     */
    public function callbackManifestWebApp()
    {
        $manifest = $this->renderTemplate('manifest.webapp.twig');

        return Response::create(
            $manifest,
            Response::HTTP_OK,
            [
                'Content-Type' => 'application/manifest+json',
            ]
        );
    }

    /**
     * @param $app
     * @param $size
     * @param false $ico
     * @return false
     * @throws \ImagickException
     */
    private function getUploadedFaviconFilename($app, $size, $ico = false)
    {
        $settingsRepository = $app['storage']->getRepository('settings');
        $settings           = $settingsRepository->findAll()[0];
        $faFavicon          = $settings->getFaFavicon();

        if ($faFavicon) {
            return $this->getUploadedFaFavicons($app, $settings, $ico, $size, $faFavicon);
        }

        return $this->getUploadedImageFavicons($settings, $ico);
    }

    /**
     * @param $settings
     * @param $ico
     * @return false
     */
    protected function getUploadedImageFavicons($settings, $ico)
    {
        if ($ico) {
            $file = $settings->getFaviconIco();
        } else {
            $file = $settings->getFavicon();
        }

        if ($file) {
            return $file['file'];
        }

        return false;
    }

    /**
     * @param $app
     * @param $settings
     * @param $ico
     * @param $size
     * @param $faFavicon
     * @return false|string|Response
     * @throws \ImagickException
     */
    protected function getUploadedFaFavicons($app, $settings, $ico, $size, $faFavicon)
    {
        $filesystem = $app['filesystem']->getFilesystem('files');
        $filePath   = 'favicon/fa/' . $size . '/' . str_replace('.svg', $ico ? '.ico' : '.png', $faFavicon);
        $file       = $this->generatedIconExists($filesystem, $filePath);

        if ($file) {
            return $file->getPath();
        }

        if ($ico) {
            $file = $this->generateFontAwesomeIconIco($settings, $faFavicon, $size, $filePath, $filesystem);
        } else {
            $file = $this->generateFontAwesomeIconPng($settings, $faFavicon, $size, $filePath, $filesystem);
        }

        return $file;
    }

    /**
     * @param $settings
     * @param $faIconName
     * @param $size
     * @param $filePath
     * @param Filesystem $filesystem
     * @return string
     * @throws \ImagickException
     */
    private function generateFontAwesomeIconIco($settings, $faIconName, $size, $filePath, Filesystem $filesystem)
    {
        return $this->generateFaIcon($settings, $faIconName, $size, $filePath, $filesystem, IconType::ICO);
    }

    /**
     * @param $settings
     * @param $faIconName
     * @param $size
     * @param $filePath
     * @param Filesystem $filesystem
     * @return string
     * @throws \ImagickException
     */
    private function generateFontAwesomeIconPng($settings, $faIconName, $size, $filePath, Filesystem $filesystem)
    {
        return $this->generateFaIcon($settings, $faIconName, $size, $filePath, $filesystem, IconType::PNG32);
    }

    /**
     * @param Filesystem $filesystem
     * @param $filePath
     * @return false|File
     */
    private function generatedIconExists(Filesystem $filesystem, $filePath)
    {
        if ($filesystem->has($filePath)) {
            return $filesystem->get($filePath);
        }

        return false;
    }

    /**
     * @param $settings
     * @param $faIconName
     * @param $size
     * @param $filePath
     * @param Filesystem $filesystem
     * @param $fileformat
     * @return string
     * @throws \ImagickException
     */
    private function generateFaIcon($settings, $faIconName, $size, $filePath, Filesystem $filesystem, $fileformat)
    {
        $icon        = file_get_contents(__DIR__ . '/../../font-awesome-favicon-field-type/icons/' . $faIconName);
        $widthHeight = explode('x', $size);
        $image       = new Imagick();
        $color       = $settings->getTilecolor();

        if ($color) {
            $icon = str_replace('<svg ', '<svg fill="' . $color . '" ', $icon);
        }

        $image->setBackgroundColor(new ImagickPixel('transparent'));
        $image->readImageBlob($icon);
        $image->setImageFormat($fileformat);
        $image->scaleImage($widthHeight[0], $widthHeight[1], true);
        $filesystem->write($filePath, $image);
        $image->clear();
        $image->destroy();

        return $filePath . $image;
    }
}
