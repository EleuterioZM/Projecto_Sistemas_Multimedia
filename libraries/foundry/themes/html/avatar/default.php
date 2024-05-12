<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="o-avatar <?php echo $class;?> <?php echo $style;?>">
	<div class="o-avatar__mobile"></div>

	<?php if ($useAnchorTag) { ?>
	<a class="o-avatar__content" 
		href="<?php echo $permalink; ?>"
		<?php echo $anchorAttributes; ?>
	>
	<?php } else { ?>
	<div class="o-avatar__content">
	<?php } ?>
		<img src="<?php echo $url; ?>" 
			alt="<?php echo $this->fd->html('str.escape', $name); ?>"
			width="<?php echo $width;?>"
			height="<?php echo $height;?>"

			<?php echo $attributes; ?>

			<?php if ($tooltip) { ?>
			data-<?php echo $this->fd->getShortName();?>-provide="tooltip"
			data-title="<?php echo $this->fd->html('str.escape', $name); ?>"
			<?php } ?>
		/>
	<?php if ($useAnchorTag) { ?>
	</a>
	<?php } else { ?>
	</div>
	<?php } ?>
</div>