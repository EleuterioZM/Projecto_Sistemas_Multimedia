<?php

/**
 * @package         Convert Forms
 * @version         3.2.8 Free
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2020 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('_JEXEC') or die('Restricted access');
JHtml::_('bootstrap.popover');

?>

<div class="cf-templates">
	<div class="cf-templates-items">
	    <?php foreach ($this->templates as $key => $templateGroup) { ?>
			<div class="cf-template-group">
				<div class="cf-template-group-name">
					<?php echo JText::_("COM_CONVERTFORMS_TEMPLATE_GROUP_" . $key); ?>
				</div>
				<div class="cf-template-group-items cf-template-group-<?php echo $key ?>">
					<?php foreach ($templateGroup as $template) { ?>
					    <div class="cf-template">
							<div>
								<a href="<?php echo $template["link"]; ?>"
									class="hasPopover"
									data-placement="top"
									data-content="<?php echo $template["label"] ?>">
									<img width="100%" height="auto" src="<?php echo $template["thumb"]; ?>"/>
								</a>
					    	</div>
					    </div>
					<?php } ?>
				</div>
			</div>
	    <?php } ?>
	</div>
</div>

<script>
	jQuery(function($) {
		$(".cf-templates a").click(function(event) {
			event.preventDefault();
			window.parent.location = $(this).attr("href");
			window.parent.jModalClose();
		});
	})
</script>