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
<dialog>
	<width>400</width>
	<height>260</height>
	<selectors type="json">
	{
		"{closeButton}": "[data-close-button]"
	}
	</selectors>
	<bindings type="javascript">
	{
		"{closeButton} click": function() {
			this.parent.close();
		}
	}
	</bindings>
	<title><?php echo JText::_('COM_KOMENTO_LIKES_USERS_DIALOG'); ?></title>
	<content>
		<div class="kt-name-list-wrapper">
			<div class="kt-name-list">
			<?php foreach ($users as $user) { ?>
				<div class="kt-name-list__item">
					<div class="o-flag">
						<div class="o-flag__image">
							<?php echo $this->html('html.avatar', $user); ?>
						</div>
						<div class="o-flag__body">
							<a href="<?php echo $user->getPermalink();?>" title="<?php echo $user->getName();?>">
								<?php echo $user->getName(); ?>
							</a>
						</div>
					</div>
				</div>
			<?php } ?>
			</div>
			<div class="o-loader o-loader--top"></div>
		</div>
	</content>
	<buttons>
		<?php echo $this->html('dialog.closeButton'); ?>
	</buttons>
</dialog>