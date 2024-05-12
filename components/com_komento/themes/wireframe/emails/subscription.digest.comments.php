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

// do not modify below section:
// preview start
if (isset($templatePreview) && $templatePreview) {
	$articles = ['1' => 
			(object) [
				'component' => 'com_content',
				'cid' => '1',
				'title' => 'Joomla article',
				'link' => 'javascript:void(0);',
				'unlink' => 'javascript:void(0);',
				'posts' => [
					(object) [
						'articleTitle' => 'Article One',
						'articleLink' => 'javascript:void(0);',
						'authorName' => 'Author Name',
						'comment' => 'As a developer, should you use modules or code this bit of feature by yoursef? Let\' find out.'
					],
					(object) [
						'articleTitle' => 'Article One',
						'articleLink' => 'javascript:void(0);',
						'authorName' => 'Author Name',
						'comment' => 'As a developer, should you use modules or code this bit of feature by yoursef? Let\' find out.'
					]
				]
			]
	];

	$icon = rtrim(JURI::root(), '/') . '/media/com_komento/images/icons/comment.png';
}
// preview end
?>

<?php foreach ($articles as $key => $article) { ?>

	<?php if (!$article->posts) { continue; } ?>

	<?php echo $this->fd->html('email.sectionHeading', 'COM_KT_DIGEST_ITEM_HEADER', 'COM_KT_DIGEST_ITEM_HEADER_DESC'); ?>

	<?php foreach ($article->posts as $comment) { ?>

		<?php echo $this->fd->html('email.digestItem', JText::sprintf('COM_KT_DIGEST_COMMENT_ITEM', $comment->authorName, '<a href="' . $comment->articleLink . '" class="font-bold" style="color: #4e72e2; font-weight: bold; text-decoration: none;" target="blank">' . $comment->articleTitle . '</a>'), $comment->comment, ['icon' => $icon, 'divider' => true]); ?>

	<?php } ?>

	<?php echo $this->fd->html('email.unsubscribe', $article->unlink, 'COM_KT_DIGEST_EMAIL_UNSUBSCRIBE', 'COM_KT_DIGEST_EMAIL_UNSUBSCRIBE_TEXT'); ?>

	<?php echo $this->fd->html('email.spacer'); ?>

<?php } ?>