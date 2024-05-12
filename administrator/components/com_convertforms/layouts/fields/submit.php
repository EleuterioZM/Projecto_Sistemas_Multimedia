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

extract($displayData);

// Styles
$buttonContainerClasses = array(
	'cf-text-' . $field->align,
);

$buttonStyles = array(
    'border-radius:' . (int) $field->borderradius . 'px',
    'padding:'       . (int) $field->vpadding . 'px ' . (int) $field->hpadding . 'px',
    'color:'         . $field->textcolor,
    'font-size:'     . (int) $field->fontsize . 'px'
);

if (in_array($field->btnstyle, array('gradient', 'flat')))
{
    $buttonStyles[] = 'background-color:' . $field->bg;

    $styles = '
        #cf_' . $form['id'] . ' .cf-btn:after { 
            border-radius: ' . (int) $form['params']->get('btnborderradius', '5') . 'px'.'
        }
    ';
}

if ($field->btnstyle == 'outline')
{
    $buttonStyles[] = 'border: solid 1px ' . $field->bg;
    $buttonStyles[] = 'background: none';

    $styles = '
        #cf_' . $form['id'] . ' .cf-btn:hover { 
            background-color: ' . $field->bg . ' !important;
            color: ' . $field->texthovercolor . ' !important;
        }
    ';
}

$buttonClasses = [
    'cf-btn-style-'  . $field->btnstyle,
    'cf-btn-shadow-' . ($field->shadow ? '1' : '0'),
    $field->size,
    isset($field->inputcssclass) ? $field->inputcssclass : null
];

?>

<div class="<?php echo implode(' ', $buttonContainerClasses) ?>">
    <button type="submit" class="cf-btn <?php echo implode(" ", $buttonClasses) ?>" style="<?php echo implode(";", $buttonStyles) ?>">
        <span class="cf-btn-text"><?php echo JText::_($field->text) ?></span>
        <span class="cf-spinner-container">
            <span class="cf-spinner">
                <span class="bounce1"></span>
                <span class="bounce2"></span>
                <span class="bounce3"></span>
            </span>
        </span>
    </button>
</div>

<?php 

if (isset($styles) && !empty($styles))
{
    if (JFactory::getApplication()->isClient('site'))
    {
        ConvertForms\Helper::addStyleDeclarationOnce($styles);
    } else 
    {
        // On backend add styles inline 
        echo '<style>' . $styles . '</style>';
    }
}

?>