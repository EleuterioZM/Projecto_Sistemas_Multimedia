<?php

namespace Nextend\SmartSlider3\Platform\Joomla;

use Exception;
use JEventDispatcher;
use Joomla\CMS\Factory;
use Joomla\Event\Event;
use Joomla\CMS\Plugin\PluginHelper;
use Nextend\Framework\Pattern\SingletonTrait;
use Nextend\Security\Joomla\JoomlaSecurity;
use Nextend\SmartSlider3\Settings;
use ReflectionFunction;

class JoomlaShim {

    use SingletonTrait;

    public static $isJoomla4 = 0;

    protected function init() {
        self::$isJoomla4 = version_compare(JVERSION, '4', '>=') ? 1 : 0;

        JoomlaSecurity::getInstance();
    }

    public static function getDispatcher() {
        if (!self::$isJoomla4) {
            return JEventDispatcher::getInstance();
        }

        return Factory::getApplication()
                      ->getDispatcher();
    }

    private static function getExcludedContentPlugins() {
        static $excludedPlugins;
        if ($excludedPlugins === null) {

            $excludedPlugins   = explode('||', Settings::get('joomla-plugins-content-excluded', ''));
            $excludedPlugins[] = 'plgcontentemailcloak';
            $excludedPlugins[] = 'plgcontentdropeditor';
            $excludedPlugins[] = 'plgcontentshortcode_ultimate';
            $excludedPlugins[] = 'plgcontentarkcontent';
            $excludedPlugins[] = 'plgcontentosyoutube';
            $excludedPlugins[] = 'plgsystemt4';
            $excludedPlugins[] = 'plgcontentfields';
        }

        return $excludedPlugins;
    }

    public static function triggerOnContentPrepare($data) {
        static $contentPluginsEnabled, $pluginsToRun = array();

        PluginHelper::importPlugin('content');

        if (!self::$isJoomla4) {
            if ($contentPluginsEnabled === null) {
                $contentPluginsEnabled = intval(Settings::get('joomla-plugins-content-enabled', 1));

                if ($contentPluginsEnabled) {
                    $classNames = array();
                    foreach (PluginHelper::getPlugin('content') as $plugin) {
                        $classNames[] = strtolower('Plg' . $plugin->type . $plugin->name);
                    }

                    $classNames = array_diff($classNames, self::getExcludedContentPlugins());

                    $dispatcher = JEventDispatcher::getInstance();

                    $observers = $dispatcher->get('_observers');

                    foreach ($observers as $observer) {
                        if (is_object($observer)) {
                            $className = strtolower(get_class($observer));
                            if (in_array($className, $classNames)) {
                                $pluginsToRun[] = $observer;
                            } else if (method_exists($observer, 'onContentPrepare') && !in_array($className, self::getExcludedContentPlugins())) {
                                $pluginsToRun[] = $observer;
                            }
                        }
                    }
                }
            }

            if ($contentPluginsEnabled && !empty($pluginsToRun)) {
                foreach ($pluginsToRun as $observer) {
                    // Joomla removes it in every update
                    $data['event'] = 'oncontentprepare';
                    $observer->update($data);
                }
            }

            return true;
        }


        if ($contentPluginsEnabled === null) {
            $contentPluginsEnabled = intval(Settings::get('joomla-plugins-content-enabled', 1));
            if ($contentPluginsEnabled) {

                $classNames = array();
                foreach (PluginHelper::getPlugin('content') as $plugin) {
                    $classNames[] = strtolower('Plg' . $plugin->type . $plugin->name);
                }

                $classNames = array_diff($classNames, self::getExcludedContentPlugins());

                $dispatcher = Factory::getApplication()
                                     ->getDispatcher();

                $listeners = $dispatcher->getListeners('onContentPrepare');

                foreach ($listeners as $listener) {
                    if (is_array($listener)) {
                        if (is_callable($listener) && is_object($listener[0])) {

                            $className = strtolower(get_class($listener[0]));
                            if (in_array($className, $classNames)) {
                                $pluginsToRun[] = $listener;
                            }
                        }
                    } else {

                        $fn        = new ReflectionFunction($listener);
                        $fnClosure = $fn->getClosureThis();

                        if (is_object($fnClosure)) {
                            $className = strtolower(get_class($fnClosure));
                            if (in_array($className, $classNames)) {
                                $pluginsToRun[] = $listener;
                            }
                        }
                    }
                }
            }
        }

        if ($contentPluginsEnabled && !empty($pluginsToRun)) {
            $event = new Event('onContentPrepare', $data);
            foreach ($pluginsToRun as $callable) {

                try {
                    call_user_func($callable, $event);
                } catch (Exception $e) {
                }
            }
        }

        return true;
    }

    public static function getOnContentPreparePluginsList() {
        if (!self::$isJoomla4) {

            PluginHelper::importPlugin('content');

            $dispatcher = JEventDispatcher::getInstance();

            $classNames = array();
            foreach ($dispatcher->get('_observers') as $observer) {
                if ((is_object($observer) || (is_string($observer) && class_exists($observer))) && method_exists($observer, 'onContentPrepare')) {
                    $className              = strtolower(get_class($observer));
                    $classNames[$className] = $className;
                }
            }

            foreach (PluginHelper::getPlugin('content') as $plugin) {
                $className              = strtolower('Plg' . $plugin->type . $plugin->name);
                $classNames[$className] = ucfirst($plugin->name);
            }

            return $classNames;
        }

        PluginHelper::importPlugin('content');

        $dispatcher = Factory::getApplication()
                             ->getDispatcher();

        $classNames = array();
        foreach ($dispatcher->getListeners('onContentPrepare') as $listener) {

            if (is_array($listener)) {
                if (is_callable($listener) && is_object($listener[0])) {

                    $className              = strtolower(get_class($listener[0]));
                    $classNames[$className] = $className;
                }
            } else {
                $fn        = new ReflectionFunction($listener);
                $fnClosure = $fn->getClosureThis();

                if (is_object($fnClosure)) {
                    $className              = strtolower(get_class($fnClosure));
                    $classNames[$className] = $className;
                }
            }
        }

        foreach (PluginHelper::getPlugin('content') as $plugin) {
            $className              = strtolower('Plg' . $plugin->type . $plugin->name);
            $classNames[$className] = ucfirst($plugin->name);
        }

        return $classNames;
    }

    public static function triggerEvent($eventName, $args = array()) {
        if (!self::$isJoomla4) {
            $dispatcher = JEventDispatcher::getInstance();

            return $dispatcher->trigger('onInitN2Library', $args);
        }

        return Factory::getApplication()
                       ->triggerEvent($eventName, $args);
    }

    public static function loadComContentRoute() {
        if (!self::$isJoomla4) {
            require_once JPATH_ROOT . '/components/com_content/helpers/route.php';
        }
    }

    public static function mediaLibraryUrl() {
        if (!self::$isJoomla4) {
            return 'index.php?option=com_media&view=images&tmpl=component&asset=com_content&author=&fieldid=notused&folder=';
        }

        return 'index.php?option=com_media&tmpl=component&asset=86&author=584&fieldid={field-media-id}&path=local-0:/';
    }
}

JoomlaShim::getInstance();