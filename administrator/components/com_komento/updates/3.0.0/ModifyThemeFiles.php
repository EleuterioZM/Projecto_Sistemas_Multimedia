<?php
/**
* @package      Komento
* @copyright    Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

KT::import('admin:/includes/maintenance/dependencies');

class KomentoMaintenanceScriptModifyThemeFiles extends KomentoMaintenanceScript
{
    public static $title = 'Modify Theme Files';
    public static $description = 'Update theme files and settings';

    public function main()
    {
        $state = true;

        // elego -> elegant
        // kuro -> outline
        // bubbo -> bubbles
        // freso -> remove
        // minimo -> vortex

        // List of themes that needs to be renamed
        $removableThemes = array('elego', 'kuro', 'bubbo', 'freso', 'minimo');

        foreach ($removableThemes as $theme) {
            $folder = JPATH_ROOT . '/components/com_komento/themes/' . $theme;
            
            if (JFolder::exists($folder)) {
                $state = JFolder::move($folder, $folder . '.ignore');
            }
        }
        
        return $state;
    }
}
