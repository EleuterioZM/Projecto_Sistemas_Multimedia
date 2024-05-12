<?php
/**
 * @package     Joomla.Site
 * @subpackage  Template.system
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
$app = JFactory::getApplication();
$document = JFactory::getDocument();

// Add JavaScript Frameworks
\JHtml::_('behavior.core');
\JHtml::_('bootstrap.framework');

$document->addStyleSheet('templates/' . $this->template . '/css/bonsaicss/bonsai-base.min.css');
$document->addStyleSheet('templates/' . $this->template . '/css/bonsaicss/bonsai-utilities.min.css');
$document->addStyleSheet('templates/' . $this->template . '/css/nexus.min.css');

require_once JPATH_ADMINISTRATOR . '/components/com_users/helpers/users.php';

$twofactormethods = UsersHelper::getTwoFactorMethods();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
	<jdoc:include type="head" />
	<link rel="stylesheet" href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/offline.css" type="text/css" />

	<?php if ($this->direction == 'rtl') : ?>
		<link rel="stylesheet" href="<?php echo $this->baseurl; ?>/templates/system/css/offline_rtl.css" />
	<?php endif; ?>

</head>
<body>
<jdoc:include type="message" />

<div id="frame" class="offline-wrapper">
	<?php if ($app->get('offline_image') && file_exists($app->get('offline_image'))) : ?>
	<!-- Image -->
	<div class="offline-image">
		<img src="<?php echo $app->get('offline_image'); ?>" alt="<?php echo htmlspecialchars($app->get('sitename')); ?>" />
	</div>
	<?php endif; ?>

	<!-- Site name -->
	<h1 class="site-title"><?php echo htmlspecialchars($app->get('sitename')); ?></h1>

	<!-- Offline message -->
	<?php if ($app->get('display_offline_message', 1) == 1 && str_replace(' ', '', $app->get('offline_message')) != '') : ?>
		<p class="offline-message"><?php echo $app->get('offline_message'); ?></p>
	<?php elseif ($app->get('display_offline_message', 1) == 2 && str_replace(' ', '', JText::_('JOFFLINE_MESSAGE')) != '') : ?>
		<p class="offline-message"><?php echo JText::_('JOFFLINE_MESSAGE'); ?></p>
	<?php endif; ?>

	<!-- Login form -->
	<form action="<?php echo JRoute::_('index.php', true); ?>" method="post" id="form-login">
		<fieldset class="input">
			<p id="form-login-username">
				<label for="username"><?php echo JText::_('JGLOBAL_USERNAME'); ?></label>
				<input name="username" id="username" type="text" class="inputbox" alt="<?php echo JText::_('JGLOBAL_USERNAME'); ?>" size="18" />
			</p>
			<p id="form-login-password">
				<label for="passwd"><?php echo JText::_('JGLOBAL_PASSWORD'); ?></label>
				<input type="password" name="password" class="inputbox" size="18" alt="<?php echo JText::_('JGLOBAL_PASSWORD'); ?>" id="passwd" />
			</p>
			<?php if (count($twofactormethods) > 1) : ?>
				<p id="form-login-secretkey">
					<label for="secretkey"><?php echo JText::_('JGLOBAL_SECRETKEY'); ?></label>
					<input type="text" name="secretkey" class="inputbox" size="18" alt="<?php echo JText::_('JGLOBAL_SECRETKEY'); ?>" id="secretkey" />
				</p>
			<?php endif; ?>
			<p id="submit-buton">
				<label>&nbsp;</label>
				<input type="submit" name="Submit" class="button login" value="<?php echo JText::_('JLOGIN'); ?>" />
			</p>
			<input type="hidden" name="option" value="com_users" />
			<input type="hidden" name="task" value="user.login" />
			<input type="hidden" name="return" value="<?php echo base64_encode(JUri::base()); ?>" />
			<?php echo JHtml::_('form.token'); ?>
		</fieldset>
	</form>

</div>
</body>
</html>
