<?php
/*
 * @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @component Phoca Gallery
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('_JEXEC') or die();
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Factory;
use Joomla\CMS\Pagination\Pagination;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Object\CMSObject;
jimport( 'joomla.application.component.view' );
phocagalleryimport( 'phocagallery.rate.ratecategory' );

class PhocaGalleryCpViewPhocaGalleryCs extends HtmlView
{
	protected $items;
	protected $pagination;
	protected $state;
	protected $t;
	protected $r;
	public $filterForm;
	public $activeFilters;
	//protected $_context 	= 'com_phocagallery.phocagalleryc';

	function display($tpl = null) {


		$model 				= $this->getModel();
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');
		$this->filterForm   = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

		$this->r = new PhocaGalleryRenderAdminViews();
		$this->t			= PhocaGalleryUtils::setVars('c');

		// Preprocess the list of items to find ordering divisions.
		foreach ($this->items as &$item) {
			$this->ordering[$item->parent_id][] = $item->id;
		}

		// if search, don't do a tree, only display the searched items
		$this->t['search'] = $this->state->get('filter.search');
		/*
		 * We need to load all items because of creating tree
		 * After creating tree we get info from pagination
		 * and will set displaying of categories for current pagination
		 * E.g. pagination is limitstart 5, limit 5 - so only categories from 5 to 10 will be displayed
		 */

		 // the same for max levels
		$this->t['level'] = $this->state->get('filter.level');

		if (!empty($this->items) && !$this->t['search']) {
			$text = ''; // text is tree name e.g. Category >> Subcategory
			$tree = array();

			// Filter max levels
			if (isset($this->t['level']) && $this->t['level'] > 0) {
				$maxLevel = (int)$this->t['level'] + 1;
			} else {
				$maxLevel = false;
			}

			$this->items = $this->processTree($this->items, $tree, 0, $text, -1, 0, '', $maxLevel);

			// Re count the pagination
			$countTotal 		= count($this->items);
			$model->setTotal($countTotal);
			$this->pagination	= $model->getPagination();

			/*
			// PHOCAEDIT
			// Because of search, we load more items - e.g. if the searched string is in category which is on level 3
			// we have loaded parent categories of the level 3 category: level3 (searched string) - level2 - level1 (root)
			// Now we need to limit if, it is a paradox:
			// a) wee need to get parent categories - but only for creating tree
			// b) but we don't want to display them - so in model we load it moreover but now we need to remove again + we need to limit pagination

			$app = Factory::getApplication('administrator');
			$search = $app->getUserStateFromRequest('com_phocagallery.phocagalleryimgs.filter.search', 'filter_search');

			if ($search != '') {
				foreach ($this->items as $k => $v) {

					$pos = strpos(strtolower($v->title_self), strtolower($search));
					if ($pos !== false) {

					} else {
						unset($this->items[$k]);
					}
				}
			}

			// Correct the pagination
			$c = count($this->items);
			$this->pagination = new Pagination($c, $this->pagination->limitstart, $this->pagination->limit);
			// END PHOCAEDIT */
		}


		//$mainframe	= JFactory::getApplication();
		//$document	= JFactory::getDocument();
		//$uri		= \Joomla\CMS\Uri\Uri::getInstance();



		$this->t['notapproved'] 	= $this->get( 'NotApprovedCategory' );


		$params 	= ComponentHelper::getParams('com_phocagallery');

		$this->t['enablethumbcreation']			= $params->get('enable_thumb_creation', 1 );
		$this->t['enablethumbcreationstatus'] 	= PhocaGalleryRenderAdmin::renderThumbnailCreationStatus((int)$this->t['enablethumbcreation']);


		$this->addToolbar();
		parent::display($tpl);
	}

	protected function addToolbar() {

		require_once JPATH_COMPONENT.'/helpers/phocagallerycs.php';

		$state	= $this->get('State');
		$canDo	= PhocaGalleryCsHelper::getActions($state->get('filter.category_id'));
		$user  = Factory::getUser();
		$bar = Toolbar::getInstance('toolbar');

		ToolbarHelper::title( Text::_( 'COM_PHOCAGALLERY_CATEGORIES' ), 'folder' );
		if ($canDo->get('core.create')) {
			ToolbarHelper::addNew('phocagalleryc.add','JToolbar_NEW');
		}
		if ($canDo->get('core.edit')) {
			ToolbarHelper::editList('phocagalleryc.edit','JToolbar_EDIT');
		}

		$dropdown = $bar->dropdownButton('status-group')->text('JTOOLBAR_CHANGE_STATUS')->toggleSplit(false)->icon('icon-ellipsis-h')->buttonClass('btn btn-action')->listCheck(true);
		$childBar = $dropdown->getChildToolbar();


		if ($canDo->get('core.edit.state')) {

		/*	ToolbarHelper::divider();
			ToolbarHelper::custom('phocagallerycs.publish', 'publish.png', 'publish_f2.png','JToolbar_PUBLISH', true);
			ToolbarHelper::custom('phocagallerycs.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JToolbar_UNPUBLISH', true);
			ToolbarHelper::custom( 'phocagallerycs.approve', 'approve.png', '', 'COM_PHOCAGALLERY_APPROVE' , true);
			ToolbarHelper::custom( 'phocagallerycs.disapprove', 'disapprove.png', '',  'COM_PHOCAGALLERY_NOT_APPROVE' , true);
			//ToolbarHelper::custom('phocagallerycs.cooliris', 'cooliris.png', '',  'COM_PHOCAGALLERY_COOLIRIS' , true);*/

			$childBar->publish('phocagallerycs.publish')->listCheck(true);
			$childBar->unpublish('phocagallerycs.unpublish')->listCheck(true);
			$childBar->standardButton('approve')->text('COM_PHOCAGALLERY_APPROVE')->task('phocagallerycs.approve')->listCheck(true);
			$childBar->standardButton('disapprove')->text('COM_PHOCAGALLERY_NOT_APPROVE')->task('phocagallerycs.disapprove')->listCheck(true);
		}

		if ($canDo->get('core.delete')) {
			//ToolbarHelper::deleteList( Text::_( 'COM_PHOCAGALLERY_WARNING_DELETE_ITEMS' ), 'phocagallerycs.delete', 'COM_PHOCAGALLERY_DELETE');
			$childBar->delete('phocagallerycs.delete')->text('COM_PHOCAGALLERY_DELETE')->message('COM_PHOCAGALLERY_WARNING_DELETE_ITEMS')->icon('icon-trash')->listCheck(true);
		}
		// Add a batch button
		if ($user->authorise('core.edit'))
		{
			//HTMLHelper::_('bootstrap.renderModal', 'collapseModal');
			/*$title = Text::_('JToolbar_BATCH');


			$dhtml = '<joomla-toolbar-button id="toolbar-batch" list-selection>';
			$dhtml .= "<button data-bs-toggle=\"modal\" data-bs-target=\"#collapseModal\" class=\"btn btn-primary btn-small\">
						<i class=\"icon-checkbox-partial\" title=\"$title\"></i>
						$title</button>";
			$dhtml .= '</joomla-toolbar-button>';

			$bar->appendButton('Custom', $dhtml, 'batch');*/

			HTMLHelper::_('bootstrap.renderModal', 'collapseModal');
			$childBar->popupButton('batch')->text('JTOOLBAR_BATCH')->selector('collapseModal')->listCheck(true);

		}
		ToolbarHelper::divider();
		ToolbarHelper::help( 'screen.phocagallery', true );
	}


	protected function processTree( $data, $tree, $id = 0, $text='', $currentId = 0, $level = 0, $parentsTreeString = '', $maxLevel = false) {


		$countItemsInCat 	= 0;// Ordering
		$level 				= $level + 1;
		$parentsTreeString	= $id . ' '. $parentsTreeString;




		// Limit the level of tree
		if (!$maxLevel || ($maxLevel && $level < $maxLevel)) {
			foreach ($data as $key) {
				$show_text 	= $text . $key->title;

				static $iCT = 0;// All displayed items

				if ($key->parent_id == $id && $currentId != $id && $currentId != $key->id ) {


					$tree[$iCT] 					= new CMSObject();



					// Ordering MUST be solved here
					if ($countItemsInCat > 0) {
						$tree[$iCT]->orderup				= 1;
					} else {
						$tree[$iCT]->orderup 				= 0;
					}

					if ($countItemsInCat < ($key->countid - 1)) {
						$tree[$iCT]->orderdown 				= 1;
					} else {
						$tree[$iCT]->orderdown 				= 0;
					}

					$tree[$iCT]->level				= $level;
					$tree[$iCT]->parentstree		= $parentsTreeString;

					$tree[$iCT]->id 				= $key->id;
					$tree[$iCT]->title 				= $show_text;
					$tree[$iCT]->title_self 		= $key->title;
					$tree[$iCT]->parent_id			= $key->parent_id;
					$tree[$iCT]->owner_id			= $key->owner_id;
					$tree[$iCT]->name				= $key->name;
					$tree[$iCT]->alias				= $key->alias;
					$tree[$iCT]->image				= $key->image;
					$tree[$iCT]->section			= $key->section;
					$tree[$iCT]->image_position		= $key->image_position;
					$tree[$iCT]->description		= $key->description;
					$tree[$iCT]->published			= $key->published;
					$tree[$iCT]->editor				= $key->editor;
					$tree[$iCT]->ordering			= $key->ordering;
					$tree[$iCT]->access				= $key->access;
					$tree[$iCT]->access_level		= $key->access_level;
					$tree[$iCT]->count				= $key->count;
					$tree[$iCT]->params				= $key->params;
					$tree[$iCT]->checked_out		= $key->checked_out;
					$tree[$iCT]->checked_out_time	= $key->checked_out_time;
					$tree[$iCT]->groupname			= 0;
					$tree[$iCT]->username			= $key->username;
					$tree[$iCT]->usernameno			= $key->usernameno;
					$tree[$iCT]->parentcat_title	= $key->parentcat_title;
					$tree[$iCT]->parentcat_id		= $key->parentcat_id;
					$tree[$iCT]->hits				= $key->hits;
					$tree[$iCT]->ratingavg			= $key->ratingavg;
					$tree[$iCT]->accessuserid		= $key->accessuserid;
					$tree[$iCT]->uploaduserid		= $key->uploaduserid;
					$tree[$iCT]->deleteuserid		= $key->deleteuserid;
					$tree[$iCT]->userfolder			= $key->userfolder;
					$tree[$iCT]->latitude			= $key->latitude;
					$tree[$iCT]->longitude			= $key->longitude;
					$tree[$iCT]->zoom				= $key->zoom;
					$tree[$iCT]->geotitle			= $key->geotitle;
					$tree[$iCT]->approved			= $key->approved;
					$tree[$iCT]->language			= $key->language;
					$tree[$iCT]->language_title		= $key->language_title;
					$tree[$iCT]->link				= '';
					$tree[$iCT]->filename			= '';// Will be added in View (after items will be reduced)
					$tree[$iCT]->linkthumbnailpath	= '';


					$iCT++;


					$tree = $this->processTree($data, $tree, $key->id, $show_text . " - ", $currentId, $level, $parentsTreeString, $maxLevel);

					$countItemsInCat++;
				}

			}
		}

		return($tree);
	}

	protected function getSortFields() {
		return array(
			'a.ordering'	=> Text::_('JGRID_HEADING_ORDERING'),
			'a.title' 		=> Text::_('COM_PHOCAGALLERY_TITLE'),
			'a.published' 	=> Text::_('COM_PHOCAGALLERY_PUBLISHED'),
			'a.approved' 	=> Text::_('COM_PHOCAGALLERY_APPROVED'),
			'parent_title' 	=> Text::_('COM_PHOCAGALLERY_PARENT_CATEGORY'),
			'a.owner' 		=> Text::_('COM_PHOCAGALLERY_OWNER'),
			'ratingavg' 	=> Text::_('COM_PHOCAGALLERY_RATING'),
			'a.hits' 		=> Text::_('COM_PHOCAGALLERY_HITS'),
			'language' 		=> Text::_('JGRID_HEADING_LANGUAGE'),
			'a.id' 			=> Text::_('JGRID_HEADING_ID')
		);
	}
}
?>
