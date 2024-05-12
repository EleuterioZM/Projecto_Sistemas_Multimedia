<?php
/**
* @package		Komento
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>

<?php echo $this->fd->html('email.heading', 'COM_KT_EMAILS_NEW_COMMENT_HEADING', 'COM_KT_EMAILS_NEW_COMMENT_SUBHEADING'); ?>

<?php echo $this->fd->html('email.content', JText::_('COM_KT_EMAILS_NEW_COMMENT_CONTENT'), 'clear'); ?>

<?php echo $this->fd->html('email.comment',
	$templatePreview ? $lipsum : $commentContent,
	$templatePreview ? '13th August 2021' : $commentDate,
	$templatePreview ? 'John Doe' : ucfirst($commentAuthorName)
); ?>

<?php echo $this->fd->html('email.spacer'); ?>

<?php echo $this->fd->html('email.button', 'COM_KOMENTO_EMAILS_BUTTON_VIEW_COMMENT', $templatePreview ? 'javascript:void(0);' : $commentPermalink, 'primary'); ?>