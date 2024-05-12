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
<div class="kt-comments" data-kt-comments-pinned>
	<?php foreach ($pinnedComments as $pinnedComment) { ?>
		<?php echo $this->output('site/comments/item', ['comment' => $pinnedComment, 'pinned' => true, 'application' => $application]); ?>
	<?php } ?>
</div>
<hr class="kt-divider">