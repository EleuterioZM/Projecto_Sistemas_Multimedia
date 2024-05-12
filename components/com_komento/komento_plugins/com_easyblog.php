<?php
/**
* @package      Komento
* @copyright    Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

$file = JPATH_ADMINISTRATOR . '/components/com_easyblog/includes/easyblog.php';
$oldFile = JPATH_ROOT . '/components/com_easyblog/helpers/helper.php';

$exists = JFile::exists($file);

// If this file exist, we know this site is using Easyblog 5
if ($exists) {

	// include the file
	require_once(KOMENTO_ROOT . '/komento_plugins/com_easyblog5.php');

	class KomentoComeasyblog extends KomentoComeasyblog5
	{
		public function __construct($component)
		{
			parent::__construct($component);
		}
	}

} else if (JFile::exists($oldFile)) {

	// include the file
	require_once(KOMENTO_ROOT . '/komento_plugins/com_easyblog3.php');
	
	class KomentoComeasyblog extends KomentoComeasyblog3
	{
		public function __construct( $component )
		{
			parent::__construct( $component );
		}
	}
}

