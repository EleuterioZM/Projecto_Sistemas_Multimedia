<?php

/**
 * @package         Convert Forms
 * @version         3.2.8 Free
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2018 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('_JEXEC') or die('Restricted access');

extract($displayData);

?>
<div class="nr-responsive-control-item">
    <?php if (!empty($label)) { ?>
    <div class="title" title="<?php echo !empty($description) ? $description : ''; ?>"><?php echo $label; ?></div>
    <?php } ?>
    <?php echo $data; ?>
</div>