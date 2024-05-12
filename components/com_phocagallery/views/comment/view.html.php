<?php
/*
 * @package Joomla
 * @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 *
 * @component Phoca Component
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

use Joomla\CMS\Factory;

defined('_JEXEC') or die();
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\HTML\HTMLHelper;
jimport( 'joomla.application.component.view');
phocagalleryimport('phocagallery.comment.comment');
phocagalleryimport('phocagallery.comment.commentimage');
phocagalleryimport( 'phocagallery.picasa.picasa');
phocagalleryimport( 'phocagallery.facebook.fbsystem');

class PhocaGalleryViewComment extends HtmlView
{

	public $t;
	protected $params;

	function display($tpl = null) {
		$app	= Factory::getApplication();

		$document		= Factory::getDocument();
		$this->params	= $app->getParams();
		$user 			= Factory::getUser();
		$uri 			= \Joomla\CMS\Uri\Uri::getInstance();
		$this->itemId	= $app->input->get('Itemid', 0, 'int');
		$this->t['icon_path']	= 'media/com_phocagallery/images/';



		$neededAccessLevels	= PhocaGalleryAccess::getNeededAccessLevels();
		$access				= PhocaGalleryAccess::isAccess($user->getAuthorisedViewLevels(), $neededAccessLevels);


		// PLUGIN WINDOW - we get information from plugin
		$get = array();
		$get['comment']			= $app->input->get( 'comment', '', 'string' );
		$this->t['id']		= $app->input->get('id', 0, 'int');
		$this->t['catid'] 	= $app->input->get('catid', '', 'string');

		$this->t['maxcommentchar']			= $this->params->get( 'max_comment_char', 1000 );
		$this->t['displaycommentimg']		= $this->params->get( 'display_comment_img', 0 );
		$this->t['detailwindowbackgroundcolor']= $this->params->get( 'detail_window_background_color', '#ffffff' );
		$this->t['commentwidth']				= $this->params->get( 'comment_width', 500 );
		$this->t['enable_multibox']			= $this->params->get( 'enable_multibox', 0);
		$this->t['multibox_comments_width']		= $this->params->get( 'multibox_comments_width', 300 );
		$this->t['externalcommentsystem'] 	= $this->params->get( 'external_comment_system', 0 );
		$this->t['gallerymetakey'] 			= $this->params->get( 'gallery_metakey', '' );
		$this->t['gallerymetadesc'] 			= $this->params->get( 'gallery_metadesc', '' );
		$this->t['altvalue']		 			= $this->params->get( 'alt_value', 1 );
		$this->t['largewidth'] 				= $this->params->get( 'large_image_width', 640 );
		$this->t['largeheight'] 				= $this->params->get( 'large_image_height', 480 );
		$this->t['picasa_correct_width_l']	= (int)$this->params->get( 'large_image_width', 640 );
		$this->t['picasa_correct_height_l']	= (int)$this->params->get( 'large_image_height', 480 );

		$paramsFb = [];//PhocaGalleryFbSystem::getCommentsParams($this->params->get( 'fb_comment_user_id', ''));// Facebook
		$this->t['fb_comment_app_id']		= isset($paramsFb['fb_comment_app_id']) ? $paramsFb['fb_comment_app_id'] : '';
		$this->t['fb_comment_width']			= isset($paramsFb['fb_comment_width']) ? $paramsFb['fb_comment_width'] : 550;
		$this->t['fb_comment_lang'] 			= isset($paramsFb['fb_comment_lang']) ? $paramsFb['fb_comment_lang'] : 'en_US';
		$this->t['fb_comment_count'] 		= isset($paramsFb['fb_comment_count']) ? $paramsFb['fb_comment_count'] : '';
		$this->t['display_comment_nopup']	= $this->params->get( 'display_comment_nopup', 0);
		$this->t['enablecustomcss']			= $this->params->get( 'enable_custom_css', 0);
		$this->t['customcss']				= $this->params->get( 'custom_css', '');

		// Multibox
		if ($this->t['enable_multibox'] == 1) {
			$this->t['commentwidth'] = (int)$this->t['multibox_comments_width'] - 70;//padding - margin
		}
		$get['commentsi']						= $app->input->get( 'commentsi', '', 'int' );
		$this->t['enable_multibox_iframe'] 	= 0;
		if ($get['commentsi'] == 1) {
			// Seems we are in iframe
			$this->t['enable_multibox_iframe'] = 1;
		}

		// CSS
		PhocaGalleryRenderFront::renderAllCSS();

		if ($this->t['gallerymetakey'] != '') {
			$document->setMetaData('keywords', $this->t['gallerymetakey']);
		}
		if ($this->t['gallerymetadesc'] != '') {
			$document->setMetaData('description', $this->t['gallerymetadesc']);
		}



		// PARAMS - Open window parameters - modal popup box or standard popup window
		$detail_window = $this->params->get( 'detail_window', 0 );


		// Plugin information
		if (isset($get['comment']) && $get['comment'] != '') {
			$detail_window = $get['comment'];
		}


		// Only registered (VOTES + COMMENTS)
		$this->t['not_registered'] 	= true;
		$this->t['name']		= '';
		if ($access) {
			$this->t['not_registered'] 	= false;
			$this->t['name']		= $user->name;
		}

		//$document->addScript(JUri::base(true).'/media/com_phocagallery/js/comments.js');
		//$document->addCustomTag(PhocaGalleryRenderFront::renderCommentJS((int)$this->t['maxcommentchar']));

		$this->t['already_commented'] = PhocaGalleryCommentImage::checkUserComment( (int)$this->t['id'], (int)$user->id );
		$this->t['commentitem']					= PhocaGalleryCommentImage::displayComment( (int)$this->t['id'] );




		// PARAMS - Display Description in Detail window - set the font color
		$this->t['detailwindowbackgroundcolor']	= $this->params->get( 'detail_window_background_color', '#ffffff' );
		$this->t['detailwindow']			 		= $this->params->get( 'detail_window', 0 );
		$description_lightbox_font_color 			= $this->params->get( 'description_lightbox_font_color', '#ffffff' );

		$description_lightbox_bg_color 				= $this->params->get( 'description_lightbox_bg_color', '#000000' );
		$description_lightbox_font_size 			= $this->params->get( 'description_lightbox_font_size', 12 );

		// NO SCROLLBAR IN DETAIL WINDOW
		$document->addCustomTag( "<style type=\"text/css\"> \n"
			." html,body, .contentpane{background:".$this->t['detailwindowbackgroundcolor'].";text-align:left;} \n"
			." center, table {background:".$this->t['detailwindowbackgroundcolor'].";} \n"
			." #sbox-window {background-color:#fff;padding:5px} \n"
			." </style> \n");

		$model	= $this->getModel();
		$this->item	= $model->getData();

		$this->t['imgtitle']	=	$this->item->title;

		// Back button
		$this->t['backbutton'] = '';
		if ($this->t['detailwindow'] == 7 || $this->t['display_comment_nopup']) {

			// Display Image
			// Access check - don't display the image if you have no access to this image (if user add own url)
			// USER RIGHT - ACCESS - - - - - - - - - -
			$rightDisplay	= 0;
			if (!empty($this->item)) {
				$rightDisplay = PhocaGalleryAccess::getUserRight('accessuserid', $this->item->cataccessuserid, $this->item->cataccess, $user->getAuthorisedViewLevels(), $user->get('id', 0), 0);
			}

			if ($rightDisplay == 0) {
				$this->t['pl']		= 'index.php?option=com_users&view=login&return='.base64_encode($uri->toString());
				$app->enqueueMessage(Text::_('COM_PHOCAGALLERY_NOT_AUTHORISED_ACTION'), 'error');
				$app->redirect(Route::_($this->t['pl'], false));
				exit;
			}
			// - - - - - - - - - - - - - - - - - - - -

			phocagalleryimport('phocagallery.image.image');
			$this->t['backbutton'] = '<div><a href="'.Route::_('index.php?option=com_phocagallery&view=category&id='. $this->t['catid'].'&Itemid='. $this->itemId).'"'
				.' title="'.Text::_( 'COM_PHOCAGALLERY_BACK_TO_CATEGORY' ).'">'
				. PhocaGalleryRenderFront::renderIcon('icon-up-images', 'media/com_phocagallery/images/icon-up-images.png', Text::_('COM_PHOCAGALLERY_BACK_TO_CATEGORY'), 'ph-icon-up-images ph-icon-button').'</a></div>';

			// Get file thumbnail or No Image
			$this->item->filenameno		= $this->item->filename;
			$this->item->filename			= PhocaGalleryFile::getTitleFromFile($this->item->filename, 1);
			$this->item->filesize			= PhocaGalleryFile::getFileSize($this->item->filenameno);
			$altValue				= PhocaGalleryRenderFront::getAltValue($this->t['altvalue'], $this->item->title, $this->item->description, $this->item->metadesc);
			$this->item->altvalue			= $altValue;
			$realImageSize			= '';
			$extImage = PhocaGalleryImage::isExtImage($this->item->extid);
			if ($extImage) {
				$this->item->extl			=	$this->item->extl;
				$this->item->exto			=	$this->item->exto;
				$realImageSize 		= PhocaGalleryImage::getRealImageSize($this->item->extl, '', 1);
				$this->item->imagesize 	= PhocaGalleryImage::getImageSize($this->item->exto, 1, 1);
				if ($this->item->extw != '') {
					$extw 		= explode(',',$this->item->extw);
					$this->item->extw	= $extw[0];
				}
				$correctImageRes 		= PhocaGalleryPicasa::correctSizeWithRate($this->item->extw, $this->item->exth, $this->t['picasa_correct_width_l'], $this->t['picasa_correct_height_l']);
				$this->item->linkimage		= HTMLHelper::_( 'image', $this->item->extl, $this->item->altvalue, array('width' => $correctImageRes['width'], 'height' => $correctImageRes['height']));
				$this->item->realimagewidth 	= $correctImageRes['width'];
				$this->item->realimageheight	= $correctImageRes['height'];

			} else {
				$this->item->linkthumbnailpath	= PhocaGalleryImageFront::displayCategoryImageOrNoImage($this->item->filenameno, 'large');
				$this->item->linkimage			= HTMLHelper::_( 'image', $this->item->linkthumbnailpath, $this->item->altvalue);
				$realImageSize 				= PhocaGalleryImage::getRealImageSize ($this->item->filenameno);
				$this->item->imagesize			= PhocaGalleryImage::getImageSize($this->item->filenameno, 1);
				if (isset($realImageSize['w']) && isset($realImageSize['h'])) {
					$this->item->realimagewidth		= $realImageSize['w'];
					$this->item->realimageheight		= $realImageSize['h'];
				} else {
					$this->item->realimagewidth	 	= $this->t['largewidth'];
					$this->item->realimageheight		= $this->t['largeheight'];
				}
			}

		}

		// ACTION
		$this->t['action']	= $uri->toString();
		$this->_prepareDocument();
		parent::display($tpl);
	}

	protected function _prepareDocument() {

		$app		= Factory::getApplication();
		$menus		= $app->getMenu();
		$pathway 	= $app->getPathway();
		//$this->params		= $app->getParams();
		$title 		= null;

		$this->t['gallerymetakey'] 		= $this->params->get( 'gallery_metakey', '' );
		$this->t['gallerymetadesc'] 		= $this->params->get( 'gallery_metadesc', '' );


		$menu = $menus->getActive();
		if ($menu) {
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		} else {
			$this->params->def('page_heading', Text::_('JGLOBAL_ARTICLES'));
		}

		$title = $this->params->get('page_title', '');

		if (empty($title)) {
			$title = htmlspecialchars_decode($app->get('sitename'));
		} else if ($app->get('sitename_pagetitles', 0) == 1) {
			$title = Text::sprintf('JPAGETITLE', htmlspecialchars_decode($app->get('sitename')), $title);

			if (isset($this->item->title) && $this->item->title != '') {
				$title = $title .' - ' .  $this->item->title;
			}

		} else if ($app->get('sitename_pagetitles', 0) == 2) {

			if (isset($this->item->title) && $this->item->title != '') {
				$title = $title .' - ' .  $this->item->title;
			}

			$title = Text::sprintf('JPAGETITLE', $title, htmlspecialchars_decode($app->get('sitename')));
		}

		$this->document->setTitle($title);

		if ($this->item->metadesc != '') {
			$this->document->setDescription($this->item->metadesc);
		} else if ($this->t['gallerymetadesc'] != '') {
			$this->document->setDescription($this->t['gallerymetadesc']);
		} else if ($this->params->get('menu-meta_description', '')) {
			$this->document->setDescription($this->params->get('menu-meta_description', ''));
		}

		if ($this->item->metakey != '') {
			$this->document->setMetadata('keywords', $this->item->metakey);
		} else if ($this->t['gallerymetakey'] != '') {
			$this->document->setMetadata('keywords', $this->t['gallerymetakey']);
		} else if ($this->params->get('menu-meta_keywords', '')) {
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords', ''));
		}

		if ($app->get('MetaTitle') == '1' && $this->params->get('menupage_title', '')) {
			$this->document->setMetaData('title', $this->params->get('page_title', ''));
		}

		/*if ($app->get('MetaAuthor') == '1') {
			$this->document->setMetaData('author', $this->item->author);
		}

		/*$mdata = $this->item->metadata->toArray();
		foreach ($mdata as $k => $v) {
			if ($v) {
				$this->document->setMetadata($k, $v);
			}
		}*/

		// Breadcrumbs TO DO (Add the whole tree)
		/*if (isset($this->category[0]->parentid)) {
			if ($this->category[0]->parentid == 1) {
			} else if ($this->category[0]->parentid > 0) {
				$pathway->addItem($this->category[0]->parenttitle, Route::_(PhocaDocumentationHelperRoute::getCategoryRoute($this->category[0]->parentid, $this->category[0]->parentalias)));
			}
		}

		if (!empty($this->category[0]->title)) {
			$pathway->addItem($this->category[0]->title);
		}*/
	}
}
