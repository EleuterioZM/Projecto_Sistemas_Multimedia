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

class PlgButtonConvertforms extends JPlugin
{
	/**
	 * Load the language file on instantiation.
	 *
	 * @var    boolean
	 */
	protected $autoloadLanguage = true;

    /**
     *  Application Object
     *
     *  @var  object
     */
    protected $app;

	/**
	 * ConvertForms Button
	 *
	 * @param  string  $name  The name of the button to add
	 *
	 * @return JObject  The button object
	 */
	public function onDisplay($name)
	{
		$component = $this->app->input->getCmd('option');
		$basePath  = $this->app->isClient('administrator') ? '' : 'administrator/';
		$link      = $basePath . 'index.php?option=com_convertforms&amp;view=editorbutton&amp;layout=button&amp;tmpl=component&e_name=' . $name . '&e_comp='. $component;

		$button          = new JObject;
		$button->modal   = true;
		$button->class   = 'btn cf';
		$button->link    = $link;
		$button->text    = JText::_('PLG_EDITORS-XTD_CONVERTFORMS_BUTTON_TEXT');
		$button->name    = 'vcard';

		if (defined('nrJ4'))
		{
			$button->options = [
				'height'     => '200px',
				'bodyHeight' => '180px',
				'modalWidth' => '250px',
			];
		} else 
		{
			$button->options = "{handler: 'iframe', size: {x: 350, y: 220}}";
		}

		return $button;
	}
}