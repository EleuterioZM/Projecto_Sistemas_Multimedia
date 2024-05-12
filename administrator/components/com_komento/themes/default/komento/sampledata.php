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
<div class="grid grid-cols-1 md:grid-cols-12 gap-md">
	<div class="col-span-1 md:col-span-7">
		<p>Page to generate sample comments on Joomla articles.</p>

		<?php if ($totalComments) { ?>
			<p style="color:green;">Total of <?php echo $totalComments; ?> comments added into Komento.</p>
		<?php } ?>

		<form name="frmSample" action="index.php" method="post">
			<p>Enter the number of comments to be generated for each articles. Please do not set too high to avoid PHP timeout.</p>

			Number of comments: &nbsp;<input type="text" value="50" name="frmMax" /> 
			<br /><br />
			<button>Submit</button>

			<?php echo $this->fd->html('form.action', '', '', 'komento'); ?>
			<?php echo $this->fd->html('form.hidden', 'layout', 'sampledata'); ?>
		</form>
	</div>
	
	<div class="col-span-1 md:col-span-5">
	</div>
</div>


