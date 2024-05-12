<?php
/**
* @package      Komento
* @copyright    Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

KT::import('admin:/includes/model');

class KomentoModelAcl extends KomentoModel
{
	protected $element = 'acl';
	protected $total = null;
	protected $pagination = null;
	protected $data = null;
	protected $systemComponents = [];

	public function __construct($config = [])
	{
		parent::__construct($config);

		$mainframe = JFactory::getApplication();

		$limit = $mainframe->getUserStateFromRequest('com_komento.acls.limit', 'limit', $mainframe->getCfg('list_limit', 20), 'int');
		$limitstart = $this->input->get('limitstart', 0, 'int');

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);

		$this->systemComponents = [
				'com_config', 'com_finder', 'com_media', 'com_redirect', 'com_users', 'com_content', 'com_komento'
			];

		$this->db = KT::db();
	}

	public function getAclObject($cid = 0, $type = 'usergroup')
	{
		$sql = KT::sql();

		$sql->select('#__komento_acl')
			->column('rules')
			->where('cid', $cid)
			->where('type', $type);

		$result = $sql->loadResult();

		if (empty($result)) {
			return false;
		}

		$result = json_decode($result);

		return $result;
	}

	public function updateUserGroups()
	{
		$userGroups	= KT::getUsergroups();
		$userGroupIDs = [];

		foreach ($userGroups as $userGroup) {
			$userGroupIDs[] = $userGroup->id;
		}

		$sql = $this->db->sql();

		$sql->select('#__komento_acl')
			->column('cid')
			->where('type', 'usergroup');

		$this->db->setQuery($sql);

		$current = $this->db->loadColumn();

		KT::import('admin:/includes/acl/acl');

		$defaultset = KT::ACL()->getEmptySet(true);
		$defaultset = json_encode($defaultset);

		foreach ($userGroupIDs as $userGroupID) {
			if (!in_array($userGroupID, $current)) {

				$table = KT::table('acl');
				$table->cid = $userGroupID;
				$table->type = 'usergroup';
				$table->rules = $defaultset;

				$table->store();
			}
		}
	}

	public function getData($type = 'usergroup', $cid = 0)
	{
		static $_cache = [];

		$idx = $type . $cid;

		if (isset($_cache[$idx])) {
			return $_cache[$idx];
		}

		$query = '';
		$query .= 'SELECT `rules` FROM ' . $this->db->nameQuote('#__komento_acl');
		$query .= ' WHERE `type` = ' . $this->db->quote($type);
		$query .= ' AND `cid` = ' . $this->db->quote($cid);
		$query .= ' ORDER BY `type`';

		$this->db->setQuery($query);

		$rulesets = $this->db->loadResult();

		if (empty($rulesets)){
			$rulesets = new stdClass();
		} else {
			$rulesets = json_decode($rulesets);
		}

		$defaultset = KT::ACL()->getEmptySet();

		foreach ($defaultset as $section => &$rules) {

			foreach ($rules as $key => &$value) {

				if (isset($rulesets->$key)) {
					$value = $rulesets->$key;
				}
			}
		}

		$_cache[$idx] = $defaultset;

		return $defaultset;
	}

	/**
	 * Retrieves the title of a user group given the id of the group
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getGroupTitle($gid)
	{
		$db = KT::db();

		$query = [
			'select `title` FROM `#__usergroups`',
			'WHERE `id`=' . $db->Quote($gid)
		];

		$db->setQuery($query);

		$title = $db->loadResult();

		return $title;
	}

	public function save($data)
	{
		$cid = $data['target_id'];
		$type = $data['target_type'];

		KT::import('admin:/includes/acl/acl');

		$defaultset = KT::ACL()->getEmptySet(true);

		foreach ($defaultset as $key => $value) {
			if (isset($data[$key])) {
				$defaultset->$key = $data[$key] ? true : false;
			}
		}

		$table = KT::table('Acl');
		$table->compositeLoad($cid, $type);

		$table->rules = json_encode($defaultset);

		return $table->store();
	}
}