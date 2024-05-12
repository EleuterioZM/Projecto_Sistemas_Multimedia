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

if ($this->config->get('enable_location')) { 
?>
<div class="kmt-location mt-5 mb-5"<?php if( $this->config->get( 'enable_schema' ) ) echo ' itemprop="contentLocation" itemscope itemtype="http://schema.org/Place"'; ?>>
	<?php if( $row->address && $row->latitude && $row->longitude ) { ?>
		<?php echo $this->fd->html('icon.font', 'fdi fa fa-map-marker'); ?>
		<?php echo JText::_('COM_KOMENTO_COMMENT_FROM');?> <a href="http://maps.google.com/maps?z=15&amp;q=<?php echo FH::escape( $row->latitude ); ?>,<?php echo FH::escape( $row->longitude );?>" target="_blank"><?php echo FH::escape( $row->address );?></a>
	<?php } else { ?>
		<?php if( $row->address ) { ?>
			<?php echo $this->fd->html('icon.font', 'fdi fa fa-map-marker'); ?>
			<?php echo JText::_( 'COM_KOMENTO_COMMENT_FROM' ); ?><span<?php if( $this->config->get( 'enable_schema' ) ) echo ' itemprop="address"'; ?>><?php echo FH::escape( $row->address ); ?></span>
		<?php } ?>
	<?php } ?>

	<!-- Extended data for schema purposes -->
	<?php if( $this->config->get( 'enable_schema' ) ) { ?>
	<span class="hidden" itemprop="geo" itemscope itemtype="http://schema.org/GeoCoordinates">
		<span itemprop="latitude"><?php echo FH::escape( $row->longitude ); ?></span>
		<span itemprop="longitude"><?php echo FH::escape( $row->latitude ); ?></span>
	</span>
	<span class="hidden" itemprop="map">http://maps.google.com/maps?z=15&q=<?php echo FH::escape( $row->latitude );?>,<?php echo FH::escape( $row->longitude );?></span>
	<?php } ?>
</div>
<?php }
