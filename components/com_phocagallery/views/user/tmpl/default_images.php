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
use Joomla\CMS\Router\Route;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Factory;


echo '<div id="phocagallery-upload">'.$this->t['iepx'];

if ($this->t['displayupload'] == 1) {
	if ($this->t['categorypublished'] == 0) {
		echo '<p>'.Text::_('COM_PHOCAGALLERY_YOUR_MAIN_CATEGORY_IS_UNPUBLISHED').'</p>';
	} else if ($this->t['task'] == 'editimg' && $this->t['imageedit']) {

?><h4><?php echo Text::_('COM_PHOCAGALLERY_EDIT'); ?></h4>
<form action="<?php echo htmlspecialchars($this->t['action']);?>" name="phocagalleryuploadform" id="phocagallery-upload-form" method="post" >
<table>
	<tr>
		<td><?php echo Text::_('COM_PHOCAGALLERY_TITLE');?>:</td>
		<td><input type="text" id="imagename" name="imagename" maxlength="255" class="comment-input" value="<?php echo $this->t['imageedit']->title ?>" /></td>
	</tr>

	<tr>
		<td><?php echo Text::_( 'COM_PHOCAGALLERY_DESCRIPTION' ); ?>:</td>
		<td><textarea id="phocagallery-upload-description" name="phocagalleryuploaddescription" onkeyup="countCharsUpload();" cols="30" rows="10" class="form-control comment-input"><?php echo $this->t['imageedit']->description; ?></textarea></td>
	</tr>

	<tr>
		<td>&nbsp;</td>
		<td><?php echo Text::_('COM_PHOCAGALLERY_CHARACTERS_WRITTEN');?> <input name="phocagalleryuploadcountin" value="0" readonly="readonly" class="form-control comment-input2" /> <?php echo Text::_('COM_PHOCAGALLERY_AND_LEFT_FOR_DESCRIPTION');?> <input name="phocagalleryuploadcountleft" value="<?php echo $this->t['maxcreatecatchar'];?>" readonly="readonly" class="comment-input2" />
		</td>
	</tr>

	<tr>
		<td>&nbsp;</td>
		<td align="right"><input type="button" onclick="window.location='<?php echo Route::_($this->t['pp'].$this->t['psi']);?>'" id="phocagalleryimagecancel" value="<?php echo Text::_('COM_PHOCAGALLERY_CANCEL'); ?>"/> <input type="submit" onclick="return(checkCreateImageForm());" id="phocagalleryimagesubmit" value="<?php echo Text::_('COM_PHOCAGALLERY_EDIT'); ?>"/></td>
	</tr>
</table>

<?php echo HTMLHelper::_( 'form.token' ); ?>
<input type="hidden" name="task" value="editimage"/>
<input type="hidden" name="controller" value="user" />
<input type="hidden" name="view" value="user"/>
<input type="hidden" name="tab" value="<?php echo $this->t['currenttab']['images'];?>" />
<input type="hidden" name="limitstartsubcat" value="<?php echo $this->t['subcategorypagination']->limitstart;?>" />
<input type="hidden" name="limitstartimage" value="<?php echo $this->t['imagepagination']->limitstart;?>" />
<input type="hidden" name="Itemid" value="<?php echo $this->itemId ?>"/>
<input type="hidden" name="id" value="<?php echo $this->t['imageedit']->id ?>"/>
<input type="hidden" name="parentcategoryid" value="<?php echo $this->t['parentcategoryid'] ?>"/>
<input type="hidden" name="filter_order_image" value="<?php echo $this->listsimage['order']; ?>" />
<input type="hidden" name="filter_order_Dir_image" value="" />
</form>
<?php
	} else {


?><h4><?php echo Text::_( 'COM_PHOCAGALLERY_IMAGES' ); ?></h4>
<div style="float:left" class="filter-search btn-group pull-left" >
        <form action="<?php echo htmlspecialchars($this->t['action']);?>" method="post" name="phocagalleryimageform" id="phocagalleryimageform">


		<?php /* <td align="left" width="100%"><?php echo JText::_( 'COM_PHOCAGALLERY_FILTER' ); ?>:
		<input type="text" name="phocagalleryimagesearch" id="phocagalleryimagesearch" value="<?php echo $this->listsimage['search'];?>" onchange="document.phocagalleryimageform.submit();" />
		<button onclick="this.form.submit();"><?php echo Text::_( 'COM_PHOCAGALLERY_SEARCH' ); ?></button>
		<button onclick="document.getElementById('phocagalleryimagesearch').value='';document.phocagalleryimageform.submit();"><?php echo Text::_( 'COM_PHOCAGALLERY_RESET' ); ?></button></td>
		<td nowrap="nowrap"><?php echo $this->listsimage['catid']; echo $this->listsimage['state'];?></td> */ ?>


		<div class="filter-search btn-group pull-left">
		<label for="filter_search" class="element-invisible"><?php echo Text::_( 'COM_PHOCAGALLERY_FILTER' ); ?></label>
		<input type="text" name="phocagalleryimagesearch" id="phocagalleryimagesearch" placeholder="<?php echo Text::_( 'COM_PHOCAGALLERY_SEARCH' ); ?>" value="<?php echo $this->listsimage['search'];?>" title="<?php echo Text::_( 'COM_PHOCAGALLERY_SEARCH' ); ?>" /></div>

		<div class="btn-group pull-left hidden-phone">
		<button class="btn btn-primary tip hasTooltip" type="submit" onclick="this.form.submit();"  title="<?php echo Text::_( 'COM_PHOCAGALLERY_SEARCH' ); ?>"><?php echo '<svg class="ph-si ph-si-search"><title>'.Text::_('COM_PHOCAGALLERY_SEARCH').'</title><use xlink:href="#ph-si-search"></use></svg>' ?></button>
		<button class="btn btn-secondary tip hasTooltip" type="button" onclick="document.getElementById('phocagalleryimagesearch').value='';document.getElementById(\'phocagalleryimageform\').submit();" title="<?php echo Text::_( 'COM_PHOCAGALLERY_CLEAR' ); ?>"><?php echo Text::_( 'COM_PHOCAGALLERY_CLEAR' ); ?></button>
		</div>

		</div>
        <div class="ph-adminlist-select-row"><?php echo $this->listsimage['catid'] ?> <?php echo $this->listsimage['state']; ?></div>

<table class="ph-adminlist">
<thead>
	<tr>
	<th width="5"><?php echo Text::_( 'COM_PHOCAGALLERY_NUM' ); ?></th>
	<th class="image" width="3%" align="center"><?php echo Text::_( 'COM_PHOCAGALLERY_IMAGE' ); ?></th>
	<th class="title" width="15%"><?php echo PhocaGalleryGrid::sort(  'COM_PHOCAGALLERY_TITLE', 'a.title', $this->listsimage['order_Dir'], $this->listsimage['order'], 'image', 'asc', '', 'phocagalleryimageform', '_image'); ?></th>
	<th width="3%" nowrap="nowrap"><?php echo PhocaGalleryGrid::sort(   'COM_PHOCAGALLERY_PUBLISHED', 'a.published', $this->listsimage['order_Dir'], $this->listsimage['order'], 'image', 'asc', '', 'phocagalleryimageform' , '_image'); ?></th>
	<th width="3%" nowrap="nowrap"><?php echo Text::_('COM_PHOCAGALLERY_DELETE'); ?></th>
	<th width="3%" nowrap="nowrap"><?php echo PhocaGalleryGrid::sort(   'COM_PHOCAGALLERY_APPROVED', 'a.approved', $this->listsimage['order_Dir'], $this->listsimage['order'], 'image', 'asc', '', 'phocagalleryimageform', '_image' ); ?></th>
	<th width="80" nowrap="nowrap" align="center">

	<?php echo PhocaGalleryGrid::sort(   'COM_PHOCAGALLERY_ORDER', 'a.ordering', $this->listsimage['order_Dir'], $this->listsimage['order'],'image', 'asc', '', 'phocagalleryimageform', '_image' );
	//$image = '<img src="'.JUri::base(true).'/'.$this->t['pi'].'icon-filesave.png'.'" width="16" height="16" border="0" alt="'.JText::_( 'COM_PHOCAGALLERY_SAVE_ORDER' ).'" />';

	//$image = PhocaGalleryRenderFront::renderIcon('save', $this->t['pi'].'icon-filesave.png', JText::_('COM_PHOCAGALLERY_SAVE_ORDER'));
	$image = '<svg class="ph-si ph-si-save"><title>'.Text::_('COM_PHOCAGALLERY_SAVE_ORDER').'</title><use xlink:href="#ph-si-save"></use></svg>';

	$task = 'saveordersubcat';
	$href = '<a href="javascript:saveorderimage()" title="'.Text::_( 'COM_PHOCAGALLERY_SAVE_ORDER' ).'"> '.$image.'</a>';
	echo $href;
	?></th>
	<th width="3%" nowrap="nowrap"><?php echo PhocaGalleryGrid::sort(  'COM_PHOCAGALLERY_CATEGORY' , 'a.catid', $this->listsimage['order_Dir'], $this->listsimage['order'], 'image', 'asc', '', 'phocagalleryimageform', '_image' ); ?></th>

	<th width="1%" nowrap="nowrap"><?php echo PhocaGalleryGrid::sort(   'COM_PHOCAGALLERY_ID', 'a.id', $this->listsimage['order_Dir'], $this->listsimage['order'] , 'image',  'asc', '', 'phocagalleryimageform', '_image'); ?></th>
	</tr>
</thead>

<tbody><?php
$k 		= 0;
$i 		= 0;
$n 		= count( $this->t['imageitems'] );
$rows 	= &$this->t['imageitems'];

if (is_array($rows)) {
	foreach ($rows as $row) {
		$linkEdit 	= Route::_( $this->t['pp'].'&task=editimg&id='. $row->slug.$this->t['psi'] );

	?><tr class="<?php echo "row$k"; ?>">
	<td>
		<input type="hidden" id="cb<?php echo $k ?>" name="cid[]" value="<?php echo $row->id ?>" />
		<?php
		echo $this->t['imagepagination']->getRowOffset( $i );?>
	</td>
	<td align="center" valign="middle">
	<?php
	$row->linkthumbnailpath = PhocaGalleryImageFront::displayCategoryImageOrNoImage($row->filename, 'small');
	$imageRes	= PhocaGalleryImage::getRealImageSize($row->filename, 'small');
	$correctImageRes = PhocaGalleryImage::correctSizeWithRate($imageRes['w'], $imageRes['h'], 50, 50);
	//echo JHtml::_( 'image', $row->linkthumbnailpath.'?imagesid='.md5(uniqid(time())),'', array('width' => $correctImageRes['width'], 'height' => $correctImageRes['height']));
	echo '<img src="'.Uri::root().$row->linkthumbnailpath.'?imagesid='.md5(uniqid(time())).'" width="'.$correctImageRes['width'].'" height="'.$correctImageRes['height'].'" alt="" />';

	?>
	</td>

	<td><a href="<?php echo $linkEdit; ?>" title="<?php echo Text::_( 'COM_PHOCAGALLERY_EDIT_IMAGE' ); ?>"><?php echo $row->title; ?></a></td>
	<?php

	// Publish Unpublish
	echo '<td align="center">';
	if ($row->published == 1) {
		echo ' <a title="'.Text::_('COM_PHOCAGALLERY_UNPUBLISH').'" href="'. Route::_($this->t['pp'].'&id='.$row->slug.'&task=unpublishimage'. $this->t['psi']).'">';
		//echo JHtml::_('image', $this->t['pi'].'icon-publish.png', JText::_('COM_PHOCAGALLERY_UNPUBLISH'))
		//echo PhocaGalleryRenderFront::renderIcon('publish', $this->t['pi'].'icon-publish.png', JText::_('COM_PHOCAGALLERY_UNPUBLISH'))
        echo '<svg class="ph-si ph-si-enabled"><title>'.Text::_('COM_PHOCAGALLERY_UNPUBLISH').'</title><use xlink:href="#ph-si-enabled"></use></svg>'
		.'</a>';
	}
	if ($row->published == 0) {
		echo ' <a title="'.Text::_('COM_PHOCAGALLERY_PUBLISH').'" href="'. Route::_($this->t['pp'].'&id='.$row->slug.'&task=publishimage'.$this->t['psi']).'">';
		//echo JHtml::_('image', $this->t['pi'].'icon-unpublish.png', JText::_('COM_PHOCAGALLERY_PUBLISH'))
		//echo PhocaGalleryRenderFront::renderIcon('unpublish', $this->t['pi'].'icon-unpublish.png', JText::_('COM_PHOCAGALLERY_PUBLISH'))
        echo '<svg class="ph-si ph-si-disabled"><title>'.Text::_('COM_PHOCAGALLERY_PUBLISH').'</title><use xlink:href="#ph-si-disabled"></use></svg>'
		.'</a>';
	}
	echo '</td>';

	// Remove
	echo '<td align="center">';

	// USER RIGHT - Delete (Publish/Unpublish) - - - - - - - - - - -
	// 2, 2 means that user access will be ignored in function getUserRight for display Delete button
	// because we cannot check the access and delete in one time
	$rightDisplayDelete	= 0;
	$user 				= Factory::getUser();
	$model 				= $this->getModel('user');
	$isOwnerCategory 	= $model->isOwnerCategoryImage((int)$user->id, (int)$row->id);

	$catAccess	= PhocaGalleryAccess::getCategoryAccess((int)$isOwnerCategory);
	if (!empty($catAccess)) {
		$rightDisplayDelete = PhocaGalleryAccess::getUserRight('deleteuserid', $catAccess->deleteuserid, 2, $user->getAuthorisedViewLevels(), $user->get('id', 0), 0);
	}
	// - - - - - - - - - - - - - - - - - - - - - -

	if ($rightDisplayDelete) {
		echo ' <a onclick="return confirm(\''.Text::_('COM_PHOCAGALLERY_WARNING_DELETE_ITEMS').'\')" title="'.Text::_('COM_PHOCAGALLERY_DELETE').'" href="'. Route::_($this->t['pp'].'&id='.$row->slug.'&task=removeimage'.$this->t['psi'] ).'">';
		//echo JHtml::_('image',  $this->t['pi'].'icon-trash.png', JText::_('COM_PHOCAGALLERY_DELETE'))
		//echo PhocaGalleryRenderFront::renderIcon('trash', $this->t['pi'].'icon-trash.png', JText::_('COM_PHOCAGALLERY_DELETE') )
        echo '<svg class="ph-si ph-si-trash"><title>'.Text::_('COM_PHOCAGALLERY_DELETE').'</title><use xlink:href="#ph-si-trash"></use></svg>'
		.'</a>';
	} else {
		//echo JHtml::_('image', $this->t['pi'].'icon-trash-g.png', JText::_('COM_PHOCAGALLERY_DELETE'));
		//echo PhocaGalleryRenderFront::renderIcon('trash', $this->t['pi'].'icon-trash-g.png', JText::_('COM_PHOCAGALLERY_DELETE'),'ph-icon-disabled');
		echo '<svg class="ph-si ph-si-disabled"><title>'.Text::_('COM_PHOCAGALLERY_DELETE').'</title><use xlink:href="#ph-si-disabled"></use></svg>';
	}
	echo '</td>';

	// Approved
	echo '<td align="center">';
	if ($row->approved == 1) {
		//echo JHtml::_('image', $this->t['pi'].'icon-publish.png', JText::_('COM_PHOCAGALLERY_APPROVED'));
		//echo PhocaGalleryRenderFront::renderIcon('publish', $this->t['pi'].'icon-publish.png', JText::_('COM_PHOCAGALLERY_APPROVED'));
		echo '<svg class="ph-si ph-si-enabled"><title>'.Text::_('COM_PHOCAGALLERY_APPROVED').'</title><use xlink:href="#ph-si-enabled"></use></svg>';
	} else {
		//echo JHtml::_('image', $this->t['pi'].'icon-unpublish.png', JText::_('COM_PHOCAGALLERY_NOT_APPROVED'));
		//echo PhocaGalleryRenderFront::renderIcon('unpublish', $this->t['pi'].'icon-unpublish.png', JText::_('COM_PHOCAGALLERY_NOT_APPROVED'));
		echo '<svg class="ph-si ph-si-disabled"><title>'.Text::_('COM_PHOCAGALLERY_NOT_APPROVED').'</title><use xlink:href="#ph-si-disabled"></use></svg>';
	}
	echo '</td>';

	$linkUp 	= Route::_($this->t['pp'].'&id='.$row->slug.'&task=orderupimage'.$this->t['psi']);
	$linkDown 	= Route::_($this->t['pp'].'&id='.$row->slug.'&task=orderdownimage'.$this->t['psi']);

	echo '<td class="order" align="right">'
	.'<span>'. $this->t['imagepagination']->orderUpIcon( $i, ($row->catid == @$this->t['imageitems'][$i-1]->catid), $linkUp, 'COM_PHOCAGALLERY_MOVE_UP', $this->t['imageordering']).'</span> '
	.'<span>'. $this->t['imagepagination']->orderDownIcon( $i, $n, ($row->catid == @$this->t['imageitems'][$i+1]->catid), $linkDown, 'COM_PHOCAGALLERY_MOVE_UP', $this->t['imageordering'] ).'</span> ';

	$disabled = $this->t['imageordering'] ?  '' : 'disabled="disabled"';
	echo '<input type="text" name="order[]" size="5" value="'. $row->ordering.'" '. $disabled.' class="form-control inputbox input-mini" style="text-align: center" />';
	echo '</td>';

	echo '<td align="center">'. $row->category .'</td>';
	echo '<td align="center">'. $row->id .'</td>'
	.'</tr>';

		$k = 1 - $k;
		$i++;
	}
}
?></tbody>
<tfoot>
	<tr>
	<td colspan="9" class="footer"><?php

$this->t['imagepagination']->setTab($this->t['currenttab']['images']);
if (count($this->t['imageitems'])) {
	echo '<div class="pagination pg-center">';
	echo '<div class="pg-inline">'
		.Text::_('COM_PHOCAGALLERY_DISPLAY_NUM') .'&nbsp;'
		.$this->t['imagepagination']->getLimitBox()
		.'</div>';
	echo '<div style="margin:0 10px 0 10px;display:inline;" class="sectiontablefooter'.$this->params->get( 'pageclass_sfx' ).'" >'
		.$this->t['imagepagination']->getPagesLinks()
		.'</div>'
		.'<div style="margin:0 10px 0 10px;display:inline;" class="pagecounter">'
		.$this->t['imagepagination']->getPagesCounter()
		.'</div>';
	echo '</div>';
}

?></td>
	</tr>
</tfoot>
</table>


<?php echo HTMLHelper::_( 'form.token' ); ?>

<input type="hidden" name="controller" value="user" />
<input type="hidden" name="task" value=""/>
<input type="hidden" name="view" value="user"/>
<input type="hidden" name="tab" value="<?php echo $this->t['currenttab']['images'];?>" />
<input type="hidden" name="limitstartsubcat" value="<?php echo $this->t['subcategorypagination']->limitstart;?>" />
<input type="hidden" name="limitstartimage" value="<?php echo $this->t['imagepagination']->limitstart;?>" />
<input type="hidden" name="Itemid" value="<?php echo $this->itemId ?>"/>
<input type="hidden" name="catid" value="<?php echo $this->t['catidimage'] ?>"/>
<input type="hidden" name="filter_order_image" value="<?php echo $this->listsimage['order']; ?>" />
<input type="hidden" name="filter_order_Dir_image" value="" />

</form>
<p>&nbsp;</p>
<?php


	if ((int)$this->t['displayupload'] == 1) {
		echo '<h4>'. Text::_('COM_PHOCAGALLERY_SINGLE_FILE_UPLOAD').'</h4>';
		echo $this->loadTemplate('upload');
	}

	if ((int)$this->t['ytbupload'] > 0) {
		echo '<h4>'. Text::_('COM_PHOCAGALLERY_YTB_UPLOAD').'</h4>';
		echo $this->loadTemplate('ytbupload');
	}

	if((int)$this->t['enablemultiple']  == 1) {
		echo '<h4>'. Text::_('COM_PHOCAGALLERY_MULTPLE_FILE_UPLOAD').'</h4>';
		echo $this->loadTemplate('multipleupload');
	}

	/*if($this->t['enablejava'] == 1) {
		echo '<h4>'. Text::_('COM_PHOCAGALLERY_JAVA_UPLOAD').'</h4>';
		echo $this->loadTemplate('javaupload');
	}*/


	}
} else {
	echo '<div>'.Text::_('COM_PHOCAGALLERY_NO_CATEGORY_TO_UPLOAD_IMAGES').'</div>';
	echo '<div>'.Text::_('COM_PHOCAGALLERY_NO_CATEGORY_TO_UPLOAD_IMAGES_ADMIN').'</div>';
}
echo '</div>';
?>
