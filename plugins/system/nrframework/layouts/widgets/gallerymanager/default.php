<?php

/**
 * @package         Convert Forms
 * @version         3.2.8 Free
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('_JEXEC') or die('Restricted access');

extract($displayData);

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Component\ComponentHelper;

if (!$disabled)
{
	if (strpos($css_class, 'ordering-default') !== false)
	{
		JHtml::script('https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js');
	}

	// Required in the front-end for the media manager to work
	if (!defined('nrJ4'))
	{
		JHtml::_('behavior.modal');
		// Front-end editing: The below script is required for front-end media library selection to work as its missing from parent window when called
		if (JFactory::getApplication()->isClient('site'))
		{
			?>
			<script>
			function jInsertFieldValue(value, id) {
				var old_id = document.id(id).value;
				if (old_id != id) {
					var elem = document.id(id)
					elem.value = value;
					elem.fireEvent("change");
				}
			}
			</script>
			<?php
		}
	}
	else
	{
		HTMLHelper::_('bootstrap.dropdown', '.dropdown-toggle');
		
		$doc = JFactory::getApplication()->getDocument();
		$doc->addScriptOptions('media-picker', [
			'images' => array_map(
				'trim',
				explode(
					',',
					ComponentHelper::getParams('com_media')->get(
						'image_extensions',
						'bmp,gif,jpg,jpeg,png'
					)
				)
			)
		]);

		$wam = $doc->getWebAssetManager();
		$wam->useScript('webcomponent.media-select');
		
		Text::script('JFIELD_MEDIA_LAZY_LABEL');
		Text::script('JFIELD_MEDIA_ALT_LABEL');
		Text::script('JFIELD_MEDIA_ALT_CHECK_LABEL');
		Text::script('JFIELD_MEDIA_ALT_CHECK_DESC_LABEL');
		Text::script('JFIELD_MEDIA_CLASS_LABEL');
		Text::script('JFIELD_MEDIA_FIGURE_CLASS_LABEL');
		Text::script('JFIELD_MEDIA_FIGURE_CAPTION_LABEL');
		Text::script('JFIELD_MEDIA_LAZY_LABEL');
		Text::script('JFIELD_MEDIA_SUMMARY_LABEL');
	}
}

// Use admin gallery manager path if browsing via backend
$gallery_manager_path = JFactory::getApplication()->isClient('administrator') ? 'administrator/' : '';

// Javascript files should always load as they are used to populate the Gallery Manager via Dropzone
JHtml::script('plg_system_nrframework/dropzone.min.js', ['relative' => true, 'version' => 'auto']);
JHtml::script('plg_system_nrframework/widgets/gallery/manager_init.js', ['relative' => true, 'version' => 'auto']);
JHtml::script('plg_system_nrframework/widgets/gallery/manager.js', ['relative' => true, 'version' => 'auto']);

if ($load_stylesheet)
{
	JHtml::stylesheet('plg_system_nrframework/widgets/gallerymanager.css', ['relative' => true, 'version' => 'auto']);
}
?>
<!-- Gallery Manager -->
<div class="nrf-widget tf-gallery-manager<?php echo $css_class; ?>" data-field-id="<?php echo $field_id; ?>">
	<!-- Make Joomla client-side form validator happy by adding a fake hidden input field when the Gallery is required. -->
	<?php if ($required) { ?>
		<input type="hidden" required class="required" id="<?php echo $id; ?>"/>
	<?php } ?>

	<!-- Actions -->
	<div class="tf-gallery-actions">
		<div class="btn-group tf-gallery-actions-dropdown<?php echo !defined('nrJ4') ? ' dropdown' : ''; ?> " title="<?php echo Text::_('NR_GALLERY_MANAGER_SELECT_UNSELECT_IMAGES'); ?>">
			<button class="btn btn-secondary add tf-gallery-actions-dropdown-current tf-gallery-actions-dropdown-action select" onclick="return false;"><i class="me-2 icon-checkbox-unchecked"></i></button>
			<button class="btn btn-secondary add dropdown-toggle dropdown-toggle-split" data-<?php echo defined('nrJ4') ? 'bs-' : ''; ?>toggle="dropdown" title="<?php echo Text::_('NR_GALLERY_MANAGER_ADD_DROPDOWN'); ?>">
				<span class="caret"></span>
			</button>
			<ul class="dropdown-menu">
				<li><a href="#" class="dropdown-item tf-gallery-actions-dropdown-action select"><?php echo Text::_('NR_GALLERY_MANAGER_SELECT_ALL_ITEMS'); ?></a></li>
				<li><a href="#" class="dropdown-item tf-gallery-actions-dropdown-action unselect is-hidden"><?php echo Text::_('NR_GALLERY_MANAGER_UNSELECT_ALL_ITEMS'); ?></a></li>
			</ul>
		</div>
		<a class="tf-gallery-regenerate-thumbs-button icon-button" title="<?php echo Text::_('NR_GALLERY_MANAGER_REGENERATE_THUMBNAILS'); ?>">
			<i class="icon-refresh"></i>
			<div class="message"></div>
		</a>
		<a class="tf-gallery-remove-selected-items-button icon-button" title="<?php echo Text::_('NR_GALLERY_MANAGER_REMOVE_SELECTED_IMAGES'); ?>">
			<i class="icon-trash"></i>
		</a>
		<div class="btn-group add-button<?php echo !defined('nrJ4') ? ' dropdown' : ''; ?>">
			<button class="btn btn-success add tf-gallery-add-item-button" onclick="return false;" title="<?php echo Text::_('NR_GALLERY_MANAGER_ADD_IMAGES'); ?>"><i class="me-2 icon-pictures"></i><?php echo Text::_('NR_GALLERY_MANAGER_ADD_IMAGES'); ?></button>
			<button class="btn btn-success add dropdown-toggle dropdown-toggle-split" data-<?php echo defined('nrJ4') ? 'bs-' : ''; ?>toggle="dropdown" title="<?php echo Text::_('NR_GALLERY_MANAGER_ADD_DROPDOWN'); ?>">
				<span class="caret"></span>
			</button>
			<ul class="dropdown-menu">
				<li>
					<a <?php echo !defined('nrJ4') ? 'rel="{handler: \'iframe\', size: {x: 1000, y: 750}}" href="' . JURI::root() . $gallery_manager_path . '?option=com_media&view=images&tmpl=component&fieldid=' . $id . '_uploaded_file"' : 'href="#" data-bs-toggle="modal" data-bs-target="#tf-GalleryMediaManager" data-gallery-id="' . $id . '"'; ?> class="dropdown-item tf-gallery-browse-item-button<?php echo !defined('nrJ4') ? ' modal' : ''; ?> popup" title="<?php echo Text::_('NR_GALLERY_MANAGER_BROWSE_MEDIA_LIBRARY'); ?>"><i class="me-2 icon-folder-open"></i><?php echo Text::_('NR_GALLERY_MANAGER_BROWSE_MEDIA_LIBRARY'); ?></a>
				</li>
			</ul>
		</div>
		<input type="hidden" class="media_uploader_file" id="<?php echo $id; ?>_uploaded_file" />
	</div>
	<!-- /Actions -->

	<!-- Dropzone -->
	<div
		data-inputname="<?php echo $name; ?>"
		data-maxfilesize="<?php echo $max_file_size; ?>"
		data-maxfiles="<?php echo $limit_files; ?>"
		data-acceptedfiles="<?php echo $allowed_file_types; ?>"
		data-value='<?php echo $gallery_items ? json_encode($gallery_items, JSON_HEX_APOS) : ''; ?>'
		data-baseurl="<?php echo JURI::base(); ?>"
		data-rooturl="<?php echo JURI::root(); ?>"
		class="tf-gallery-dz">
		<!-- DZ Message Wrapper -->
		<div class="dz-message">
			<!-- Message -->
			<div class="dz-message-center">
				<span class="text"><?php echo Text::_('NR_GALLERY_MANAGER_DRAG_AND_DROP_TEXT'); ?></span>
				<span class="browse"><?php echo Text::_('NR_GALLERY_MANAGER_BROWSE'); ?></span>
			</div>
			<!-- /Message -->
		</div>
		<!-- /DZ Message Wrapper -->
	</div>
	<!-- /Dropzone -->

	<!-- Dropzone Preview Template -->
	<template>
		<div class="tf-gallery-preview-item template" data-item-id="">
			<div class="select-item-checkbox" title="<?php echo Text::_('NR_GALLERY_MANAGER_CHECK_TO_DELETE_ITEMS'); ?>">
				<input type="checkbox" id="<?php echo $name; ?>[select-item]" />
				<label for="<?php echo $name; ?>[select-item]"></label>
			</div>
			<a href="#" class="tf-gallery-preview-remove-item" title="<?php echo Text::_('NR_GALLERY_MANAGER_CLICK_TO_DELETE_ITEM'); ?>" data-dz-remove><svg fill="#dedede" viewBox="0 0 512 512" width="13" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><path d="M437.5,386.6L306.9,256l130.6-130.6c14.1-14.1,14.1-36.8,0-50.9c-14.1-14.1-36.8-14.1-50.9,0L256,205.1L125.4,74.5  c-14.1-14.1-36.8-14.1-50.9,0c-14.1,14.1-14.1,36.8,0,50.9L205.1,256L74.5,386.6c-14.1,14.1-14.1,36.8,0,50.9  c14.1,14.1,36.8,14.1,50.9,0L256,306.9l130.6,130.6c14.1,14.1,36.8,14.1,50.9,0C451.5,423.4,451.5,400.6,437.5,386.6z"/></svg></a>
			<div class="inner">
				<div class="dz-status"></div>
				<div class="dz-thumb">
					<div class="dz-progress"><span class="text"><?php echo Text::_('NR_GALLERY_MANAGER_UPLOADING'); ?></span><span class="dz-upload" data-dz-uploadprogress></span></div>
					<div class="tf-gallery-preview-in-queue"><?php echo Text::_('NR_GALLERY_MANAGER_IN_QUEUE'); ?></div>
					<img data-dz-thumbnail />
				</div>
				<div class="dz-details">
					<div class="detail">
						<textarea name="<?php echo $name; ?>[caption]" class="item-caption" placeholder="<?php echo Text::_('NR_GALLERY_MANAGER_CAPTION_HINT'); ?>" title="<?php echo Text::_('NR_GALLERY_MANAGER_CAPTION_HINT'); ?>" rows="5"></textarea>
					</div>
				</div>
				<div class="tf-gallery-preview-error"><div data-dz-errormessage></div></div>
				<input type="hidden" value="" class="item-thumbnail" name="<?php echo $name; ?>[thumbnail]" />
			</div>
		</div>
	</template>
	<!-- /Dropzone Preview Template -->

	<?php
	// Print Joomla 4 Media Manager modal only if Gallery is not disabled
	if (defined('nrJ4') && !$disabled)
	{
		echo JHtml::_('bootstrap.renderModal', 'tf-GalleryMediaManager', [
			'title'       => Text::_('NR_GALLERY_MANAGER_SELECT_ITEM'),
			'url' 		  => Route::_(JURI::root() . $gallery_manager_path . '?option=com_media&view=media&tmpl=component'),
			'height'      => '400px',
			'width'       => '800px',
			'bodyHeight'  => 80,
			'modalWidth'  => 80,
			'backdrop' 	  => 'static',
			'footer'      => '<button type="button" class="btn btn-secondary tf-gallery-button-save-selected button-save-selected" data-bs-dismiss="modal">' . Text::_('JSELECT') . '</button>' . '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">' . Text::_('JCANCEL') . '</button>',
		]);
	}
	?>
</div>
<!-- /Gallery Manager -->