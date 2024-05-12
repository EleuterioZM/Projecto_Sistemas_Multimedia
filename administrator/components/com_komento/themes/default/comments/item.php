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
<tr id="<?php echo 'kmt-' . $comment->id; ?>" class="kmt-row" childs="<?php echo $comment->hasReplies() ? 1 : 0; ?>" depth="<?php echo $comment->depth; ?>" parentid="<?php echo $comment->parent_id; ?>">
	

	<td>
		<div>
			<a href="<?php echo JRoute::_('index.php?option=com_komento&view=comments&from=' . $layout . '&layout=form&id=' . $comment->id); ?>">
			<?php if (FCJString::strlen($comment->getContent()) > 80) { ?>
				<?php echo FCJString::substr(strip_tags($comment->getContent()), 0, 80); ?>...
				<?php } else { ?> 
				<?php echo FCJString::substr(strip_tags($comment->getContent()), 0); ?>
				<?php } ?>
			</a>
		</div>

		<?php if ($layout !== 'pending') { ?>
		<div class="mt-sm">
			<div class="o-inline-list text-gray-500">
				<div fd-breadcrumb="·">
					<?php echo $this->fd->html('icon.font', 'fdi far fa-thumbs-up'); ?>&nbsp; <?php echo $comment->likes; ?>
				</div>
				<div fd-breadcrumb="·">
					<?php echo $this->fd->html('icon.font', 'fdi far fa-thumbs-down'); ?>&nbsp; <?php echo $comment->dislikes; ?>
				</div>
				<div fd-breadcrumb="·">
					<?php echo $this->fd->html('icon.font', 'fdi fa fa-star'); ?>&nbsp; <?php echo $comment->getRatings(); ?>
				</div>
			</ul>
		</div>
		<?php } ?>
	</td>

	<td class="linked-cell center">
		<?php if (!$search) { ?>
			<?php if ($comment->hasReplies()) { ?>
				<a href="<?php echo JRoute::_('index.php?option=com_komento&view=comments&parentid=' . $comment->id); ?>"><?php echo JText::_('COM_KOMENTO_VIEW_CHILD'); ?></a>
			<?php } else { ?>
				&mdash;
			<?php } ?>
		<?php } else { ?>
			<?php if ($comment->parent_id) { ?>
				<a href="<?php echo JRoute::_('index.php?option=com_komento&view=comments&controller=comment&nosearch=1&parentid=' . $comment->parent_id); ?>"><?php echo JText::_('COM_KOMENTO_VIEW_PARENT'); ?></a>
			<?php } else { ?>
				&mdash;
			<?php } ?>
		<?php } ?>
	</td>
	
	<td class="published-cell center">
		<?php echo $this->html('grid.published', $comment, 'comments', 'published'); ?>
	</td>

	<?php if ($layout === 'reports') { ?>
		<td class="center">
			<?php echo $comment->reports; ?>
		</td>
	<?php } ?>

	<?php if (!in_array($layout, ['reports', 'pending'])) { ?>
	<td class="sticked-cell center">
		<?php echo $this->html('grid.featured', $comment, 'comments', 'sticked', $comment->isFeatured() ? 'unfeature' : 'feature'); ?>
	</td>
	<?php } ?>

	<td class="center">
		<?php if ($comment->extension && $comment->isPublished()) { ?>
			<a href="<?php echo $comment->getPermalink(); ?>" target=_blank><?php echo $comment->contenttitle; ?></a>
		<?php } elseif ($comment->extension && !$comment->isPublished()) { ?>
			<a href="<?php echo $comment->getItemPermalink(); ?>" target=_blank><?php echo $comment->contenttitle; ?></a>
		<?php } else { ?>
			<span class="error"><?php echo $comment->contenttitle; ?></span>
		<?php } ?>

		<div class="mt-xs">
			<a href="javascript:void(0);" data-component="<?php echo $comment->component;?>">
				<?php echo $this->fd->html('label.standard', $comment->componenttitle); ?>
			</a>
		</div>
	</td>

	<td class="center">
		<?php echo $comment->getCreatedDate()->toSql(); ?>
	</td>

	<td class="center">
		<?php if ($comment->created_by) { ?>
		<a href="<?php echo JRoute::_('index.php?option=com_users&task=user.edit&id=' . $comment->created_by);?>" target="_blank">
		<?php } ?>
		
		<?php echo $comment->name; ?>
		
		<?php if ($comment->created_by) { ?>
		</a>
		<?php } ?>

		<div class="mt-xs">
			<?php echo $this->fd->html('label.standard', $comment->ip, 'info', ['rounded' => false]);?>
		</div>
	</td>

	<td class="center">
		<?php echo $comment->id;?>
	</td>
</tr>
