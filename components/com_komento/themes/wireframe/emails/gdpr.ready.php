<?php
/**
* @package      Komento
* @copyright    Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<?php echo $this->fd->html('email.heading', 'COM_KT_EMAILS_GDPR_HEADING', 'COM_KT_EMAILS_GDPR_SUBHEADING'); ?>

<?php echo $this->fd->html('email.content', JText::_('COM_KT_EMAILS_GDPR_DOWNLOAD_CONTENT'), 'clear'); ?>

<?php echo $this->fd->html('email.button', 'COM_KT_EMAILS_BUTTON_DOWNLOAD_ACCOUNT_DATA', $templatePreview ? 'javascript:void(0);' : $downloadLink, 'primary'); ?>