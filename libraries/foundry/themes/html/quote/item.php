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
<?php if (!$inline) { ?>
<div class="o-blockquote">
<?php } ?>

	<blockquote class="<?php echo $inline ? 'o-blockquote-markup' : ''; ?>">
		<div><?php echo $message; ?></div>

		<?php if ($author) { ?>
		<cite><?php echo JText::sprintf('FD_QUOTE_BY_AUTHOR', $author); ?></cite>
		<?php } ?>
	</blockquote>

<?php if (!$inline) { ?>
</div>
<?php } ?>