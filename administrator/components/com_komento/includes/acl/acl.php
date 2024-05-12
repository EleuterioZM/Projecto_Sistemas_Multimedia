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

class KomentoAcl
{
	protected static $rules = [];

	public function allow($type, $comment = '', $component = '', $cid = '')
	{
		// for complicated acl situations
		// $type = ['edit', 'delete', 'publish', 'unpublish', 'stick', 'delete_attachment'];

		if (!empty($comment) && (empty($component) || empty($cid))) {
			
			if (!is_object($comment)) {
				$comment = KT::comment($comment);
			}

			$component = $comment->component;
			$cid = $comment->cid;
		}

		if (empty($component) || empty($cid)) {
			return false;
		}

		$profile = KT::user();
		$application = KT::loadApplication($component)->load($cid);

		KT::setCurrentComponent($component);

		switch ($type) {

			case 'minimize':
				if ($profile->id != 0 && $profile->allow('minimize_comment')) {
					return true;
				}
				
				break;

			case 'edit':
				if ($profile->id != 0 && ($profile->allow('edit_all_comment') || ($profile->id == $application->getAuthorId() && $profile->allow('author_edit_comment') ) || ($profile->id == $comment->created_by && $profile->allow('edit_own_comment')))) {
					return true;
				}
				break;

			case 'delete':
				if( $profile->id != 0 && ($profile->allow('delete_all_comment') || ($profile->id == $application->getAuthorId() && $profile->allow('author_delete_comment')) || ($profile->id == $comment->created_by && $profile->allow('delete_own_comment')))) {
					return true;
				}
				break;

			case 'publish':
				if ($profile->allow('publish_all_comment') || ($profile->id == $application->getAuthorId() && $profile->allow('author_publish_comment'))) {
					return true;
				}
				break;

			case 'unpublish':
				if( $profile->allow( 'unpublish_all_comment' ) || ( $profile->id == $application->getAuthorId() && $profile->allow( 'author_unpublish_comment' ) ) )
				{
					return true;
				}
				break;
			case 'stick':
				if ($profile->allow('stick_all_comment') || ($profile->id == $application->getAuthorId() && $profile->allow('author_stick_comment'))) {
					return true;
				}
				break;

			case 'like':
				if ($profile->allow('like_comment')) {
					return true;
				}
				break;

			case 'report':
				if ($profile->allow('report_comment')) {
					return true;
				}
				break;

			case 'delete_attachment':
				if ($profile->allow('delete_all_attachment') || ($profile->id == $application->getAuthorId() && $profile->allow('author_delete_attachment')) || ($profile->id == $comment->created_by && $profile->allow('delete_own_attachment'))) {
					return true;
				}
				break;
		}

		return false;
	}

	public function check($action, $userId)
	{
		$userId = (int) $userId;
		$result = false;

		$rules = $this->getRules($userId);

		if (isset($rules[$action])) {
			$result = (bool) $rules[$action];
		}

		return $result;
	}

	public function getRules($userId)
	{
		$signature = serialize(array($userId));

		if (empty(self::$rules[$signature])) {
			$profile = KT::user($userId);

			$model = KT::model('Acl');
			$data = [];

			// check user group specific rules
			$gids = $profile->getUsergroups();

			foreach ($gids as $gid) {
				$data[]	= $model->getAclObject($gid, 'usergroup');
			}

			// check user specific rules
			$data[] = $model->getAclObject($userId, 'user');

			// remove empty set
			foreach ($data as $key => $value) {
				if (empty($value)) {
					unset($data[$key]);
				}
			}

			if (count($data) < 1) {
				$data[] = KT::acl()->getEmptySet(true);
			}

			self::$rules[$signature] = $this->merge($data);
		}

		return self::$rules[$signature];
	}

	private function merge($data)
	{
		$result	= [];

		foreach ($data as $ruleset) {

			if (!empty($ruleset)) {
				foreach ($ruleset as $key => $value) {
					if (isset($result[$key])) {
						// This logics prioritizes TRUE
						if (!(bool) $result[$key]) {
							$result[$key] = $value;
						}
					} else {
						$result[$key] = $value;
					}
				}
			}
		}

		return $result;
	}

	/**
	 * Get Default ACL set
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function getEmptySet($flat = false)
	{
		static $acl = null;

		if (empty($acl)) {
			$rulesFile = KOMENTO_ADMIN_ROOT . '/defaults/acl.json';

			if (!JFile::exists($rulesFile)) {
				return false;
			}

			$contents = file_get_contents($rulesFile);
			$acl = json_decode($contents);
		}

		if ($flat === false) {
			return $acl;
		}

		$data = new stdClass();

		foreach ($acl as $section => $rules) {

			foreach($rules as $key => $value) {
				$data->$key = $value;
			}
		}

		return $data;
	}
}
