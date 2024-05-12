<?php
/**
* @package		Komento
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="panel">
	<div class="panel-head">
		<b><?php echo JText::_('COM_KOMENTO_MIGRATOR_PROGRESS');?></b>
        <span data-progress-loading class="kt-loader-o size-sm hide"></span>
	</div>
	<div class="panel-body">
    	<div data-progress-empty><?php echo JText::_('COM_KOMENTO_MIGRATOR_NO_PROGRESS_YET'); ?></div>
    	<div data-progress-status style="overflow:auto; height:98%;max-height: 300px;"></div>
	</div>
</div>


