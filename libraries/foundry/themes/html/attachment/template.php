<?php
/**
* @package		Foundry
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Foundry is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="o-attachment-list__item" data-fd-attachment>
	<div class="o-attachment">
		<div class="o-attachment__preview">
			<div class="o-attachment-preview" data-fd-attachment-preview>
				<div class="o-attachment-preview__content"></div>
			</div>
		</div>
		<div class="o-attachment__content">
			<a href="javascript:void(0);" class="o-attachment__name" target="_blank" data-fd-attachment-title></a>
			<div class="o-attachment__size">
				<span data-fd-attachment-size></span> kb
			</div>
		</div>
		<div class="o-attachment__actions">
			<?php if ($download) { ?>
			<a href="javascript:void(0);" target="_blank">
				<i class="fdi fa fa-download"></i>
			</a>
			<?php } ?>
			
			<a href="javascript:void(0);" data-fd-attachment-delete>
				<i class="fdi far fa-trash-alt"></i>
			</a>
		</div>
	</div>
</div>