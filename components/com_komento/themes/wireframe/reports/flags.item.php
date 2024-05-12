<?php
/**
 * @package		Komento
 * @copyright	Copyright (C) 2012 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * Komento is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');
?>

<tr id="kmt-<?php echo $row->id; ?>" class="kmt-item" parentid="kmt-<?php echo $row->parent_id; ?>">
<!-- Checkbox -->
<td><?php echo $pagination->getRowOffset( $i ); ?></td>

<!-- Comment details -->
<td>
	<ul class="kmt-head">
		<!-- Avatar -->
		<?php if( $this->config->get( 'layout_avatar_enable' ) ) { ?>
		<li class="kmt-avatar">
			<?php if( !KT::user($row->created_by)->guest) { ?>
				<a href="<?php echo KT::user($row->created_by)->getProfileLink(); ?>">
			<?php } ?>
			<img src="<?php echo KT::user($row->created_by)->getAvatar(); ?>" class="avatar" />
			<?php if (!KT::user($row->created_by)->guest) { ?>
				</a>
			<?php } ?>
		</li>
		<?php } ?>

		<!-- Content Title -->
		<li class="kmt-content-title"><a href="<?php echo KT::loadApplication( $row->component )->load( $row->cid )->getContentPermalink(); ?>"><?php echo KT::getExtension( $row->component )->load( $row->cid )->getContentTitle(); ?></a></li>


		<!-- Name -->
		<li class="kmt-author">
			<?php if (!KT::user($row->created_by)->guest ) { ?>
				<a href="<?php echo KT::user($row->created_by)->getProfileLink(); ?>">
			<?php }

				echo KT::user($row->created_by)->getName();

				if( !KT::user($row->created_by)->guest) { ?>
				</a>
			<?php } ?>
		</li>

		<!-- Time -->
		<li class="kmt-date">
			<?php if( $this->config->get( 'enable_lapsed_time') ) {
				echo KomentoDateHelper::getLapsedTime( $row->created );
			} else {
				echo $row->created;
			} ?>
		</li>

		<!-- Permalink -->
		<li class="kmt-permalink"><a href="<?php echo KT::loadApplication( $row->component )->load( $row->cid )->getContentPermalink() . '#kmt-' . $row->id; ?>"><?php echo JText::_( 'COM_KOMENTO_COMMENT_PERMALINK' ) ; ?></a></li>

		<!-- Status -->
		<li class="kmt-status"><?php echo $row->published ? JText::_( 'COM_KOMENTO_PUBLISHED' ) : JText::_( 'COM_KOMENTO_UNPUBLISHED' );?></li>
	</ul>

	<div class="kmt-body">

		<?php // parseBBcode to HTML
			$row->comment = KomentoCommentHelper::parseBBCode($row->comment);
			$row->comment = nl2br($row->comment);
		?>
		<span class="kmt-text"><?php echo $row->comment; ?></span>

	</div>
</td>

<!-- Flag details -->
<td>
	<ul class="kmt-info">
		<?php if($row->spam) { ?>
		<li><?php echo JText::_( 'COM_KOMENTO_SPAM' ) . ': ' . $row->spam; ?></li>
		<?php } ?>

		<?php if($row->offensive) { ?>
		<li><?php echo JText::_( 'COM_KOMENTO_OFFENSIVE' ) . ': ' . $row->offensive; ?></li>
		<?php } ?>

		<?php if($row->offtopic) { ?>
		<li><?php echo JText::_( 'COM_KOMENTO_OFFTOPIC' ) . ': ' . $row->offtopic; ?></li>
		<?php } ?>
	</ul>
</td>

<!-- Resolve -->
<td>
	<ul class="kmt-resolve">
		<?php if( $this->my->allow( 'manage_flag', $row->component ) ) { ?>
		<li><a href="javascript:void(0);" class="kmt-noflag"><?php echo JText::_( 'COM_KOMENTO_CLEAR' ); ?></a></li>
		<li><a href="javascript:void(0);" class="kmt-spam"><?php echo JText::_( 'COM_KOMENTO_SPAM' ); ?></a></li>
		<li><a href="javascript:void(0);" class="kmt-offensive"><?php echo JText::_( 'COM_KOMENTO_OFFENSIVE' ); ?></a></li>
		<li><a href="javascript:void(0);" class="kmt-offtopic"><?php echo JText::_( 'COM_KOMENTO_OFFTOPIC' ); ?></a></li>
		<?php } ?>
		<?php if( $row->published == 1 && ( $this->my->allow( 'publish_all_comment', $row->component ) || ( $row->created_by == $this->my->id && $this->my->allow( 'publish_own_comment', $row->component ) ) ) ) { ?>
		<li><a href="javascript:void(0);" class="kmt-unpublish"><?php echo JText::_( 'COM_KOMENTO_UNPUBLISHED' ); ?></a></li>
		<?php } ?>

		<?php if( $this->my->allow( 'delete_all_comment', $row->component ) || ( $row->created_by == $this->my->id && $this->my->allow( 'delete_own_comment', $row->component ) ) ) { ?>
		<li><a href="javascript:void(0)" class="kmt-delete"><?php echo JText::_( 'COM_KOMENTO_COMMENT_DELETE' ) ; ?></a></li>
		<?php } ?>
	</ul>
</td>
</tr>
