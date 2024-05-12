<?php
/*
 * @package Joomla
 * @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 *
 * @component Phoca Gallery
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Language\Text;
echo '<div id="phocagallery-ytbupload">';
echo '<div style="font-size:1px;height:1px;margin:0px;padding:0px;">&nbsp;</div>';
echo '<form onsubmit="return OnUploadSubmitPG(\'loading-label-ytb\');" action="'. $this->t['syu_url'] .'" id="phocaGalleryUploadFormYU" method="post">';
//if ($this->t['ftp']) { echo PhocaGalleryFileUpload::renderFTPaccess();}
//echo '<h4>';
//echo JText::_('COM_PHOCAGALLERY_YTB_UPLOAD');
//echo ' </h4>';
if ($this->t['catidimage'] == 0 || $this->t['catidimage'] == '') {
	echo '<div class="alert alert-error alert-danger">'.Text::_('COM_PHOCAGALLERY_PLEASE_SELECT_CATEGORY_TO_BE_ABLE_TO_IMPORT_YOUTUBE_VIDEO').'</div>';
}
echo $this->t['syu_output'];

$this->t['upload_form_id'] = 'phocaGalleryUploadFormYU';
?>

<div><?php echo Text::_( 'COM_PHOCAGALLERY_YTB_LINK' ); ?>:</div>
<div>
<input type="text" id="phocagallery-ytbupload-link" class="form-control" name="phocagalleryytbuploadlink" value=""  maxlength="255" size="48" /></br>
<input type="submit" class="btn btn-primary" id="file-upload-submit" value="<?php echo Text::_('COM_PHOCAGALLERY_START_UPLOAD'); ?>"/>
</div>


<input type="hidden" name="controller" value="user" />
<input type="hidden" name="viewback" value="user" />
<input type="hidden" name="view" value="user"/>
<input type="hidden" name="tab" value="<?php echo $this->t['currenttab']['images'];?>" />
<input type="hidden" name="Itemid" value="<?php echo $this->itemId ?>"/>
<input type="hidden" name="filter_order_image" value="<?php echo $this->listsimage['order']; ?>" />
<input type="hidden" name="filter_order_Dir_image" value="" />
<input type="hidden" name="catid" value="<?php echo $this->t['catidimage'] ?>"/>

<?php
if ($this->t['upload_form_id'] == 'phocaGalleryUploadFormYU') {
	//echo '<div id="loading-label-ytb" style="text-align:center">'
	//. JHtml::_('image', 'media/com_phocagallery/images/icon-switch.gif', '')
	//. '  '.JText::_('COM_PHOCAGALLERY_LOADING').'</div>';
    echo '<div id="loading-label-user" class="ph-loading-text ph-loading-hidden"><div class="ph-lds-ellipsis"><div></div><div></div><div></div><div></div></div><div>'. Text::_('COM_PHOCAGALLERY_LOADING') . '</div></div>';
}

echo '</form>';
echo '</div>';
