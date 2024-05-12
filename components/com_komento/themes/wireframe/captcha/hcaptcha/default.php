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
<div id="hcaptcha" class="h-captcha required" 
	data-sitekey="<?php echo $this->config->get('antispam_hcaptcha_site');?>"
	data-theme="<?php echo $this->config->get('antispam_hcaptcha_theme');?>"
	data-size="<?php echo $this->config->get('antispam_hcaptcha_size');?>"
	data-callback="recaptchaCallback"
></div>

<input name="h-captcha-response" type="hidden" value="" data-kt-recaptcha-response />