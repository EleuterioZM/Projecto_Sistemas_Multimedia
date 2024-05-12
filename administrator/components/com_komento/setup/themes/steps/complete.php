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
<div class="text-center">

	<p class="mb-5"><?php echo t('INSTALLATION_COMPLETED_DESC');?></p>

	<div id="svg__ani" class="lottie"></div>

	<div class="d-flex justify-content--c mt-5 mb-3">
		<div class="pl-3">
			<a href="<?php echo JURI::root();?>administrator/index.php?option=<?php echo SI_IDENTIFIER;?>" class="btn btn-outline-secondary">
				Launch Backend
			</a>
		</div>
	</div>
</div>

<script>
var animationRemote = bodymovin.loadAnimation({
	container: document.getElementById('svg__ani'),
	path: '/administrator/components/<?php echo SI_IDENTIFIER;?>/setup/assets/images/success.json',
	autoplay: true,
	renderer: 'svg',
	loop: false
});
</script>
