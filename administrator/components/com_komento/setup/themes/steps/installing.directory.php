<?php
/**
* @package		Komento
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

$package = $input->get('package', '');
?>
<form name="installation" method="post" data-installation-form>
	<p class="section-desc">
		<?php echo JText::_('COM_KOMENTO_INSTALLATION_INSTALLING_DESC');?>
	</p>

	<div class="alert alert-success" data-installation-completed style="display: none;">
		<?php echo JText::_('COM_KOMENTO_INSTALLATION_INSTALLING_COMPLETED'); ?>
	</div>

	<div data-install-progress>
		<ol class="install-logs list-reset" data-progress-logs>
			<li class="active" data-progress-extract>
				<div class="progress-icon">
					<i class="icon-radio-unchecked"></i>
				</div>
				<div class="split__title"><?php echo JText::_('Extracting Files');?></div>
				<span class="progress-state text-info"><?php echo JText::_('Extracting');?></span>
			</li>

			<?php include(__DIR__ . '/installing.steps.php'); ?>
		</ol>
	</div>

	<input type="hidden" name="option" value="<?php echo KT_IDENTIFIER;?>" />
	<input type="hidden" name="active" value="<?php echo $active; ?>" />
	<input type="hidden" name="source" data-source />

	<?php if ($reinstall) { ?>
	<input type="hidden" name="reinstall" value="1" />
	<?php } ?>

	<?php if ($update) { ?>
	<input type="hidden" name="update" value="1" />
	<?php } ?>

</form>

<script type="text/javascript">
$(document).ready(function(){

	<?php if ($reinstall) { ?>
		kt.ajaxUrl = "<?php echo JURI::root();?>administrator/index.php?option=<?php echo KT_IDENTIFIER;?>&ajax=1&reinstall=1";
	<?php } ?>

	<?php if ($update) { ?>
		kt.ajaxUrl = "<?php echo JURI::root();?>administrator/index.php?option=<?php echo KT_IDENTIFIER;?>&ajax=1&update=1";
	<?php } ?>

	kt.installation.extract();
});
</script>