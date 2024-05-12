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
<div class="kt-comment-control">
	<?php echo $this->fd->html('dropdown.standard', function() {
		return $this->fd->html('button.standard', $this->fd->html('icon.font', 'fdi fa fa-ellipsis-h fa-fw'), 'default', 'xs', [
			'ghost' => true,
			'iconOnly' => true
		]);
	}, function() use ($comment) {
		$items = [];

		if ($comment->canEdit()) {
			$items['edit'] = $this->fd->html('dropdown.item', 'COM_KOMENTO_COMMENT_EDIT', null, ['attributes' => 'data-kt-manage-edit']);
		}

		if ($comment->canMinimize()) {
			$items['minimize'] = $this->fd->html('dropdown.item', 'COM_KT_MINIMIZE_COMMENT', null, ['attributes' => 'data-kt-manage-minimize', 'wrapperClass' => 'kt-admin-minimize']);

			$items['expand'] = $this->fd->html('dropdown.item', 'COM_KT_EXPAND_COMMENT', null, ['attributes' => 'data-kt-manage-expand', 'wrapperClass' => 'kt-admin-expand']);
		}

		if ($comment->canFeature()) {
			$items['unfeature'] = $this->fd->html('dropdown.item', 'COM_KOMENTO_UNFEATURE_COMMENT', null, ['attributes' => 'data-kt-manage-unpin', 'wrapperClass' => 'kt-unpin-comment']);
			
			$items['feature'] = $this->fd->html('dropdown.item', 'COM_KOMENTO_FEATURE_COMMENT', null, ['attributes' => 'data-kt-manage-pin', 'wrapperClass' => 'kt-pin-comment']);
		}

		if ($comment->canUnpublish() && $comment->isPublished()) {
			$items['unpublish'] = $this->fd->html('dropdown.item', 'COM_KOMENTO_COMMENT_UNPUBLISH', null, ['attributes' => 'data-kt-manage-unpublish']);
		}

		if ($comment->canPublish() && $comment->isUnpublished()) {
			$items['publish'] = $this->fd->html('dropdown.item', 'COM_KOMENTO_COMMENT_PUBLISH', null, ['attributes' => 'data-kt-manage-unpublish']);
		}

		if ($comment->canSubmitSpam()) {
			$items['spam'] = $this->fd->html('dropdown.item', 'COM_KOMENTO_MARK_SPAM', null, ['attributes' => 'data-kt-submit-spam']);
		}

		if ($comment->canDelete()) {
			$items['delete'] = $this->fd->html('dropdown.item', 'COM_KOMENTO_COMMENT_DELETE', null, ['attributes' => 'data-kt-manage-delete', 'class' => 'text-danger']);
		}

		return $items;
	}, [
		'header' => 'COM_KT_ACTIONS',
		'divider' => true,
		'target' => 'self',
		'class' => 'md:w-[180px]',
		'appearance' => $this->config->get('layout_appearance'),
		'theme' =>  $this->config->get('layout_accent')
	]);
	?>
</div>