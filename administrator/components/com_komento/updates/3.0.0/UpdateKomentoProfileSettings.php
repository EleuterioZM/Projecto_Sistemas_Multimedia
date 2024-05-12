<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

KT::import('admin:/includes/maintenance/dependencies');

class KomentoMaintenanceScriptUpdateKomentoProfileSettings extends KomentoMaintenanceScript
{
    public static $title = 'Update Komento Profile Settings.';
    public static $description = 'Migrate Komento Profile setting into profile avatar integration.';

    public function main()
    {
        $state = true;
        $config = KT::config();

        $komentoProfile = $config->get('use_komento_profile', null);

        if (!is_null($komentoProfile) && $komentoProfile) {
            // cdn disabled. lets remove the cdn url.
            $config->set('layout_avatar_integration', 'default');
            $config->set('use_komento_profile', '0');

            // Convert the config object to a json string.
            $jsonString = $config->toString();

            $configTable = KT::table('Configs');
            $configTable->load('config');

            $configTable->params = $jsonString;

            $state = $configTable->store();
        }

        return $state;
    }
}
