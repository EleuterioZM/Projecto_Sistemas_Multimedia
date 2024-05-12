<?php
/**
* @package      Foundry
* @copyright    Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* Foundry is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<a class="fd-state-<?php echo $class;?> badge" href="javascript:void(0);"
	<?php echo $actionAllowed ? ' data-fd-table-publishing="' . $this->fd->getName() . '"' : '';?>
    style="<?php echo $actionAllowed ? '' : 'cursor:default;'; ?>"

    <?php if ($actionAllowed && $task) { ?>
	data-task="<?php echo $task;?>"
    <?php } ?>

    data-<?php echo $this->fd->getShortName();?>-provide="tooltip"
    data-title="<?php echo JText::_($tooltip);?>"
    data-placement="bottom"
	<?php echo !$actionAllowed ? ' disabled="disabled"' : '';?>
>
</a>
