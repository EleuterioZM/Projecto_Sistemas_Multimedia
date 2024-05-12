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
			<div class="o-attachment-preview is-<?php echo $attachment->image ? 'image' : 'icon is-icon--' . $attachment->icon; ?>" data-fd-attachment-preview>
				<?php if ($attachment->image) { ?>
					<a href="<?php echo $attachment->image;?>" class="o-attachment-preview__content" style="background-image: url(<?php echo $attachment->image;?>);" 
						data-fd-lightbox="<?php echo $attachment->id;?>" 
						data-title="<?php echo FH::escape($attachment->title);?>"></a>
				<?php } else { ?>
					<div class="o-attachment-preview__content"></div>
				<?php } ?>
			</div>
		</div>
		<div class="o-attachment__content">
			<a href="<?php echo $attachment->download;?>" class="o-attachment__name" target="_blank"><?php echo $attachment->title;?></a>
			<div class="o-attachment__size">
				<span><?php echo $attachment->size;?></span> kb
			</div>
		</div>
		<div class="o-attachment__actions">
			<?php if ($attachment->download) { ?>
			<a href="<?php echo $attachment->download;?>" target="_blank" class="outline-none">
				<i class="fdi fa fa-download"></i>
			</a>
			<?php } ?>
				
			<?php if ($attachment->canDelete) { ?>
			<a href="javascript:void(0);" data-fd-attachment-delete data-id="<?php echo $attachment->id;?>" class="outline-none">
				<i class="fdi far fa-trash-alt"></i>
			</a>
			<?php } ?>
		</div>
	</div>
</div>