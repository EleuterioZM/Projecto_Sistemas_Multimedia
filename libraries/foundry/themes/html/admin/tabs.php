<?php
/**
* @package		Foundry
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Foundry is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="app-tabs">
	<ul class="app-tabs-list" data-fd-tabs-header>
		<?php foreach ($tabs as $tab) { ?>
		<li class="tabItem <?php echo $tab->active ? 'is-active' : '';?>" data-fd-tab-header-item>
			<a href="#<?php echo $tab->id;?>" data-fd-tab data-form-tabs="<?php echo $tab->id;?>"><?php echo JText::_($tab->title);?></a>
		</li>
		<?php } ?>
	</ul>
</div>
