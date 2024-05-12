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

class KomentoModelEasySocial extends KomentoModel
{
	protected $element = 'easysocial';

	/**
	 * Send system notication as batch basis to members
	 *
	 * @since	3.0.11
	 * @access	public
	 */
	public function notifyUsers($rules, $recipients, $recipientType, $options, $exclude = array())
	{
		$segments = explode('.', $rules);
		$element = array_shift($segments);
		$rulename = implode('.', $segments);

		$alert = ES::alert($element, $rulename);

		if (!$alert) {
			// alert rule not found.
			return true;
		}

		if ($recipientType != 'user' && $recipientType != 'usergroup') {
			// skip
			return true;
		}

		if (is_object($options)) {
			$options = ES::makeArray($options);
		}

		// If params is not set, just give it an empty array
		if (!isset($options['params'])) {
			$options['params'] = array();
		}

		// Assign any non-table key into params automatically
		$columns = ES::db()->getTableColumns('#__social_notifications');
		foreach ($options as $key => $val) {
			if (!in_array($key, $columns)) {
				$options['params'][$key] = $val;
			}
		}

		if (!isset($options['uid'])) {
			$options['uid'] = 0;
		}

		if (!isset($options['type'])) {
			$options['type'] = $alert->element;
		}

		if (!isset($options['cmd'])) {
			$options['cmd'] = $options['type'] . '.' . $alert->rule;
		}

		if (!isset($options['title'])) {
			$options['title'] = $this->getNotificationTitle('system', $alert);
		}

		if (!isset($options['actor_id'])) {
			$options['actor_id'] = ES::user()->id;
		}

		if (!isset($options['actor_type'])) {
			$options['actor_type'] = SOCIAL_TYPE_USER;
		}

		if (!isset($options['target_type'])) {
			$options['target_type'] = SOCIAL_TYPE_USER;
		}

		if (!isset($options['url'])) {
			$options['url'] = FH::getURI(true);
		}

		$uid = $options['uid'];
		$type = $options['type'];
		$cmd = $options['cmd'];
		$title = $options['title'];
		$actorId = $options['actor_id'];
		$actorType = $options['actor_type'];
		$targetType = $options['target_type'];
		$url = $options['url'];
		$params = ES::makeJSON($options['params']);

		$content = isset($options['content']) ? $options['content'] : '';
		$contextType = isset($options['context_type']) ? $options['context_type'] : '';
		$contextIds = isset($options['context_ids']) ? $options['context_ids'] : '';
		$image = isset($options['image']) ? $options['image'] : '';
		$created = ES::date()->toSql();

		$db = ES::db();
		$sql = $db->sql();

		$ruleId = $alert->id; // rule id

		$query = "insert into `#__social_notifications` (`uid`, `type`, `context_ids`, `context_type`, `cmd`, `title`, `content`,";
		$query .= " `image`, `created`, `state`, `actor_id`, `actor_type`, `params`, `url`, `target_type`, `target_id`)";

		$query .= " select " . $db->Quote($uid) . ", " . $db->Quote($type) . ", " . $db->Quote($contextIds) . ", " . $db->Quote($contextType) . ", ";
		$query .= $db->Quote($cmd) . ", " . $db->Quote($title) . ", " . $db->Quote($content) . ", " . $db->Quote($image) . ", " . $db->Quote($created) . ", ";
		$query .= $db->Quote(0) . ", " . $db->Quote($actorId) . ", " . $db->Quote($actorType) . ", " . $db->Quote($params) . ", " . $db->Quote($url) . ", ";
		$query .= $db->Quote($targetType) . ", x.`id` from (";
		$query .= "     select a.`id`,";
		$query .= "         (select `system` from `#__social_alert_map` where `alert_id` = " . $db->Quote($ruleId);
		$query .= "              and `user_id` = a.`id` union select `system` from `#__social_alert` where `id` = " . $db->Quote($ruleId) . " limit 1) as `notify_system`";

		if ($recipientType == 'user') {

			$query .= "       from `#__users` as a";
			$query .= "     where a.`block` = " . $db->quote(0);

			if ($exclude) {
				if (!is_array($exclude)) {
					$exclude = array($exclude);
				}

				$recipients = array_diff($recipients, $exclude);
			}

			if (!$recipients) {
				// no user ids. stop here.
				return true;
			}

			$query .= " and a.`id` IN (" . implode(',', $recipients) . ")";
		} else {
			// user groups
			if (!$recipients) {
				// nothing to send.
				return true;
			}

			$query .= "       from `#__users` as a";
			$query .= "       inner join `#__user_usergroup_map` as ag on a.`id` = ag.`user_id`";
			$query .= "     where a.`block` = " . $db->quote(0);
			$query .= "		and ag.`group_id` IN (" . implode(',', $recipients) . ")";

			if ($exclude) {
				if (!is_array($exclude)) {
					$exclude = array($exclude);
				}

				$query .= " and a.`id` NOT IN (" . implode(',', $exclude) . ")";
			}

		}

		$query .= " ) as x";
		$query .= " where x.`notify_system` = " . $db->Quote('1');

		// echo $query;exit;

		$sql->raw($query);

		$db->setQuery($sql);
		$db->query();

		return true;
	}


	public function getNotificationTitle($type, $alert)
	{
		ES::language()->loadSite();

		$segments = array();

		$segments[] = $alert->app > 0 ? 'APP' : 'COM_EASYSOCIAL';

		$segments[] = strtoupper($alert->element);
		$segments[] = strtoupper($alert->rule);
		$segments[] = strtoupper($type);
		$segments[] = 'TITLE';

		// We do not want to JText this here
		// Notifications are now generated live and translate live instead of storing the translated string into the database
		// $title = JText::_(implode('_', $segments));
		$title = implode('_', $segments);

		return $title;
	}
}
