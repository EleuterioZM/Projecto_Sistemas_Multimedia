<?php

/**
 * @package         Convert Forms
 * @version         3.2.8 Free
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('_JEXEC') or die('Restricted access');

class JFormFieldTFBorderControl extends JFormField
{
    protected function getInput()
    {
        // Control Group Class
        $control_group_class = (string) $this->element['control_group_class'];

        // Hides the inner control labels
        $hide_labels = (bool) $this->element['hide_labels'];
        $hiddenLabel = $hide_labels ? 'hiddenLabel="true"' : '';

        // Prefix and suffix for the fieldset
        $prefix = $suffix = '';
        
        // Whether to display the fields inline
        $inline = (bool) $this->element['inline'];
        if ($inline)
        {
            $prefix = '<field name="border_control_row_start" type="nr_inline" />';
            $suffix = '<field name="border_control_row_end" type="nr_inline" end="1" />';
        }
        
        $form_source = new SimpleXMLElement('
            <form>
                <fieldset name="border">
                    ' . $prefix . '
                    <field name="style" type="list"
                        ' . $hiddenLabel . '
                        label="NR_STYLE"
                        description="NR_BORDER_CONTROL_STYLE_DESC"
                        required="' . $this->required . '"
                        disabled="' . $this->disabled . '">
                        <option value="none">NR_NONE</option>
						<option value="solid">NR_SOLID</option>
						<option value="dotted">NR_DOTTED</option>
						<option value="dashed">NR_DASHED</option>
						<option value="double">NR_DOUBLE</option>
						<option value="groove">NR_GROOVE</option>
						<option value="ridge">NR_RIDGE</option>
                    </field>
                    <field name="width" type="nrnumber"
                        ' . $hiddenLabel . '
                        label="NR_WIDTH"
                        description="NR_BORDER_WIDTH_DESC"
                        class="input-small"
						default="0"
                        required="' . $this->required . '"
                        disabled="' . $this->disabled . '"
                        addon="px"
						showon="style!:none"
                    />
                    <field name="color" type="color"
                        ' . $hiddenLabel . '
                        label="NR_COLOR"
                        description="NR_BORDER_COLOR_DESC"
						keywords="transparent,none"
						format="rgba"
						position="bottom"
                        required="' . $this->required . '"
                        disabled="' . $this->disabled . '"
						showon="style!:none"
                    />
                    ' . $suffix . '
                </fieldset>
            </form>
        ');

        $control  = $this->name;
        $formname = 'border.' . str_replace(['jform[', '[', ']'], ['', '.', ''], $control);

        $form = JForm::getInstance($formname, $form_source->asXML(), ['control' => $control]);
        $form->bind($this->value);

        return $form->renderFieldset('border', [
            'class' => $control_group_class
        ]);
    }
}