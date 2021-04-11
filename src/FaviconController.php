<?php

declare(strict_types=1);

namespace Lulububu\FaviconExtension;

use App\Service\SettingsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class FaviconController
 *
 * @author Joshua Schumacher <joshua@lulububu.de>
 * @package Lulububu\FaviconExtension
 */
class FaviconController extends AbstractController
{
    /**
     * @var SettingsService $settingsService
     */
    protected $settingsService;

    /**
     * FaviconController constructor.
     *
     * @param SettingsService $settingsServie
     */
    public function __construct(SettingsService $settingsService)
    {
        $this->settingsService = $settingsService;
    }

    /**
     * @return Response
     */
    public function manifestJson()
    {
        return $this->getJSONManifestResponseForTemplatePath('@favicon-extension/manifest.json.twig');
    }

    /**
     * @return Response
     */
    public function manifestWebApp()
    {
        return $this->getJSONManifestResponseForTemplatePath('@favicon-extension/manifest.webapp.twig');
    }

    /**
     * @return Response
     */
    public function browserConfig()
    {
        return $this->getXMLResponseForTemplatePath('@favicon-extension/browserconfig.xml.twig');
    }

    /**
     * @return Response
     */
    public function yandexBrowserManifest()
    {
        return $this->getJSONManifestResponseForTemplatePath('@favicon-extension/yandex-browser-manifest.json.twig');
    }

    /**
     * @param false $size
     * @return RedirectResponse
     */
    public function faviconIco($size = false): RedirectResponse
    {
        $filename = $this->getUploadedFaviconFilename(true);

        if ($filename) {
            return new RedirectResponse($filename);
        }

        return new RedirectResponse('theme/app/images/meta/favicons/favicon.ico');
    }

    /**
     * @param string $size
     * @return RedirectResponse
     */
    public function faviconPng(string $size = '32x32'): RedirectResponse
    {
        $filename = $this->getUploadedFaviconFilename();

        if ($filename) {
            return new RedirectResponse('/thumbs/' . $size . '/' . $filename);
        }

        return new RedirectResponse('theme/app/images/meta/favicons/favicon-' . $size . '.png');
    }

    /**
     * @param bool $ico
     * @return string|null
     */
    private function getUploadedFaviconFilename(bool $ico = false): ?string
    {
        $settings = $this->settingsService->getSettings();

        /* TODO: FA-Favicons
        $faFavicon          = $settings->getFaFavicon();

        if ($faFavicon) {
            return $this->getUploadedFaFavicons($app, $settings, $ico, $size, $faFavicon);
        }
        */

        return $this->getUploadedImageFavicons($settings, $ico);
    }

    /**
     * @param $settings
     * @param $ico
     * @return string|null
     */
    protected function getUploadedImageFavicons($settings, $ico): ?string
    {
        if ($ico) {
            $file = $settings->getFieldValue('favicon_ico');
        } else {
            $file = $settings->getFieldValue('favicon');
        }

        if ($file) {
            return $file['path'];
        }

        return null;
    }

    /**
     * @param string $templatePath
     * @return Response
     */
    protected function getJSONManifestResponseForTemplatePath(string $templatePath): Response
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
    protected function getXMLResponseForTemplatePath($templatePath): Response
    {
        return $this->getResponseForTemplatePath(
            $templatePath,
            'application/xml'
        );
    }

    /**
     * @param string $templatePath
     * @param string $contentType
     * @return Response
     */
    protected function getResponseForTemplatePath(string $templatePath, string $contentType): Response
    {
        $manifest = $this->renderView($templatePath);

        return Response::create(
            $manifest,
            Response::HTTP_OK,
            [
                'Content-Type' => $contentType,
            ]
        );
    }

    /* TODO: FA-Favicons
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

    private function generatedIconExists(Filesystem $filesystem, $filePath)
    {
        if ($filesystem->has($filePath)) {
            return $filesystem->get($filePath);
        }

        return false;
    }

    private function generateFontAwesomeIconIco($settings, $faIconName, $size, $filePath, Filesystem $filesystem)
    {
        return $this->generateFaIcon($settings, $faIconName, $size, $filePath, $filesystem, IconType::ICO);
    }

    private function generateFontAwesomeIconPng($settings, $faIconName, $size, $filePath, Filesystem $filesystem)
    {
        return $this->generateFaIcon($settings, $faIconName, $size, $filePath, $filesystem, IconType::PNG32);
    }

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
    */
}