<?php


namespace Nextend\SmartSlider3\Platform\Joomla\Module\Field;


use Joomla\CMS\Form\FormField;
use Joomla\CMS\Uri\Uri;

jimport('joomla.form.formfield');

class FieldEditSlider extends FormField {

    protected $type = 'EditSlider';

    public function getInput() {
        $style = '<style>#jform_params_slider_chzn{width:100% !important;max-width:500px;}</style>';

        return $style . '<a href="#" onclick="window.open(\'' . Uri::root() . 'administrator/index.php?option=com_smartslider3&nextendcontroller=slider&nextendaction=edit&sliderid=\' + jQuery(\'#jform_params_slider\').val(), \'_blank\'); return false;" class="btn btn-small btn-success" target="_blank"> Edit selected slider</a>';
    }
}