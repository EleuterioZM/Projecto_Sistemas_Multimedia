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

// no hCaptcha image is present in invisible-mode
if ($field->hcaptcha_type == 'invisible')
{
	return;
}

$suffix = $field->size == 'compact' ? '_compact' : '';

$imageURL = JURI::root() . 'media/com_convertforms/img/hcaptcha_' . $field->theme . $suffix . '.png';
?>
<img src="<?php echo $imageURL ?>"/>