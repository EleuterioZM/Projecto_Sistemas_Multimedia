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

// do not modify below section:
// preview start
if (isset($templatePreview) && $templatePreview) {
	$sitename = "Site name";
	$theme = KT::themes();
	$theme->set('templatePreview', $templatePreview);
	$content = $theme->output('site/emails/subscription.digest.comments');
}
// preview end
?>

<?php echo $this->fd->html('email.heading', 'COM_KT_EMAILS_DIGEST_HEADING', JText::sprintf('COM_KT_EMAILS_DIGEST_HEADING_SUBHEADING', $sitename)); ?>

<?php echo $this->fd->html('email.digestDate'); ?>

<?php echo $content; ?>
