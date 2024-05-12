<?php

class com_SmartSlider3InstallerScript {

    /**
     * The minimum PHP version required to install this extension
     *
     * @var   string
     */
    protected $minimumPHPVersion = '7.0.0';

    /**
     * The minimum Joomla! version required to install this extension
     *
     * @var   string
     */
    protected $minimumJoomlaVersion = '3.9.0';


    /**
     * Joomla! pre-flight event. This runs before Joomla! installs or updates the package. This is our last chance to
     * tell Joomla! if it should abort the installation.
     *
     * In here we'll try to install FOF. We have to do that before installing the component since it's using an
     * installation script extending FOF's InstallScript class. We can't use a <file> tag in the manifest to install FOF
     * since the FOF installation is expected to fail if a newer version of FOF is already installed on the site.
     *
     * @param string                                      $type   Installation type (install, update, discover_install)
     * @param Joomla\CMS\Installer\Adapter\PackageAdapter $parent Parent object
     *
     * @return  boolean  True to let the installation proceed, false to halt the installation
     */
    public function preflight($type, $parent) {

        // Check the minimum PHP version
        if (!version_compare(PHP_VERSION, $this->minimumPHPVersion, 'ge')) {
            $msg = "<p>You need PHP $this->minimumPHPVersion or later to install this package</p>";
            Joomla\CMS\Log\Log::add($msg, Joomla\CMS\Log\Log::WARNING, 'jerror');

            return false;
        }

        // Check the minimum Joomla! version
        if (!version_compare(JVERSION, $this->minimumJoomlaVersion, 'ge')) {
            $msg = "<p>You need Joomla! $this->minimumJoomlaVersion or later to install this component</p>";
            Joomla\CMS\Log\Log::add($msg, Joomla\CMS\Log\Log::WARNING, 'jerror');

            return false;
        }
    }

    /**
     *
     * @param Joomla\CMS\Installer\Adapter\PackageAdapter $parent
     */
    public function install($parent) {

        $this->installOrUpdate($parent);

        $parent->getParent()
               ->setRedirectURL('index.php?option=com_smartslider3');
    }

    /**
     *
     * @param Joomla\CMS\Installer\Adapter\PackageAdapter $parent
     */
    public function uninstall($parent) {

    }

    /**
     *
     * @param Joomla\CMS\Installer\Adapter\PackageAdapter $parent
     */
    public function update($parent) {

        $this->installOrUpdate($parent);

        $parent->getParent()
               ->setRedirectURL('index.php?option=com_smartslider3');
    }

    /**
     *
     * @param Joomla\CMS\Installer\Adapter\PackageAdapter $parent
     */
    protected function installOrUpdate($parent) {

        $sourcePath = $parent->getParent()
                             ->getPath('source');

        $this->installFromPath($sourcePath . '/lib_smartslider3');
        $this->installFromPath($sourcePath . '/mod_smartslider3');
        $this->installFromPath($sourcePath . '/plugins/installer/smartslider3');
        $this->installFromPath($sourcePath . '/plugins/system/smartslider3');


        if (!file_exists(JPATH_LIBRARIES . '/smartslider3/joomla.php')) {
            $this->deleteFolder(JPATH_LIBRARIES . '/smartslider3/');
        }

        $this->cleanup();
    }

    /**
     * Cleanup old version
     */
    private function cleanup() {

        $db = Joomla\CMS\Factory::getDBO();

        $db->setQuery("DELETE FROM #__assets WHERE name LIKE 'com_nextend2'")
           ->execute();
        $db->setQuery("DELETE FROM #__assets WHERE name LIKE 'com_nextend_installer'")
           ->execute();

        $db->setQuery("DELETE FROM #__extensions WHERE type='component' AND element LIKE 'com_nextend2'")
           ->execute();
        $db->setQuery("DELETE FROM #__extensions WHERE type='plugin' AND folder LIKE 'system' AND element LIKE 'nextendsmartslider3'")
           ->execute();
        $db->setQuery("DELETE FROM #__extensions WHERE type='plugin' AND folder LIKE 'system' AND element LIKE 'nextend2'")
           ->execute();

        $this->deleteFolder(JPATH_SITE . '/libraries/nextend2');
        $this->deleteFolder(JPATH_SITE . '/components/com_nextend2');
        $this->deleteFolder(JPATH_SITE . '/media/n2');
        $this->deleteFolder(JPATH_SITE . '/plugins/system/nextendsmartslider3');
        $this->deleteFolder(JPATH_SITE . '/plugins/system/nextend2');

        $this->deleteFolder(JPATH_ADMINISTRATOR . '/components/com_nextend2');
        $this->deleteFolder(JPATH_ADMINISTRATOR . '/components/com_nextend_installer');
        $proInvert = 1;
    

        // We must delete the stucked update sites if upgrade to pro or downgrade to free
        $updateSites = $db->setQuery("SELECT update_site_id FROM #__update_sites WHERE location LIKE '%product=smartslider3%'")
                          ->loadAssocList();

        if (!empty($updateSites)) {
            foreach ($updateSites as $updateSite) {
                $db->setQuery("DELETE FROM #__update_sites_extensions WHERE update_site_id = '" . $updateSite['update_site_id'] . "'")
                   ->execute();
                $db->setQuery("DELETE FROM #__update_sites WHERE update_site_id = '" . $updateSite['update_site_id'] . "'")
                   ->execute();
            }
        }

        $storageHasSystem = !empty($db->setQuery("SHOW COLUMNS FROM #__nextend2_section_storage LIKE 'system'")
                                      ->loadResult());

        if ($storageHasSystem) {
            $db->setQuery("ALTER TABLE `#__nextend2_section_storage` CHANGE  `system`  `isSystem` INT(11) NOT NULL DEFAULT '0'")
               ->execute();
        }
    }

    protected function installFromPath($path) {

        $installer = new Joomla\CMS\Installer\Installer();
        $installer->setOverwrite(true);

        if ($success = $installer->install($path)) {
            return true;
        }

        $error = Joomla\CMS\Language\Text::sprintf('JLIB_INSTALLER_ABORT_PACK_INSTALL_ERROR_EXTENSION', 'Smart Slider 3', $path) . ' Please <a href="https://smartslider3.com/contact-us/support/" target="_blank">contact us</a> with this error!</p>';
        throw new RuntimeException($error);

        return false;
    }

    /**
     * Runs after install, update or discover_update. In other words, it executes after Joomla! has finished installing
     * or updating your component. This is the last chance you've got to perform any additional installations, clean-up,
     * database updates and similar housekeeping functions.
     *
     * @param string                                      $type   install, update or discover_update
     * @param Joomla\CMS\Installer\Adapter\PackageAdapter $parent Parent object
     */
    public function postflight($type, $parent) {

        $db = Joomla\CMS\Factory::getDBO();
        $db->setQuery("UPDATE #__extensions SET enabled=1 WHERE type='plugin' AND folder LIKE 'system' AND element LIKE 'smartslider3'")
           ->execute();
        $db->setQuery("UPDATE #__extensions SET enabled=1 WHERE type='plugin' AND folder LIKE 'installer' AND element LIKE 'smartslider3'")
           ->execute();
        $pro = 0;
    

        if (function_exists('opcache_reset')) {
            opcache_reset();
        }
    }

    private function deleteFolder($path) {

        if (Joomla\CMS\Filesystem\Folder::exists($path)) {
            Joomla\CMS\Filesystem\Folder::delete($path);
        }
    }
}