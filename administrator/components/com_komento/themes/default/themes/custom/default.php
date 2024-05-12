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
<form name="themeForm" method="post" action="index.php" id="adminForm">
	<div class="grid grid-cols-1 md:grid-cols-12 gap-md">
		<div class="col-span-1 md:col-span-12 w-auto">
			<div class="panel">
				<?php echo $this->fd->html('panel.heading', 'COM_KT_THEMES_CUSTOM_CSS_EDITOR'); ?>

				<div class="panel-body">
					<?php echo $editor->display('contents', $contents, '100%', '450px', 80, 20, false, null, null, null, array('syntax' => 'css', 'filter' => 'raw')); ?>
				</div>
			</div>
		</div>
	</div>
	
	<?php echo $this->fd->html('form.action', 'saveCustomCss', 'themes'); ?>
</form>