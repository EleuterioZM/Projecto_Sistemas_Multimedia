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
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;
$user 	= Factory::getUser();

//Ordering allowed ?
$ordering = ($this->lists['order'] == 'a.ordering');

//JHtml::_('behavior.tooltip');


$js = '
function insertLink() {';


	$items = array('imageshadow', 'fontcolor', 'bgcolor', 'bgcolorhover', 'imagebgcolor', 'bordercolor', 'bordercolorhover', 'detail','displayname', 'displaydetail', 'displaydownload', 'displaybuttons', 'displaydescription', 'descriptionheight' ,'namefontsize', 'namenumchar', 'enableswitch', 'overlib', 'piclens','float', 'boxspace', 'displayimgrating', 'pluginlink', 'type', 'minboxwidth' );
	$itemsArrayOutput = '';
	foreach ($items as $key => $value) {

		$js .= 'var '.$value.' = document.getElementById("'.$value.'").value;'."\n"
			.'if ('.$value.' != \'\') {'. "\n"
			.''.$value.' = "|'.$value.'="+'.$value.';'."\n"
			.'}';
		$itemsArrayOutput .= '+'.$value;
	}

$js .= '	
	/* Category */
	var categoryid = document.getElementById("filter_catid").value;
	var categoryIdOutput = \'\';
	if (categoryid != \'\') {
		categoryIdOutput = "|categoryid="+categoryid;
	}

	/* Image */
	var imageIdOutput = \'\';
	len = document.getElementsByName("imageid").length;
	for (i = 0; i <len; i++) {
		if (document.getElementsByName(\'imageid\')[i].checked) {
			imageid = document.getElementsByName(\'imageid\')[i].value;
			if (imageid != \'\' && parseInt(imageid) > 0) {
				imageIdOutput = "|imageid="+imageid;
			} else {
				imageIdOutput = \'\';
			}
		}
	}

	if (categoryIdOutput != \'\' &&  parseInt(categoryid) > 0) {
		/*return false;*/
	} else {
		alert("'. Text::_( 'COM_PHOCAGALLERY_PLEASE_SELECT_CATEGORY', true ).'");
		return false;
	}
	if (imageIdOutput != \'\' &&  parseInt(imageid) > 0) {
		/*return false;*/
	} else {
		alert("'. Text::_( 'COM_PHOCAGALLERY_PLEASE_SELECT_IMAGE', true ).'");
		return false;
	}
	var tag = "{phocagallery view=category"+categoryIdOutput+imageIdOutput'. $itemsArrayOutput .'+"}";
	window.parent.jInsertEditorText(tag, \''. $this->t['ename'].'\');
	window.parent.SqueezeBox.close();
}';

Factory::getDocument()->addScriptDeclaration($js);
?>
<div id="phocagallery-links">
<fieldset class="adminform">
<legend><?php echo Text::_('COM_PHOCAGALLERY_IMAGE'); ?></legend>
<form action="<?php echo $this->request_url; ?>" method="post" name="adminForm" id="adminForm">

<table class="admintable" width="100%">
		<tr>
			<td class="key" align="right" width="20%">
				<label for="title">
					<?php echo Text::_( 'COM_PHOCAGALLERY_FILTER' ); ?>
				</label>
			</td>
			<td width="80%">
				<input type="text" name="search" id="search" value="<?php echo PhocaGalleryText::filterValue($this->lists['search'], 'text');?>" class="text_area" onchange="document.adminForm.submit();" />
				<button onclick="this.form.submit();"><?php echo Text::_( 'COM_PHOCAGALLERY_SEARCH' ); ?></button>
				<button onclick="document.getElementById('search').value='';this.form.submit();"><?php echo Text::_( 'COM_PHOCAGALLERY_RESET' ); ?></button>
			</td>
		</tr>
		<tr>
			<td class="key" align="right" nowrap="nowrap">
			<label for="title" nowrap="nowrap">
				<?php echo Text::_( 'COM_PHOCAGALLERY_CATEGORY' ); ?>
			</label>
			</td>
			<td><?php echo $this->lists['catid']; ?></td>
	</tr>
</table>

<div id="editcell">
	<table class="adminlist">
		<thead>
			<tr>
				<th width="1"><?php echo Text::_( 'COM_PHOCAGALLERY_NUM' ); ?></th>
				<th width="1"></th>
				<th class="image" width="2" align="center"><?php echo Text::_('COM_PHOCAGALLERY_IMAGE'); ?></th>
				<th class="title" width="50%"><?php echo HTMLHelper::_('grid.sort',  'COM_PHOCAGALLERY_TITLE', 'a.title', $this->lists['order_Dir'], $this->lists['order'] ); ?>
				</th>
				<th width="20%" nowrap="nowrap"><?php echo HTMLHelper::_('grid.sort',  'COM_PHOCAGALLERY_FILENAME', 'a.filename', $this->lists['order_Dir'], $this->lists['order'] ); ?>
				</th>


				<th width="1%" nowrap="nowrap"><?php echo HTMLHelper::_('grid.sort',  'COM_PHOCAGALLERY_ID', 'a.id', $this->lists['order_Dir'], $this->lists['order'] ); ?>
				</th>
			</tr>
		</thead>

		<tbody>
			<?php
			$k = 0;
			for ($i=0, $n=count( $this->items ); $i < $n; $i++) {
				$row 	= &$this->items[$i];

			?>
			<tr class="<?php echo "row$k"; ?>">
				<td><?php echo $this->t['pagination']->getRowOffset( $i ); ?></td>
				<td><input type="radio" name="imageid" value="<?php echo $row->id ?>" /></td>
				<td align="center" valign="middle">
					<div class="phocagallery-box-file">
						<center>
							<div class="phocagallery-box-file-first">
								<div class="phocagallery-box-file-second">
									<div class="phocagallery-box-file-third">
										<center>
										<?php
										// PICASA of FB

										if (isset($row->extid) && $row->extid !='') {

											$resW	= explode(',', $row->extw);
											$resH	= explode(',', $row->exth);

											$correctImageRes = PhocaGalleryImage::correctSizeWithRate($resW[2], $resH[2], 50, 50);
											//echo JHtml::_( 'image', $row->exts.'?imagesid='.md5(uniqid(time())), '', array('width' => $correctImageRes['width'], 'height' => $correctImageRes['height']));
											echo '<img src="'.$row->exts.'?imagesid='.md5(uniqid(time())).'" width="'.$correctImageRes['width'].'" height="'.$correctImageRes['height'].'" alt="" />';

										} else if (isset ($row->fileoriginalexist) && $row->fileoriginalexist == 1) {

											$imageRes	= PhocaGalleryImage::getRealImageSize($row->filename, 'small');
											$correctImageRes = PhocaGalleryImage::correctSizeWithRate($imageRes['w'], $imageRes['h'], 50, 50);
											 //echo JHtml::_( 'image', $row->linkthumbnailpath.'?imagesid='.md5(uniqid(time())), '', array('width' => $correctImageRes['width'], 'height' => $correctImageRes['height']));
											 echo '<img src="'.Uri::root().$row->linkthumbnailpath.'?imagesid='.md5(uniqid(time())).'" width="'.$correctImageRes['width'].'" height="'.$correctImageRes['height'].'" alt="" />';
										} else {
											//echo JHtml::_( 'image', 'media/com_phocagallery/images/administrator/phoca_thumb_s_no_image.gif');
                                            echo '<img src="'.Uri::root().'media/com_phocagallery/images/administrator/phoca_thumb_s_no_image.gif'.'" alt="" />';
										}
										?>
										</center>
									</div>
								</div>
							</div>
						</center>
					</div>
				</td>

				<?php echo '<td>'. $row->title.'</td>';
				if (isset($row->extid) && $row->extid !='') {
					if (isset($row->exttype) && $row->exttype == 1) {
						echo '<td align="center">'.Text::_('COM_PHOCAGALLERY_FACEBOOK_STORED_FILE').'</td>';
					} else {
						echo '<td align="center">'.Text::_('COM_PHOCAGALLERY_PICASA_STORED_FILE').'</td>';
					}
				} else {
					echo '<td>' .$row->filename.'</td>';
				} ?>
				<td align="center"><?php echo $row->id; ?></td>
			</tr>
			<?php
			$k = 1 - $k;
			}
		?>
		</tbody>

		<tfoot>
			<tr>
				<td colspan="6"><?php echo $this->t['pagination']->getListFooter(); ?></td>
			</tr>
		</tfoot>
	</table>
</div>


<input type="hidden" name="controller" value="phocagallerylinkimg" />
<input type="hidden" name="type" value="<?php echo $this->t['type']; ?>" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
<input type="hidden" name="e_name" value="<?php echo $this->t['ename']?>" />
</form>


<form name="adminFormLink" id="adminFormLink">
<table class="admintable" width="100%">

    <?php
    /*
    ?>
	<tr>
		<td class="key" align="right" width="20%"><label for="imagecategories"><?php echo Text::_( 'COM_PHOCAGALLERY_IMAGE_BACKGROUND_SHADOW' ); ?></label></td>
		<td width="80%">
			<select name="imageshadow" id="imageshadow">
			<option value=""  selected="selected"><?php echo Text::_( 'COM_PHOCAGALLERY_DEFAULT' )?></option>
			<option value="none" ><?php echo Text::_('COM_PHOCAGALLERY_NONE'); ?></option>
			<option value="shadow1" ><?php echo Text::_( 'COM_PHOCAGALLERY_SHADOW1' ); ?></option>
			<option value="shadow2" ><?php echo Text::_( 'COM_PHOCAGALLERY_SHADOW2' ); ?></option>
			<option value="shadow3" ><?php echo Text::_( 'COM_PHOCAGALLERY_SHADOW3' ); ?></option>
			</select>
		</td>
	</tr>


	// Colors
	$itemsColor = array ('fontcolor' => 'COM_PHOCAGALLERY_FIELD_FONT_COLOR_LABEL', 'bgcolor' => 'COM_PHOCAGALLERY_FIELD_BACKGROUND_COLOR_LABEL', 'bgcolorhover' => 'COM_PHOCAGALLERY_FIELD_BACKGROUND_COLOR_HOVER_LABEL', 'imagebgcolor' => 'COM_PHOCAGALLERY_FIELD_IMAGE_BACKGROUND_COLOR_LABEL', 'bordercolor' => 'COM_PHOCAGALLERY_FIELD_BORDER_COLOR_LABEL', 'bordercolorhover' => 'COM_PHOCAGALLERY_FIELD_BORDER_COLOR_HOVER_LABEL');
	foreach ($itemsColor as $key => $value) {
		echo '<tr>'
		.'<td class="key" align="right" width="20%"><label for="'.$key.'">'.Text::_($value).'</label></td>'
		.'<td nowrap="nowrap"><input type="text" name="'.$key.'" id="'.$key.'" value="" class="text_area" /><span style="margin-left:10px" onclick="openPicker(\''.$key.'\')"  class="picker_buttons">'. Text::_('COM_PHOCAGALLERY_PICK_COLOR').'</span></td>'
		.'</tr>';
	}


	<tr>
		<td class="key" align="right" width="20%"><label for="detail"><?php echo Text::_( 'COM_PHOCAGALLERY_DETAIL_WINDOW' ); ?></label></td>
		<td width="80%">
		<select name="detail" id="detail" class="form-control">
		<option value=""  selected="selected"><?php echo Text::_( 'COM_PHOCAGALLERY_DEFAULT' )?></option>
		<option value="1" ><?php echo Text::_( 'COM_PHOCAGALLERY_STANDARD_POPUP_WINDOW' ); ?></option>
		<option value="0" ><?php echo Text::_( 'COM_PHOCAGALLERY_MODAL_POPUP_BOX' ); ?></option>
		<option value="2" ><?php echo Text::_( 'COM_PHOCAGALLERY_MODAL_POPUP_BOX_IMAGE_ONLY' ); ?></option>
		<option value="3" ><?php echo Text::_( 'COM_PHOCAGALLERY_SHADOWBOX' ); ?></option>
		<option value="4" ><?php echo Text::_( 'COM_PHOCAGALLERY_HIGHSLIDE' ); ?></option>
		<option value="5" ><?php echo Text::_( 'COM_PHOCAGALLERY_HIGHSLIDE_IMAGE_ONLY' ); ?></option>
		<option value="6" ><?php echo Text::_( 'COM_PHOCAGALLERY_JAK_LIGHTBOX' ); ?></option>
		<option value="8" ><?php echo Text::_( 'COM_PHOCAGALLERY_SLIMBOX' ); ?></option>
		<?php /*<option value="7" >No Popup</option>*/ ?>
		</select></td>
	</tr>
<?php

		echo '<tr>'
		.'<td class="key" align="right" width="20%"><label for="pluginlink">'.Text::_('COM_PHOCAGALLERY_PLUGIN_LINK').'</label></td>'
		.'<td nowrap><select name="pluginlink" id="pluginlink" class="form-control">'
		.'<option value=""  selected="selected">'. Text::_( 'COM_PHOCAGALLERY_DEFAULT' ).'</option>'
		.'<option value="0" >'.Text::_( 'COM_PHOCAGALLERY_LINK_TO_DETAIL_IMAGE' ).'</option>'
		.'<option value="1" >'.Text::_( 'COM_PHOCAGALLERY_LINK_TO_CATEGORY' ).'</option>'
		.'<option value="2" >'.Text::_( 'COM_PHOCAGALLERY_LINK_TO_CATEGORIES' ).'</option>';

		echo '<tr>'
		.'<td class="key" align="right" width="20%"><label for="type">'.Text::_('COM_PHOCAGALLERY_PLUGIN_TYPE').'</label></td>'
		.'<td nowrap><select name="type" id="type" class="form-control">'
		.'<option value=""  selected="selected">'. Text::_( 'COM_PHOCAGALLERY_DEFAULT' ).'</option>'
		.'<option value="0" >'.Text::_( 'COM_PHOCAGALLERY_LINK_TO_DETAIL_IMAGE' ).'</option>'
		.'<option value="1" >'.Text::_( 'COM_PHOCAGALLERY_MOSAIC' ).'</option>'
		.'<option value="2" >'.Text::_( 'COM_PHOCAGALLERY_LARGE_IMAGE' ).'</option>';

	// yes/no
    /*
	$itemsYesNo = array ('displayname' => 'COM_PHOCAGALLERY_FIELD_DISPLAY_NAME_LABEL', 'displaydetail' => 'COM_PHOCAGALLERY_FIELD_DISPLAY_DETAIL_ICON_LABEL', 'displaydownload' => 'COM_PHOCAGALLERY_FIELD_DISPLAY_DOWNLOAD_ICON_LABEL', 'displaybuttons' => 'COM_PHOCAGALLERY_FIELD_DISPLAY_BUTTONS_LABEL', 'displaydescription' => 'COM_PHOCAGALLERY_FIELD_DISPLAY_DESCRIPTION_DETAIL_LABEL', 'displayimgrating' => 'COM_PHOCAGALLERY_DISPLAY_IMAGE_RATING' );
	foreach ($itemsYesNo as $key => $value) {
		echo '<tr>'
		.'<td class="key" align="right" width="20%"><label for="'.$key.'">'.Text::_($value).'</label></td>'
		.'<td nowrap><select name="'.$key.'" id="'.$key.'" class="form-control">'
		.'<option value=""  selected="selected">'. Text::_( 'COM_PHOCAGALLERY_DEFAULT' ).'</option>';

		if ($key == 'displaydownload') {
			echo '<option value="1" >'. Text::_( 'COM_PHOCAGALLERY_SHOW' ).'</option>'
			.'<option value="2" >'.Text::_( 'COM_PHOCAGALLERY_SHOW_DIRECT_DOWNLOAD' ).'</option>'
			.'<option value="0" >'.Text::_( 'COM_PHOCAGALLERY_HIDE' ).'</option>';
		} else {
			echo '<option value="1" >'. Text::_( 'COM_PHOCAGALLERY_SHOW' ).'</option>'
			.'<option value="0" >'.Text::_( 'COM_PHOCAGALLERY_HIDE' ).'</option>';
		}
		echo '</select></td>'
		.'</tr>';
	}*/


	?>



	<tr>
		<td>&nbsp;</td>
		<td align="right"><button class="btn btn-primary" onclick="insertLink();return false;"><span class="icon-ok"></span> <?php echo Text::_( 'COM_PHOCAGALLERY_INSERT_CODE' ); ?></button></td>
	</tr>
</table>
</form>

</fieldset>
<div style="text-align:left;"><span class="icon-16-edb-back"><a style="text-decoration:underline" href="<?php echo $this->t['backlink'];?>"><?php echo Text::_('COM_PHOCAGALLERY_BACK')?></a></span></div>
</div>
