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
defined('_JEXEC') or die('Restricted access');

class modKomentoTopRated extends Komento
{
	public function getAggregatedRatingArticles($options = [])
	{
		$db = KT::db();

		$component = !isset($options['component']) ? 'all' : $options['component'];

		// cid must be an array.
		if (isset($options['cid']) && (is_object($options['cid']) || is_string($options['cid']))) {
			return [];
		}

		$cid = isset($options['cid']) ? implode(', ', $options['cid']) : false;

		$query[] = 'SELECT ax.`component`, ax.`cid`, count(1) AS `count`, sum(ax.`ratings`) AS `totalRating`, ROUND(AVG(ax.`ratings`)/2,2) AS `avgRating`';
		$query[] = 'FROM `#__komento_comments` AS `ax`';
		$query[] = 'WHERE ax.`published` = ' . $db->Quote(1);

		// we only want to display the posts that has ratings given
		$query[] = 'AND ax.`ratings` > 0';

		// We'll only need to fetch the latest votes from the user to prevent double voting. #251
		$query[] = 'AND ax.`created` = ';
		$query[] = '(SELECT MAX(bx.`created`) FROM `#__komento_comments` AS `bx`';
		$query[] = 'WHERE bx.`email` = ax.`email`';
		$query[] = 'AND bx.`component` = ax.`component`';
		$query[] = 'AND bx.`cid` = ax.`cid`';
		$query[] = ')';

		if ($options['component'] != 'all') {
			$query[] = "AND ax.`component` = " . $db->quote($options['component']);
			$query[] = "GROUP BY ax.`cid`";
		} else {
			$query[] = 'GROUP BY ax.`component`, ax.`cid`';
		}

		$query[] = 'ORDER BY `avgRating` DESC';

		if ($options['limit']) {
			$query[] = "LIMIT " . $options['limit'];
		}

		$query = implode(' ', $query);

		// echo str_replace('#_', 'jos', $query);
		// exit;

		$db->setQuery($query);
		$data = $db->loadObjectList();

		$articles = [];
		$model = KT::model('comments');

		foreach ($data as &$item) {
			$application = KT::loadApplication($item->component);
			$article = $application->load($item->cid);

			$item->title = $article->getContentTitle();
			$item->permalink = $article->getContentPermalink();
			$item->componentName = $article->getComponentName();
			$item->totalRating = $item->avgRating;

			$articles[] = $item;
		}

		return $articles;
	}
}