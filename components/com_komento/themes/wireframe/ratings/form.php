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
?>
<div class="kt-ratings-stars-forms flex items-center">
	<div class="kt-ratings-stars-forms__note pr-xs text-xs text-gray-500">
		<?php echo JText::_('COM_KT_RATE_THIS_POST');?>:
	</div>

	<?php echo $this->fd->html('rating.item', [
		'readOnly' => false,
		'score' => $score
	]); ?>
</div>
