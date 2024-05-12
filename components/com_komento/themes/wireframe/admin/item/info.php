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
?>
<?php if ($this->config->get('enable_info') && $row->modified_by) { ?>
<span class="commentInfo kmt-info">
	<?php 
	$modified = $row->modified;
	
	if ($this->config->get('enable_lapsed_time')) {
		$modified = FH::date()->getLapsedTime($row->modified);
	}
	?>
	
	<?php echo JText::sprintf('COM_KOMENTO_COMMENT_EDITTED_BY', $modified, KT::user($row->modified_by)->getName()); ?>

	<!-- Extended data for schema purposes -->
	<?php if ($this->config->get('enable_schema')) { ?>
	<span class="hidden" itemprop="editor" itemscope itemtype="http://schema.org/Person">
		<span itemprop="name"><?php echo KT::user($row->modified_by)->getName(); ?></span>
	</span>
	<time class="hidden" itemprop="dateModified" datetime="<?php echo FH::date($row->modified)->format('c'); ?>"></time>
	<?php } ?>
</span>
<?php } else { ?>
	<span class="commentInfo kmt-info hidden"></span>
<?php }
