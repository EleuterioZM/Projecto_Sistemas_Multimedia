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
<?php if ($active != 'complete') { ?>
<script>
$(document).ready( function(){

	var previous = $('[data-installation-nav-prev]'),
		active = $('[data-installation-form-nav-active]'),
		nav = $('[data-installation-form-nav]'),
		retry = $('[data-installation-retry]'),
		cancel = $('[data-installation-nav-cancel]'),
		loading = $('[data-installation-loading]');

	previous.on('click', function() {
		active.val(<?php echo $active;?> - 2);

		nav.submit();
	});

	cancel.on('click', function() {
		window.location = '<?php echo JURI::base();?>/index.php?option=<?php echo SI_IDENTIFIER;?>&cancelSetup=1';
	});

	retry.on('click', function() {
		var step = $(this).data('retry-step');

		$(this).addClass('hide');

		loading.removeClass('hide');

		window['eb']['installation'][step]();
	});
});
</script>

<form action="index.php?option=<?php echo SI_IDENTIFIER;?>" method="post" data-installation-form-nav class="hidden">
	<input type="hidden" name="active" value="" data-installation-form-nav-active />
	<input type="hidden" name="option" value="<?php echo SI_IDENTIFIER;?>" />

	<?php if (SI_INSTALLER == 'launcher') { ?>
	<input type="hidden" name="method" value="network" />
	<?php } ?>

	<?php if (SI_INSTALLER == 'full' || SI_BETA) { ?>
	<input type="hidden" name="method" value="directory" />
	<?php } ?>
</form>


<a href="javascript:void(0);" class="btn btn-outline-secondary" <?php echo $active > 1 ? ' data-installation-nav-prev' : ' data-installation-nav-cancel';?>>
	<span>
		&#8592; &nbsp;
	</span>

	<?php if ($active > 1) { ?>
		<?php echo JText::_('Previous'); ?>
	<?php } else { ?>
		<?php echo JText::_('Exit Installation'); ?>
	<?php } ?>
</a>

<a href="javascript:void(0);" class="btn btn-primary ml-auto px-4" data-installation-submit>
	<?php echo JText::_('Next'); ?>
	<span>
		&#8594; &nbsp;
	</span>
</a>

<a href="javascript:void(0);" class="btn btn-primary loading disabled ml-auto px-4 d-none" data-installation-loading>
	<?php echo JText::_('Loading'); ?>
	<span>
		<b class="ui loader"></b>
	</span>
</a>

<a href="javascript:void(0);" class="btn btn-primary ml-auto px-4 d-none" data-installation-install-addons>
	<?php echo JText::_('Install Addons'); ?>
	<span>
		&#8594; &nbsp;
	</span>
</a>

<a href="javascript:void(0);" class="btn btn-primary ml-auto px-4 d-none" data-installation-retry>
	<?php echo JText::_('Retry'); ?>
	<span>
		&#8594; &nbsp;
	</span>
</a>
<?php } ?>
