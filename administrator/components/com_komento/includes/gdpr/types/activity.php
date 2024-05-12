<?php
/**
* @package		Komento
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class KomentoGdprActivity extends KomentoGdprAbstract
{
	public $type = 'activity';

	/**
	 * Event trigger to process user's activities for GDPR download on EasySocial
	 *
	 * @since 3.1
	 * @access public
	 */
	public function onEasySocialGdprExport(SocialGdprSection &$section, SocialGdprItem $adapter)
	{
		// manually set type here.
		$adapter->type = $section->key . '_' . $this->type;

		// create tab in section
		$adapter->tab = $section->createTab($adapter);

		$limit = $this->getLimit();

		// Get a list of ids that are already processed
		$ids = $adapter->tab->getProcessedIds();

		$options = array('limit' => $limit);
		if ($ids) {
			$options['exclude'] = $ids;
		}

		$model = KT::model('activity');
		$items = $model->getActivitiesGDPR($this->userId, $options);

		if (!$items) {
			// for comments, we always finalize.
			$adapter->tab->finalize();
			return true;
		}

		if ($items) {
			foreach ($items as $activity) {

				$item = $adapter->getTemplate($activity->id, $adapter->type);

				$item->created = $activity->created;

				$item->view = false;
				$item->title =  $this->getTitle($this->userId, $activity);
				$item->intro = $this->getIntro($activity);

				$adapter->tab->addItem($item);
			}
		}

		return true;
	}


	/**
	 * Main function to process user activities data for GDPR download.
	 *
	 * @since	3.1
	 * @access	public
	 */
	public function execute(KomentoGdprSection &$section)
	{
		$this->tab = $section->createTab($this);

		$limit = $this->getLimit();

		// Get a list of ids that are already processed
		$ids = $this->tab->getProcessedIds();

		$options = array('limit' => $limit);
		if ($ids) {
			$options['exclude'] = $ids;
		}

		$model = KT::model('Actions');
		$items = $model->getActionsGDPR($this->userId, $options);

		if (!$items) {
			return $this->tab->finalize();
		}

		foreach ($items as $activity) {
			$item = $this->getTemplate($activity->id, $this->type);

			$item->created = $activity->actioned;
			$item->title =  $this->getTitle($this->userId, $activity);
			$item->intro = $this->getIntro($activity);

			$this->tab->addItem($item);
		}
	}

	public function getTitle($userid, $activity)
	{
		$actor = KT::user($userid);
		$actor = $actor->name;

		$title = JText::_('COM_KT_GDPR_ACTIVITY_' . strtoupper($activity->type));
		$title = strip_tags($title);

		return $title;
	}

	public function getIntro($activity)
	{
		$date = FH::date($activity->actioned);

		ob_start();
		?>
		<div class="gdpr-item__meta">
			<?php echo JText::sprintf('COM_KT_GDPR_COMMENTED_ON', $date->format($this->getDateFormat())); ?>
		</div>
		<?php
		$contents = ob_get_contents();
		ob_end_clean();

		return $contents;
	}
}
