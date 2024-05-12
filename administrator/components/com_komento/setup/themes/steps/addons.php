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
<form name="installation" data-installation-form>
	<div class="si-card si-container-overflow mb-4 p-3" style="max-height:15vh" data-addons-retrieving>
		<div>
			<?php echo t('INSTALLATION_ADDONS_RETRIEVING_LIST');?>
		</div>
	</div>

	<div class="d-none mt-4 mb-4" data-addons-progress>
		<div class="mb-3 d-none" data-progress-complete-message><?php echo t('INSTALLATION_MODULE_AND_PLUGINS_COMPLETED');?></div>
		<div class="mb-3" data-progress-active-message>
			<?php echo t('INSTALLATION_INSTALLING_MODULES');?>
		</div>

		<div class="si-progress mb-3">
			<div class="si-progress__bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" data-progress-bar style="width: 0%;"></div>
		</div>
	</div>

	<div class="d-none mt-5" data-sync-progress>
		<div class="mb-3 d-none" data-sync-progress-complete-message>
			<?php echo t('INSTALLATION_MAINTENANCE_EXEC_SCRIPTS_SUCCESS');?>
		</div>

		<div class="mb-3" data-sync-progress-active-message>
			<?php echo t('INSTALLATION_MAINTENANCE_EXEC_SCRIPTS');?>
		</div>

		<div class="si-progress mb-3">
			<div class="si-progress__bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" data-sync-progress-bar style="width: 0%;"></div>
		</div>

		<div class="si-card si-container-overflow mb-4 p-3" style="height: 20vh">
			<ol class="si-install-logs" data-script-logs style="font-size: 14px;">
			</ol>
		</div>
	</div>

	<div data-addons-container></div>

	<input type="hidden" name="option" value="<?php echo SI_IDENTIFIER;?>" />
	<input type="hidden" name="active" value="complete" />
</form>

<?php
$source = $input->get('source', '', 'default');
?>
<script type="text/javascript">
$(document).ready(function(){

kt.ajaxUrl = "<?php echo JURI::root();?>administrator/index.php?option=com_komento&ajax=1";
kt.options.path = '<?php echo addslashes($source);?>';

kt.addons.retrieveList();

});
</script>
