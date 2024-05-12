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

?>

<input type="hidden" name="<?php echo $field->input_name ?>" class="cf-input" value="<?php echo $field->value; ?>"

<?php if (isset($field->htmlattributes) && !empty($field->htmlattributes)) { ?>
    <?php foreach ($field->htmlattributes as $key => $value) { ?>
        <?php echo $key ?>="<?php echo htmlspecialchars($value, ENT_COMPAT, 'UTF-8') ?>"
    <?php } ?>
<?php } ?>
>
