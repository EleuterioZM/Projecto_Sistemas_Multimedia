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

require_once(JPATH_ADMINISTRATOR . '/components/com_komento/includes/komento.php');

jimport('joomla.html.html');
jimport('joomla.html.parameter.element');
jimport('joomla.form.formfield');

class JFormFieldModal_KomentoComponent extends JFormField
{
	var $type = 'Modal_KomentoComponent';

	public function getInput()
	{
		$components = KT::components()->getAvailableComponents();

		ob_start();
		?>
		<select name="<?php echo $this->name;?>">
			<option value="all"<?php echo $this->value == 'all' ? ' selected="selected"' :'';?>>All</option>
			<?php foreach ($components as $component) { ?>
			<option value="<?php echo $component;?>"<?php echo $component == $this->value ? ' selected="selected"' : '';?>><?php echo KT::loadApplication($component)->getComponentName(); ?></option>
			<?php } ?>
		</select>
		<?php
		$html	= ob_get_contents();
		ob_end_clean();

		return $html;
	}
}