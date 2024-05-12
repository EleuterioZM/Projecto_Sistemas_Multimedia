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

jimport('joomla.application.component.controllerform');

/**
 * Form Controller Class
 */
class ConvertFormsControllerForm extends JControllerForm
{
	protected $text_prefix = 'COM_CONVERTFORMS_FORM';

	public function ajaxSave()
	{
		$data = $this->getFormDataFromRequest();
		$model = $this->getModel('Form');
		$validData = $model->validate('jform', $data);

        JPluginHelper::importPlugin('convertforms');
        JPluginHelper::importPlugin('convertformstools');

		if (!$model->save($validData))
		{
			header('HTTP/1.1 500');
			$response = [
				'error' => \JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError())
			];
		}
		else 
		{
			$id = $model->getState('form.id');
			$isNew = $data['id'] == 0;
	
			$response = [
				'id'       => $id,
				'isNew'    => $isNew,
				'redirect' => JRoute::_('index.php?option=com_convertforms&task=form.edit&id=' . $id)
			];
		}

		jexit(json_encode($response, JSON_UNESCAPED_UNICODE));
	}

	public function preview()
	{
		$data = $this->getModel('Form')->validate('jform', $this->getFormDataFromRequest());
		$data['params'] = json_decode($data['params'], true);
		$data['fields'] = $data['params']['fields'];

		$response = [
			'html' => '
				<div>
					<div class="b">
						' . ConvertForms\Helper::renderForm($data)  . '
					</div>
				</div>
			'
		];

		echo json_encode($response, JSON_UNESCAPED_UNICODE);

		jexit();
	}

	private function getFormDataFromRequest()
	{
		$data = json_decode(file_get_contents('php://input'));

		$xx = new JRegistry();

		foreach ($data as $value)
		{
			$key = str_replace(['jform[', ']', '['], ['', '', '.'], $value->name);

			// Why?
			if ($key == 'emails')
			{
				continue;
			}

			$xx->set($key, $value->value);
		}

		return $xx->toArray();
	}
}
