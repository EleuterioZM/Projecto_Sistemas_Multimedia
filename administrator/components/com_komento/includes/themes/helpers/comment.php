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

class KomentoThemesComment
{
	/**
	 * Renders the admin actions
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function admin(KomentoComment $comment)
	{
		if (!$comment->canManage()) {
			return;
		}
		
		$theme = KT::themes();
		$theme->set('comment', $comment);
		$output = $theme->output('site/helpers/comment/admin');

		return $output;
	}


	/**
	 * Renders the sorting options for comment listings
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function sorting($active)
	{
		$options = [
			'oldest' => (object) [
				'key' => 'oldest',
				'attributes' => 'data-kt-sort="oldest"',
				'label' => 'COM_KOMENTO_SORT_OLDEST_FIRST'
			],

			'latest' => (object) [
				'key' => 'latest',
				'attributes' => 'data-kt-sort="latest"',
				'label' => 'COM_KOMENTO_SORT_NEWEST_FIRST'
			]
		];

		$config = KT::config();
		$my = KT::user();

		if ($config->get('enable_likes') && $my->allow('like_comment')) {
			$options['likes'] = (object) [
				'key' => 'likes',
				'attributes' => 'data-kt-sort="popular"',
				'label' => 'COM_KT_SORT_MOST_LIKES'
			];
		}

		$activeItem = $options[$active];

		$theme = KT::themes();
		$theme->set('activeItem', $activeItem);
		$theme->set('options', $options);
		$theme->set('active', $active);
		$output = $theme->output('site/helpers/comment/sorting');

		return $output;
	}

	/**
	 * Renders the subscribe button on the comment listings
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function subscribe($subscriptionId)
	{
		$namespace = $subscriptionId ? 'unsubscribe' : 'subscribe';

		$theme = KT::themes();
		$theme->set('subscriptionId', $subscriptionId);
		$output = $theme->output('site/helpers/comment/' . $namespace);

		return $output;
	}
}
