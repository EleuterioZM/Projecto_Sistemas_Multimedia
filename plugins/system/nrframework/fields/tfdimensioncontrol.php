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

JFormHelper::loadFieldClass('text');

class JFormFieldTFDimensionControl extends JFormFieldText
{
    /**
     * Set the dimensions.
     * 
     * @var  array
     */
    protected $dimensions = [
        'top' => 'NR_TOP',
        'right' => 'NR_RIGHT',
        'bottom' => 'NR_BOTTOM',
        'left' => 'NR_LEFT'
    ];

    /**
     * Set whether the linked button will be enabled or not.
     * 
     * @var  boolean
     */
    protected $linked = true;
    
    /**
     * Method to get a list of options for a list input.
     * @return  array  An array of JHtml options.
     */
    protected function getInput()
    {
        if (!$this->dimensions = isset($this->element['dimensions']) ? $this->parseDimensions($this->element['dimensions']) : $this->dimensions)
        {
            return;
        }
     
        $this->assets();
   
        $this->linked = isset($this->element['linked']) ? (boolean) $this->element['linked'] : (isset($this->value->linked) ? (boolean) $this->value->linked : $this->linked);
        
        $payload = [
            'dimensions' => $this->dimensions,
            'linked' => $this->linked,
            'name' => $this->name,
            'value' => $this->value
        ];

        $layout = new JLayoutFile('dimension', JPATH_PLUGINS . '/system/nrframework/layouts/controls');
        return $layout->render($payload);
    }

    /**
     * Prepares the given dimensions.
     * 
     * Input:
     * 
	 * - top:NR_TOP,right:NR_RIGHT,bottom:NR_BOTTOM,left:NR_LEFT
	 * - top_left:Top Left,top_right:Top Right,bottom_right:Bottom Right,bottom_left:Bottom Left
     * 
     * @param  array  $dimensions
     * 
     * @return array
     */
    private function parseDimensions($dimensions = [])
    {
        $pairs = explode(',', $dimensions);
        
        $parsed = [];

        if (empty(array_filter($pairs)))
        {
            return [];
        }

        foreach ($pairs as $key => $pair)
        {
            if (!$value = explode(':', $pair))
            {
                continue;
            }

            // We expect 2 key,value pairs
            if (count($value) !== 2)
            {
                continue;
            }

            $parsed[$value[0]] = \JText::_($value[1]);
        }

        return $parsed;
    }

    /**
     * Load field assets.
     * 
     * @return  void
     */
    private function assets()
    {
        JHtml::stylesheet('plg_system_nrframework/controls/dimension.css', ['relative' => true, 'versioning' => 'auto']);
        JHtml::script('plg_system_nrframework/controls/dimension.js', ['relative' => true, 'version' => true]);
    }
}