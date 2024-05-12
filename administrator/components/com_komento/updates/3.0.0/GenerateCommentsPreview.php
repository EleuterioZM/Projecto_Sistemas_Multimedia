<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

KT::import('admin:/includes/maintenance/dependencies');

class KomentoMaintenanceScriptGenerateCommentsPreview extends KomentoMaintenanceScript
{
    public static $title = 'Generate preview for the comments';
    public static $description = 'Generate bbcode\'s preview for the comments.';

    public function main()
    {
		$db = KT::db();
		$limit = 100;

		// step 1: retrieve the latest 100's latest comments.

		$query = "select " . $db->nameQuote('id') . ", " . $db->nameQuote('comment');
		$query .= " FROM " . $db->nameQuote('#__komento_comments');
		$query .= " WHERE " . $db->nameQuote('published') . " = " . $db->Quote('1');
		$query .= " AND " . $db->nameQuote('preview') . " = ''";
		$query .= " ORDER BY " . $db->nameQuote('id') . " DESC";
		$query .= " LIMIT $limit";

		$db->setQuery($query);
		$items = $db->loadObjectList();

		$state = true;

		if ($items) {
			// step 2: now, lets generate the preview for each items and update the preview column.

			$ids = array();
			$cond = array();

			foreach ($items as $item) {

				$id = $item->id;
				$formattedContent = KT::parser()->parseComment($item->comment);

				if ($formattedContent) {

					$ids[] = $id;
					$cond[] = "WHEN id = " . $db->Quote($id) . " THEN " . $db->Quote($formattedContent);
				}
			}

			if ($ids) {
				// now lets join the sql to form voltron force!
				$query = "update " . $db->nameQuote('#__komento_comments');
				$query .= " set " . $db->nameQuote('preview') . " = (CASE ";
				$query .= implode(' ', $cond);
				$query .= " END)";
				$query .= " WHERE " . $db->nameQuote('id') . " IN (" . implode(',', $ids) . ")";

				// echo $query;exit;

				$db->setQuery($query);
				$state = $db->query();
			}

		}

		return $state;
    }
}
