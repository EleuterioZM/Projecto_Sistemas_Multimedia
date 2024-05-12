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

class KomentoGdprComment extends KomentoGdprAbstract
{
	public $type = 'comment';

	/**
	 * Event trigger to process user's comments for GDPR download on EasySocial
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

		$model = KT::model('comments');
		$items = $model->getCommentsGDPR($this->userId, $options);

		if (!$items) {
			// for comments, we always finalize.
			$adapter->tab->finalize();
			return true;
		}

		if ($items) {
			foreach ($items as $comment) {

				$item = $adapter->getTemplate($comment->id, $adapter->type);

				$item->created = $comment->created;

				$item->view = false;
				$item->title =  $this->getTitle($this->userId, $comment);
				$item->intro = $this->getIntro($comment);

				$adapter->tab->addItem($item);
			}
		}

		return true;
	}


	/**
	 * Main function to process user comment data for GDPR download.
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

		$model = KT::model('comments');
		$items = $model->getCommentsGDPR($this->userId, $options);

		if (!$items) {
			return $this->tab->finalize();
		}

		foreach ($items as $comment) {
			$item = $this->getTemplate($comment->id, $this->type);

			$item->created = $comment->created;
			$item->title =  $this->getTitle($comment);
			$item->intro = $this->getIntro($comment);

			$this->tab->addItem($item);
		}
	}

	public function getTitle($comment)
	{
		$actor = KT::user($this->userId);
		$actor = $actor->name;

		// Prepare data and checking on plugin level
		$application = KT::loadApplication($comment->component);

		// Do not proceed if there are errors when loading the application.
		if ($application instanceof KomentoError || !$comment->component) {
			return false;
		}

		// Loading article infomation with defined get methods
		if (!$application->load($comment->cid)) {
			return false;
		}

		$item = $application->_item;
		$title = $item->{$application->_map['title']};

		$title = JText::sprintf('COM_KT_GDPR_COMMENTED_ON_POST', $title);
		$title = strip_tags($title);

		return $title;
	}

	public function getIntro($comment)
	{
		$date = FH::date($comment->created);

		ob_start();
		?>
		<div class="gdpr-item__desc">
			<?php echo $comment->preview; ?>
		</div>
		<div class="gdpr-item__meta">
			<?php echo JText::sprintf('COM_KT_GDPR_COMMENTED_ON', $date->format($this->getDateFormat())); ?>
		</div>
		<?php
		$contents = ob_get_contents();
		ob_end_clean();

		return $contents;
	}
}
