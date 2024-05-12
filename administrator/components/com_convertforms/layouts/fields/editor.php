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

extract($displayData);

JHtml::script('com_convertforms/field_editor.js', ['relative' => true , 'version' => 'auto']);
JHtml::stylesheet('com_convertforms/field_editor.css', ['relative' => true , 'version' => 'auto']);

JFactory::getDocument()->addStyleDeclaration('
    #cf_' . $form['id'] . ' .cf-control-group[data-key="' . $field->key . '"] {
        --height:' . $field->height . 'px
    }
');

echo $field->richeditor;

?>