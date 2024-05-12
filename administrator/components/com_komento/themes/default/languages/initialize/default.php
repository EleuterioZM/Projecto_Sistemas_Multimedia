<?php
/**
* @package      Komento
* @copyright    Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<form name="adminForm" id="adminForm" method="post" data-fd-grid>

    <div class="languages-wrapper" data-languages-wrapper>
    	<div class="languages-loader">
    		<?php echo JText::_('COM_KOMENTO_INITIALIZING_LANGUAGE_LIST');?><br />
    	</div>

        <div class="invalid-api">
            <?php echo $this->fd->html('icon.font', 'fdi fa fa-remove text-danger'); ?>
            <div data-languages-error></div>
        </div>
    </div>

	<?php echo $this->fd->html('form.action', '', 'languages'); ?>
</form>
