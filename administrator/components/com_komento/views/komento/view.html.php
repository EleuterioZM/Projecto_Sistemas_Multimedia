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

class KomentoViewKomento extends KomentoAdminView
{
	public function display($tpl = null)
	{
		$model = KT::model('Comments');
		
		if ($this->my->authorise('core.admin', 'com_komento')) {
			JToolBarHelper::preferences('com_komento');
		}

		$comments = '';
		$comments = $model->getComments('all', 'all', [
			'threaded' => 0,
			'sort' => 'latest',
			'limit' => 10
		]);

		if ($comments) {
			$comments = KT::formatter('comment', $comments);
		}

		$pendingComments = $model->getItems([
			'published' => 2,
			'no_tree' => 1,
			'no_child' => 1
		]);

		if ($pendingComments) {
			$pendingComments = KT::formatter('comment', $pendingComments);
		}

		$comments = $this->decorateComments($comments);
		$pendingComments = $this->decorateComments($pendingComments);

		$model = KT::model('Comments');
		$totalComments = $model->getTotalComment();
		$totalPending = $model->getCount('all', 'all', ['published' => KT_COMMENT_MODERATE]);
		$totalSpams = $model->getCount('all', 'all', ['published' => KT_COMMENT_SPAM]);

		$reportsModel = KT::model('Reports');
		$totalReports = $reportsModel->getTotal();

		$subscriptionModel = KT::model('Subscription');
		$totalSubscribers = $subscriptionModel->getTotalSubscribers();

		$mailqModel = KT::model('Mailq');
		$totalPendingMails = $mailqModel->getTotalPending();

		$currentVersion = KT::getLocalVersion();
		$updateTaskUrl = KT::isFreeVersion() ? 'javascript:void(0);' : JRoute::_('index.php?option=com_komento&controller=system&task=upgrade');

		$this->set('totalPendingMails', $totalPendingMails);
		$this->set('totalSpams', $totalSpams);
		$this->set('currentVersion', $currentVersion);
		$this->set('totalPending', $totalPending);
		$this->set('totalReports', $totalReports);
		$this->set('totalSubscribers', $totalSubscribers);
		$this->set('comments', $comments);
		$this->set('pendingComments', $pendingComments);
		$this->set('totalComments', $totalComments);
		$this->set('updateTaskUrl', $updateTaskUrl);

		parent::display('komento/default');
	}

	/**
	 * To utilize foundry's adminwidgets.comments, we need to decorate the comment object
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	private function decorateComments($comments)
	{
		if (!$comments) {
			return $comments;
		}

		foreach ($comments as &$comment) {
			$comment->authorName = $comment->getAuthor()->getName();
			$comment->authorLink = 'javascript:void(0);';
			$comment->itemTitle = $comment->contenttitle;
			$comment->permalink = $comment->getPermalink();
		}

		return $comments;
	}

	public function sampledata($tpl = null)
	{
		ini_set('max_execution_time', 120);

		$post = $this->input->getArray('post');

		$counter = 0;

		if ($post && isset($post['frmMax']) && $post['frmMax']) {

			$db = KT::db();

			// lets go the sample data generation here.
			$max = (int) $post['frmMax'];
			$max = $max ? $max : 20;

			// get all users ids
			$query = "select `id`, `name`, `email` from #__users where block = 0";
			$db->setQuery($query);
			$users = $db->loadObjectList('id');


			// get all articles id
			$query = "select id from #__content where state = 1 order by id asc";
			$db->setQuery($query);
			$articleIds = $db->loadColumn();


			$sampleComments = [
				'Hello there...',
				'This is so cool~',
				'This is a test. please ignore lol',
				'This is awesome!',
				'Hi~',
				'You are most welcome!',
				'Hey how is this one?',
				'Omg! it work!',
				'Testing one two three..',
				'Thank you :)'
			];

			$data = [];

			$data['address'] = '';
			$data['latitude'] = '';
			$data['longitude'] = '';
			$data['component'] = 'com_content';


			foreach ($articleIds as $aid) {

				for ($i = 0; $i < $max; $i++) {

					// random comment sample index
					$cIdx = array_rand($sampleComments, 1);

					// random author index
					$aIdx = array_rand($users, 1);

					$poster = $users[$aIdx];

					$data['parent_id'] = 0;
					$data['comment'] = ++$counter . ":" . $sampleComments[$cIdx];
					$data['cid'] = $aid;
					$data['created_by'] = $poster->id;
					$data['name'] = $poster->name;
					$data['email'] = $poster->email;

					$comment = KT::comment();
					$comment->bind($data);

					// Save the comment
					$state = $comment->save([
						'processAttachments' => false
					]);

				}

			}

		}

		$this->set('totalComments', $counter);

		parent::display('komento/sampledata');
	}

}
