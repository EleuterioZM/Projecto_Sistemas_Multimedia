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
<div class="row" data-fd-article="<?php echo $this->fd->getName();?>" data-id="<?php echo $id;?>">
	<div class="col-lg-10">
		<div class="o-input-group">
			<?php echo $this->fd->html('form.text', '', $articleTitle, $id . '-placeholder', ['disabled' => true]); ?>

			<?php echo $this->fd->html('button.standard', '', 'default', 'md', [
				'attributes' => 'data-fd-article-remove',
				'icon' => 'fdi fa fa-times',
				'outline' => true
			]); ?>

			<?php echo $this->fd->html('button.standard', 'FD_BROWSE', 'default', 'md', [
				'attributes' => 'data-fd-article-browse',
				'icon' => 'fdi fa fa-search',
				'outline' => true
			]); ?>
		</div>

		<?php echo $this->fd->html('form.hidden', $name, $value, $id, $attributes); ?>
	</div>
</div>
