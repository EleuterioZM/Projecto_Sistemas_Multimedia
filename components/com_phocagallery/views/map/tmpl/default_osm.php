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
use Joomla\CMS\HTML\HTMLHelper;
phocagalleryimport('phocagallery.render.rendermaposm');
phocagalleryimport('phocagallery.text.text');
if ($this->t['backbutton'] != '' && $this->t['enable_multibox_iframe'] != 1) {
	echo $this->t['backbutton'];
}

if (empty($this->map) || $this->map->longitude == '' || $this->map->latitude == '') {
	echo '<p>' . Text::_('COM_PHOCAGALLERY_ERROR_MAP_NO_DATA') . '</p>';
} else {
	$text = '<div style="text-align:left"><table style="" border="0" cellspacing="5" cellpadding="5"><tr><td align="left" colspan="2"><b>'. addslashes((string)$this->map->geotitle).'</b></td></tr>';
	$text .='<tr>';
	$text .='<td valign="top" align="left">'.HTMLHelper::_( 'image', $this->map->thumbnail, addslashes((string)$this->map->geotitle)) . '</td>';
	$text .='<td valign="top" align="left">'. PhocaGalleryText::strTrimAll(addslashes((string)$this->map->description)).'</td>';
	$text .='</tr></table></div>';

	$id		= uniqid();
$map	= new PhocaGalleryRenderMaposm($id);


	echo '<noscript>'.Text::_('COM_PHOCAGALLERY_ERROR_MAP_ENABLE_JAVASCRIPT').'</noscript>';

	$cmw = '';
	if ((int)$this->t['largemapwidth'] > 0) {
		$cmw = 'width:'.$this->t['largemapwidth'].'px;';
	}

	$cmw = 'width: 100%;';


	//$cmh = 'height:'.$this->t['largemapheight'].'px';
	$cmh = 'height: 100vh;';


	echo '<div align="center" style="margin:0;padding:0;margin-top:10px;text-align: center">';
	echo '<div id="phocaGalleryMap'.$id.'" style="margin:0 auto;padding:0;'. $cmw. $cmh.'">';
	echo '</div></div>';


	$map->loadAPI();
	$map->loadCoordinatesJS();

	$map->createMap($this->map->latitude, $this->map->longitude, $this->map->zoom);
	$map->setMapType();
	 $map->setMarker(1, $this->map->geotitle,$this->map->description,$this->map->latitude, $this->map->longitude);
	$map->renderFullScreenControl();
	//$map->renderCurrentPosition();
	//$map->renderSearch('', 'topleft');
	$map->renderMap();
}
if ($this->t['detailwindow'] == 7) {
	echo '<div>&nbsp;</div>';
    echo PhocaGalleryUtils::getExtInfo();
}
?>
