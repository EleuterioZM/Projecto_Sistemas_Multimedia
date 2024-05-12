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
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;
echo '<div id="phocagallery-multipleupload">';
echo '<div style="font-size:1px;height:1px;margin:0px;padding:0px;">&nbsp;</div>';
echo $this->t['mu_response_msg'] ;
echo '<form action="'. Uri::base().'index.php?option=com_phocagallery" >';
//if ($this->t['ftp']) {echo PhocaGalleryFileUpload::renderFTPaccess();}
echo '<h4>';
echo Text::_( 'COM_PHOCAGALLERY_UPLOAD_FILE' ).' [ ';

if ($this->t['uploadmaxsizeread'] != '0 B') {
	echo  Text::_( 'COM_PHOCAGALLERY_MAX_SIZE' ).':&nbsp;'.$this->t['uploadmaxsizeread'].','
	.' ';
}



echo Text::_('COM_PHOCAGALLERY_MAX_RESOLUTION').':&nbsp;'. $this->t['uploadmaxreswidth'].' x '.$this->t['uploadmaxresheight'].' px ]';
echo ' </h4>';

if ($this->t['catidimage'] == 0 || $this->t['catidimage'] == '') {
	echo '<div class="alert alert-error alert-danger">'.Text::_('COM_PHOCAGALLERY_PLEASE_SELECT_CATEGORY_TO_BE_ABLE_TO_UPLOAD_IMAGES').'</div>';
}
echo $this->t['mu_output'];
echo '</form>';
echo '</div>';
?>
