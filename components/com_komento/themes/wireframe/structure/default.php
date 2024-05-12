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
<?php KT::trigger('onBeforeKomentoBox', ['component' => $component, 'cid' => $cid, 'system' => &$system, 'comments' => &$comments]); ?>

<div id="fd" data-kt-structure="<?php echo $identifier; ?>">
	<?php if ($type !== 'inline') { ?>
	<div id="kt" class="kt-frontend <?php echo $this->config->get('layout_appearance'); ?> si-theme-<?php echo $this->config->get('layout_accent');?>
		<?php echo $this->isMobile() ? ' is-mobile' : '';?>
		<?php echo $this->isTablet() ? ' is-tablet' : '';?>">

		<?php echo $this->fd->html('button.standard',
			JText::_('COM_KT_VIEW_COMMENTS') . ' (<span class="commentCounter" data-kt-counter>' . $commentCount . '</span>)',
			'default', 'sm', ['attributes' => 'data-kt-toggle', 'class' => 'w-full']);
		?>
	</div>
	<?php } ?>

	<?php echo $this->output('site/structure/container'); ?>

	

	<?php echo $this->fd->html('html.tooltip', $this->config->get('layout_appearance'), $this->config->get('layout_accent')); ?>
</div>