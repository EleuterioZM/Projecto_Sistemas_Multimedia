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
<div class="o-empty <?php echo $class; ?>" <?php echo $attributes; ?>>
	<div class="o-empty__content">
		<?php if ($icon) { ?>
			<?php echo $this->fd->html('icon.font', 'o-empty__icon ' . $icon); ?>
		<?php } ?>

		<div class="o-empty__text"><?php echo JText::_($message);?></div>

		<?php if ($action && $actionMessage) { ?>
		<div class="o-empty__action">
			<a href="<?php echo $actionLink; ?>" class="o-btn o-btn--primary">
				<?php echo $actionMessage; ?>
			</a>
		</div>
		<?php } ?>
	</div>
</div>