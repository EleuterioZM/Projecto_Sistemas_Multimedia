<?php


namespace Nextend\SmartSlider3\Platform\Joomla\Plugin;

use Artx;
use ArtxPage;
use EshopHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Plugin\CMSPlugin;
use Nextend\Framework\Asset\AssetManager;
use Nextend\SmartSlider3\Platform\Joomla\Joomla3Assets;
use Nextend\SmartSlider3\Platform\Joomla\JoomlaShim;

jimport('joomla.plugin.plugin');

/**
 * Class PluginSmartSlider3
 *
 * Used in Joomla -> Plugin -> System -> Nextend2
 */
class PluginSmartSlider3 extends CMSPlugin {

    private $hadError = false;

    /*
    Artisteer jQuery fix
    */
    public function onAfterDispatch() {
        if (class_exists('Artx', true)) {
            Artx::load("Artx_Page");
            if (isset(ArtxPage::$inlineScripts)) {
                ArtxPage::$inlineScripts[] = '<script>if(typeof jQuery != "undefined") window.artxJQuery = jQuery;</script>';
            }
        }
    }

    private function isRenderAllowed() {
        static $isAllowed;

        if ($isAllowed === null) {
            /**
             * @var SiteApplication $application
             */
            $application = Factory::getApplication();
            if ($application->isClient('site')) {
                $request   = $application->input->request;
                $isAllowed = true;

                if (!Factory::getUser()->guest) {

                    if ($request->get('view') == 'form' && $request->get('layout') == 'edit' && $application->input->getInt('a_id') > 0) {
                        //Joomla frontend article editing
                        $isAllowed = false;

                    } else if ($request->get('option') == 'com_quix' && $request->get('layout') == 'edit' || $request->get('builder') == 'frontend') {
                        //Quix Visual Builder
                        $isAllowed = false;

                    } else if ($application->input->get('option') == 'com_sppagebuilder' && $application->input->get('layout') == 'edit') {
                        //SP Page Builder - @see SSDEV-3640
                        $isAllowed = false;
                    }
                }

                if ($request->get('type') == 'rss') {
                    //RSS feed
                    $isAllowed = false;
                }
            } else {
                $isAllowed = false;
            }
        }

        return $isAllowed;
    }

    private function displaySliders($output) {

        if (strpos($output, 'smartslider3[') !== false) {
            if (class_exists('\\EshopHelper', false) && EshopHelper::getConfigValue('rich_snippets') == '1') {
                $output = preg_replace_callback('/(<.*?>)?smartslider3\[([0-9]+)\]/', array(
                    self::class,
                    'cleanEshop'
                ), $output);
            }

            $output = preg_replace_callback('/smartslider3\[([0-9]+)\]/', array(
                self::class,
                'prepare'
            ), $output);
        }

        return $output;
    }

    private function onNextendBeforeCompileHead() {

        if ($this->isRenderAllowed()) {

            /**
             * @var SiteApplication $application
             */
            $application = Factory::getApplication();

            $body = $application->getBody();

            // Simple performance check to determine whether bot should process further
            if (strpos($body, 'smartslider3[') !== false) {


                $bodyParts = explode('</head>', $body);
                /**
                 * Last part is not the head
                 */
                $lastPart             = count($bodyParts) - 1;
                $bodyParts[$lastPart] = $this->displaySliders($bodyParts[$lastPart]);

                $application->setBody(implode('</head>', $bodyParts));
            }
        }
    }

    public function onAfterRender() {

        if (!JoomlaShim::$isJoomla4) {
            $joomla3Assets = new Joomla3Assets();

            $this->onNextendBeforeCompileHead();

            $joomla3Assets->process();
        } else {

            $this->onNextendBeforeCompileHead();
        }


        ob_start();
        if (class_exists('\\Nextend\\Framework\\Asset\\AssetManager', false)) {


            // PHPCS - Content already escaped
            echo AssetManager::getCSS(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

            // PHPCS - Content already escaped
            echo AssetManager::getJs(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        }
        $head = ob_get_clean();
        if (!empty($head)) {

            $application = Factory::getApplication();
            $body        = $application->getBody();

            $parts = preg_split('/<\/head>/', $body, 2);

            if (!JoomlaShim::$isJoomla4) {
                $head .= $joomla3Assets->renderHead();
            }

            $body = implode($head . '</head>', $parts);

            $application->setBody($body);
        }
    }

    public function onError() {
        $this->hadError = true;
    }

    public function onBeforeRespond() {
        if ($this->hadError) {
            $this->onAfterRender();
        }
    }

    public static function prepare($matches) {
        ob_start();
        nextend_smartslider3($matches[1]);

        return ob_get_clean();
    }

    public static function cleanEshop($matches) {
        if (strpos($matches[1], 'itemprop') !== false) {
            return $matches[1];
        }

        return $matches[0];
    }
}