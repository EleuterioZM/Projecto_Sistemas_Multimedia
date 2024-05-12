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

$operations = [
	'sql',
	'foundry',
	'admin',
	'site',
	'languages',
	'media',
	'toolbar',
	'syncdb',
	'postinstall'
];

if (SI_CONTRACT === 'free') {
	$operations = [
	'sql',
	'foundry',
	'admin',
	'site',
	'languages',
	'media',
	'syncdb',
	'postinstall'
];
}
?>
<?php foreach ($operations as $key) { ?>
<li class="si-install-logs__item" data-progress-<?php echo $key;?> data-installation-operations="<?php echo $key;?>">
	<div class="si-install-logs__title">
		<?php echo t('INSTALLATION_INITIALIZING_' . strtoupper($key));?>
	</div>

	<?php include(__DIR__ . '/log.state.php'); ?>
</li>
<?php } ?>