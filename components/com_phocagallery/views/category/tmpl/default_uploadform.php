<?php
/*
 * @package		Joomla.Framework
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 *
 * @component Phoca Component
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License version 2 or later;
 */
defined('_JEXEC') or die;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
?><table>
	<tr>
		<td><?php echo Text::_('COM_PHOCAGALLERY_FILENAME');?>:</td>
		<td>
		<input type="file" id="file-upload" name="Filedata" class="form-control" /><?php
		/*<input type="submit" id="file-upload-submit" value="<?php echo JText::_('COM_PHOCAGALLERY_START_UPLOAD'); ?>"/>*/
		?><button class="btn btn-primary" id="file-upload-submit"><i class="icon-upload icon-white"></i><?php echo Text::_('COM_PHOCAGALLERY_START_UPLOAD'); ?></button>
		<span id="upload-clear"></span>
		</td>
	</tr>

	<tr>
		<td><?php echo Text::_( 'COM_PHOCAGALLERY_IMAGE_TITLE' ); ?>:</td>
			<td>
				<input type="text" id="phocagallery-upload-title" name="phocagalleryuploadtitle" value=""  maxlength="255" class="form-control comment-input" /></td>
		</tr>

		<tr>
			<td><?php echo Text::_( 'COM_PHOCAGALLERY_DESCRIPTION' ); ?>:</td>
			<td><textarea id="phocagallery-upload-description" name="phocagalleryuploaddescription" onkeyup="countCharsUpload('<?php echo $this->t['upload_form_id']; ?>');" cols="30" rows="10" class="form-control comment-input"></textarea></td>
		</tr>

		<tr>
			<td>&nbsp;</td>
			<td><?php echo Text::_('COM_PHOCAGALLERY_CHARACTERS_WRITTEN');?> <input name="phocagalleryuploadcountin" value="0" readonly="readonly" class="form-control comment-input2" /> <?php echo Text::_('COM_PHOCAGALLERY_AND_LEFT_FOR_DESCRIPTION');?> <input name="phocagalleryuploadcountleft" value="<?php echo $this->t['max_upload_char'];?>" readonly="readonly" class="form-control comment-input2" />
			</td>
		</tr>
</table>

<?php
if ($this->t['upload_form_id'] == 'phocaGalleryUploadFormU') {
	/*echo '<div id="loading-label" style="text-align:center">'
	. HTMLHelper::_('image', 'media/com_phocagallery/images/icon-switch.gif', '')
	. '  '.JText::_('COM_PHOCAGALLERY_LOADING').'</div>';*/

	echo '<div id="loading-label" class="ph-loading-text ph-loading-hidden"><div class="ph-lds-ellipsis"><div></div><div></div><div></div><div></div></div><div>'. Text::_('COM_PHOCAGALLERY_LOADING') . '</div></div>';

}
?>
