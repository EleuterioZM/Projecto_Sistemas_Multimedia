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

JFormHelper::loadFieldClass('subform');

class JFormFieldCFSubform extends JFormFieldSubform
{
	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   3.6
	 */
	protected function getInput()
	{
		// The following script toggles the required attribute for all Email Notification options.
		JFactory::getDocument()->addScriptDeclaration('
			jQuery(function($) {
				$("input[name=\'jform[sendnotifications]\']").on("change", function() {
					var enabled = $(this).is(":checked");
					var exclude_fields = $("input[id*=reply_to], input[id$=attachments]");
					var fields = $("#behavior-emails .subform-repeatable-group").find("input, textarea").not(exclude_fields);

					if (enabled) {
						fields.attr("required", "required").addClass("required");
					} else {
						fields.removeAttr("required").removeClass("required");
					}
				});
			});
		');

		return parent::getInput();
	}
}
