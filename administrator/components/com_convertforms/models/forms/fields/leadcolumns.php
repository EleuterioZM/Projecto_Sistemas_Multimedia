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
JFormHelper::loadFieldClass('checkboxes');

class JFormFieldLeadColumns extends JFormFieldCheckboxes
{
    /**
     * Method to get a list of options for a list input.
     *
     * @return      array           An array of JHtml options.
     */
    protected function getOptions()
    {
        $formID = $this->getFormID();

        $form_fields = ConvertForms\Helper::getColumns($formID);

        $optionsForm = [];

        foreach ($form_fields as $key => $field)
        {
            $label = ucfirst(str_replace('param_', '', $field));

            if (strpos($field, 'param_') === false)
            {
                $label = JText::_('COM_CONVERTFORMS_' . $label);
            }

            $optionsForm[] = (object) [
                'value' => $field,
                'text'  => $label
            ];
        }

        return $optionsForm;
    }

    protected function getInput()
    {
		JFactory::getDocument()->addScriptDeclaration('
			function cfSubmissionColumnsApply(that) {
                // Reset task in case it was previously set and would trigger the task on submit such as submissions export
                let task = document.querySelector("input[type=\"hidden\"][name=\"task\"]");
                if (task) {
                    task.value = "";
                }
                
                that.form.submit();
            }
		');

        $html = '
            <div class="chooseColumns">
                <a class="btn btn-secondary" data-bs-toggle="collapse" data-toggle="collapse" href="#" data-target=".chooseColumnsOptions" data-bs-target=".chooseColumnsOptions">'
                    . JText::_('COM_CONVERTFORMS_CHOOSE_COLUMNS') . '
                </a>
                <div class="collapse chooseColumnsOptions">
                    <div>
                        ' . parent::getInput() . '
                        <button class="btn btn-sm btn-success" onclick="cfSubmissionColumnsApply(this);">'
                            . JText::_('JAPPLY') . 
                        '</button>
                    </div>
                </div>
            </div>
        ';

        return $html;
    }

    private function getFormID()
    {
        return $this->form->getData()->get('filter.form_id');
    }
}