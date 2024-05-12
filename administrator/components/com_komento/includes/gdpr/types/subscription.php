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

class KomentoGdprSubscription extends KomentoGdprAbstract
{
	public $type = 'subscription';

	/**
	 * Event trigger to process user's subscriptions for GDPR download on EasySocial
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

		$model = KT::model('subscription');
		$items = $model->getSubscribeGDPR($this->userId, $options);

		if (!$items) {
			// for comments, we always finalize.
			$adapter->tab->finalize();
			return true;
		}

		if ($items) {
			foreach ($items as $subscription) {

				$item = $adapter->getTemplate($subscription->id, $adapter->type);

				$item->created = $subscription->created;
				$item->title =  $this->getTitle($subscription);
				$item->intro = $this->getIntro($subscription);
				$item->content = false;
				$item->view = false;

				$adapter->tab->addItem($item);
			}
		}

		return true;
	}

	/**
	 * Main function to process user subscription data for GDPR download.
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

		$model = KT::model('subscription');
		$items = $model->getSubscribeGDPR($this->userId, $options);

		if (!$items) {
			return $this->tab->finalize();
		}

		foreach ($items as $subscription) {
			$item = $this->getTemplate($subscription->id, $this->type);

			$item->created = $subscription->created;
			$item->title =  $this->getTitle($subscription);
			$item->intro = $this->getIntro($subscription);
			$item->content = false;
			$item->view = false;

			$this->tab->addItem($item);
		}
	}

	public function getTitle($subscription)
	{
		$actor = KT::user($this->userId);
		$actor = $actor->name;

		// Prepare data and checking on plugin level
		$application = KT::loadApplication($subscription->component);

		// Do not proceed if there are errors when loading the application.
		if ($application instanceof KomentoError || !$subscription->component) {
			return false;
		}

		// Loading article infomation with defined get methods
		if (!$application->load($subscription->cid)) {
			return false;
		}

		$item = $application->_item;
		$title = $item->{$application->_map['title']};

		return $title;
	}

	public function getIntro($subscription)
	{
		$date = FH::date($subscription->created);

		ob_start();
		?>
		<div class="gdpr-item__meta">
			<?php echo $date->format($this->getDateFormat()); ?>
		</div>
		<?php
		$contents = ob_get_contents();
		ob_end_clean();

		return $contents;
	}
}
