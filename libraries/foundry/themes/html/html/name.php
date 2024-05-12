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
<?php if ($useAnchorTag) { ?>
<a href="<?php echo $permalink; ?>" title="<?php echo FH::escape($name); ?>" class="fd-name <?php echo $class;?>" <?php echo $attributes;?>>
<?php } ?>

<?php if (!$useAnchorTag) { ?>
<span class="fd-name <?php echo $class;?>">
<?php } ?>

	<?php echo $name; ?>

<?php if (!$useAnchorTag) { ?>
</span>
<?php } ?>

<?php if ($useAnchorTag) { ?>
</a>
<?php } ?>

<?php if ($verified) { ?>
	<i class="fd-verified"></i>
<?php } ?>