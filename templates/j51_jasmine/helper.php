<?php
class TplJ51JasmineHelper
{
	public static function importAjax()
	{
		self::checkAccess();
		$input = JFactory::getApplication()->input;
		$contents = $input->json->getRaw();
		// Check if it is json content
		$contents = @json_encode(@json_decode($contents));
		if (!$contents)
		{
			throw new Exception('No content available or an invalid format is uploaded');
		}
		$db = JFactory::getDBO();
		$db->setQuery('update #__template_styles set params = ' . $db->quote($contents) . ' where id = ' . $input->getInt('id'));
		$db->execute();
		JFactory::getApplication()->enqueueMessage('Uploaded successfully settings');
	}
	public static function exportAjax()
	{
		self::checkAccess();
		$db = \JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('*')
			->from('#__template_styles')
			->where('id = ' . JFactory::getApplication()->input->getInt('id'));
		$db->setQuery($query);
		$style = $db->loadObject();
		// Download the ical content
		header('Content-Type: application/json');
		header('Content-disposition: attachment; filename="' . $style->template . '-preset.json"');
		return $style->params;
	}
	public static function loadPresetAjax()
	{
		self::checkAccess();
		$input = JFactory::getApplication()->input;
		$path = JPATH_ROOT . '/templates/' . $input->get('template') . '/presets/' . $input->get('preset') . '.json';
		if (!file_exists($path))
		{
			throw new Exception('File ' . (JDEBUG ? $path : '')  . ' does not exists!');
		}
		$preset = file_get_contents($path);
		$db = JFactory::getDBO();
		$db->setQuery('update #__template_styles set params = ' . $db->quote($preset) . ' where id = ' . $input->getInt('id'));
		$db->execute();
		JFactory::getApplication()->enqueueMessage('Applied successfully preset ' . $input->get('preset'));
	}
	private static function checkAccess()
	{
		if (!\JSession::checkToken('get'))
		{
			throw new Exception(\JText::_('JINVALID_TOKEN'), 403);
		}
		if (!JFactory::getUser()->authorise('core.create', 'com_templates'))
		{
			throw new \Exception('Not allowed!!', 403);
		}
	}
}