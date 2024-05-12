<?php
/**
* @package      Komento
* @copyright    Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

/**
 * Editor KomentoLock buton
 *
 * @package		Joomla.Plugin
 * @subpackage	Editors-xtd.KomentoLock
 * @since 1.6
 */
class plgButtonKomentoLock extends JPlugin
{
	/**
	 * Constructor
	 *
	 * @access      protected
	 * @param       object  $subject The object to observe
	 * @param       array   $config  An array that holds the plugin configuration
	 * @since       1.6
	 */
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();

		FH::loadLanguage('com_komento');
	}

	/**
	 * KomentoLock button
	 * @return array A two element array of (imageName, textToInsert)
	 */
	public function onDisplay($name, $asset, $author)
	{
		$app = JFactory::getApplication();

		$doc = JFactory::getDocument();
		$template = $app->getTemplate();

		// button is not active in specific content components
		$getContent = FH::isJoomla4() ? 'Joomla.editors.instances["jform_articletext"].getValue()' : $this->_subject->getContent($name);
		$present = JText::_('KomentoLock is already added.', true) ;
		$js = "
			function insertKomentoLock(editor) {
				const content = $getContent;

				if (content.match(/\{KomentoLock\}/)) {
					alert('$present');
					return false;
				} else {
					Joomla.editors.instances[editor].replaceSelection('{KomentoLock}');
				}
			}
			";

		$doc->addScriptDeclaration($js);

		$button = new JObject;
		$button->set('modal', false);
		$button->set('onclick', 'insertKomentoLock(\''.$name.'\');return false;');
		$button->set('text', JText::_('COM_KT_PLUGIN_LOCK_BUTTON'));
		$button->set('name', 'lock');
		// TODO: The button writer needs to take into account the javascript directive
		//$button->set('link', 'javascript:void(0)');
		$button->set('link', '#');

		return $button;
	}
}
