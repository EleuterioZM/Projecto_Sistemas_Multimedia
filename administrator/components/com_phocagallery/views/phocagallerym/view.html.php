<?php
/*
 * @package		Joomla.Framework
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 *
 * @component Phoca Component
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License version 2 or later;
 */
defined('_JEXEC') or die();
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Client\ClientHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Toolbar\Toolbar;
jimport( 'joomla.client.helper' );
jimport( 'joomla.application.component.view' );
jimport( 'joomla.html.pane' );
phocagalleryimport( 'phocagallery.file.fileuploadmultiple' );
phocagalleryimport( 'phocagallery.file.fileuploadsingle' );
phocagalleryimport( 'phocagallery.file.fileuploadjava' );

class PhocaGalleryCpViewPhocaGalleryM extends HtmlView
{
	protected $form;
	protected $folderstate;
	protected $images;
	protected $folders;
	protected $t;
	protected $r;
	protected $session;
	protected $currentFolder;
	protected $path;

	public function display($tpl = null) {

		$this->form			= $this->get('Form');
		$this->folderstate	= $this->get('FolderState');
		$this->images		= $this->get('Images');
		$this->folders		= $this->get('Folders');
		$this->session		= Factory::getSession();
		$this->path			= PhocaGalleryPath::getPath();


		$this->t = PhocaGalleryUtils::setVars('m');
		$this->r	= new PhocaGalleryRenderAdminview();

		// Set Default Value
		$this->form->setValue('published', '', 1);
		$this->form->setValue('approved', '', 1);
		$this->form->setValue('language', '', '*');




		$params 									= ComponentHelper::getParams('com_phocagallery');
		$this->t['enablethumbcreation']			= $params->get('enable_thumb_creation', 1 );
		$this->t['enablethumbcreationstatus'] 	= PhocaGalleryRenderAdmin::renderThumbnailCreationStatus((int)$this->t['enablethumbcreation']);
		$this->t['multipleuploadchunk']			= $params->get( 'multiple_upload_chunk', 0 );
		$this->t['large_image_width']	= $params->get( 'large_image_width', 640 );
		$this->t['large_image_height']	= $params->get( 'large_image_height', 480 );
		$this->t['javaboxwidth'] 		= $params->get( 'java_box_width', 480 );
		$this->t['javaboxheight'] 		= $params->get( 'java_box_height', 480 );
		$this->t['uploadmaxsize'] 		= $params->get( 'upload_maxsize', 3145728 );
		$this->t['uploadmaxsizeread'] 	= PhocaGalleryFile::getFileSizeReadable($this->t['uploadmaxsize']);
		$this->t['uploadmaxreswidth'] 	= $params->get( 'upload_maxres_width', 3072 );
		$this->t['uploadmaxresheight'] 	= $params->get( 'upload_maxres_height', 2304 );
		$this->t['enablejava'] 			= $params->get( 'enable_java', -1 );
		$this->t['enablemultiple'] 		= $params->get( 'enable_multiple', 0 );
		$this->t['multipleuploadmethod'] = $params->get( 'multiple_upload_method', 4 );
		$this->t['multipleresizewidth'] 	= $params->get( 'multiple_resize_width', -1 );
		$this->t['multipleresizeheight'] = $params->get( 'multiple_resize_height', -1 );


		if((int)$this->t['enablemultiple']  >= 0) {
			PhocaGalleryFileUploadMultiple::renderMultipleUploadLibraries();
		}
		$this->r = new PhocaGalleryRenderAdminView();


		$this->currentFolder = '';
		if (isset($this->folderstate->folder) && $this->folderstate->folder != '') {
			$this->currentFolder = $this->folderstate->folder;
		}

		// - - - - - - - - - -
		//TABS
		// - - - - - - - - - -
		$this->t['tab'] 			= Factory::getApplication()->input->get('tab', '', '', 'string');
		$this->t['displaytabs']	= 0;

		// MULTIPLE UPLOAD
		if((int)$this->t['enablemultiple']  >= 0) {
			$this->t['currenttab']['multipleupload'] = $this->t['displaytabs'];
			$this->t['displaytabs']++;
		} else {
			$this->t['currenttab']['multipleupload'] = 0;
		}

		// UPLOAD
		$this->t['currenttab']['upload'] = $this->t['displaytabs'];
		$this->t['displaytabs']++;



		// MULTIPLE UPLOAD
		if($this->t['enablejava']  >= 0) {
			$this->t['currenttab']['javaupload'] = $this->t['displaytabs'];
			$this->t['displaytabs']++;
		} else {
			$this->t['currenttab']['javaupload'] = 0;
		}

		// - - - - - - - - - - -
		// Upload
		// - - - - - - - - - - -
		$sU							= new PhocaGalleryFileUploadSingle();
		$sU->returnUrl				= 'index.php?option=com_phocagallery&view=phocagallerym&layout=edit&tab=upload&folder='. PhocaGalleryText::filterValue($this->currentFolder, 'folderpath');
		$sU->tab					= 'upload';
		$this->t['su_output']	= $sU->getSingleUploadHTML();
		$this->t['su_url']		= Uri::base().'index.php?option=com_phocagallery&task=phocagalleryu.upload&amp;'
								  .$this->session->getName().'='.$this->session->getId().'&amp;'
								  . Session::getFormToken().'=1&amp;viewback=phocagallerym&amp;'
								  .'folder='. PhocaGalleryText::filterValue($this->currentFolder, 'folderpath').'&amp;tab=upload';


		// - - - - - - - - - - -
		// Multiple Upload
		// - - - - - - - - - - -
		// Get infos from multiple upload
		$muFailed						= Factory::getApplication()->input->get( 'mufailed', '0', '', 'int' );
		$muUploaded						= Factory::getApplication()->input->get( 'muuploaded', '0', '', 'int' );
		$this->t['mu_response_msg']	= $muUploadedMsg 	= '';

		if ($muUploaded > 0) {
			$muUploadedMsg = Text::_('COM_PHOCAGALLERY_COUNT_UPLOADED_IMG'). ': ' . $muUploaded;
		}
		if ($muFailed > 0) {
			$muFailedMsg = Text::_('COM_PHOCAGALLERY_COUNT_NOT_UPLOADED_IMG'). ': ' . $muFailed;
		}

		if ($muFailed > 0 && $muUploaded > 0) {
			$this->t['mu_response_msg'] = '<div class="alert alert-info">'
			.'<button type="button" class="close" data-dismiss="alert">&times;</button>'
			.Text::_('COM_PHOCAGALLERY_COUNT_UPLOADED_IMG'). ': ' . $muUploaded .'<br />'
			.Text::_('COM_PHOCAGALLERY_COUNT_NOT_UPLOADED_IMG'). ': ' . $muFailed.'</div>';
		} else if ($muFailed > 0 && $muUploaded == 0) {
			$this->t['mu_response_msg'] = '<div class="alert alert-error alert-danger">'
			.'<button type="button" class="close" data-dismiss="alert">&times;</button>'
			.Text::_('COM_PHOCAGALLERY_COUNT_NOT_UPLOADED_IMG'). ': ' . $muFailed.'</div>';
		} else if ($muFailed == 0 && $muUploaded > 0){
			$this->t['mu_response_msg'] = '<div class="alert alert-success">'
			.'<button type="button" class="close" data-dismiss="alert">&times;</button>'
			.Text::_('COM_PHOCAGALLERY_COUNT_UPLOADED_IMG'). ': ' . $muUploaded.'</div>';
		} else {
			$this->t['mu_response_msg'] = '';
		}

		if((int)$this->t['enablemultiple']  >= 0) {


			$mU						= new PhocaGalleryFileUploadMultiple();
			$mU->frontEnd			= 0;
			$mU->method				= $this->t['multipleuploadmethod'];
			$mU->url				= Uri::base().'index.php?option=com_phocagallery&task=phocagalleryu.multipleupload&amp;'
									 .$this->session->getName().'='.$this->session->getId().'&'
									 . Session::getFormToken().'=1&tab=multipleupload&folder='. PhocaGalleryText::filterValue($this->currentFolder, 'folderpath');
			$mU->reload				= Uri::base().'index.php?option=com_phocagallery&view=phocagallerym&layout=edit&'
									.$this->session->getName().'='.$this->session->getId().'&'
									. Session::getFormToken().'=1&tab=multipleupload&folder='. PhocaGalleryText::filterValue($this->currentFolder, 'folderpath');
			$mU->maxFileSize		= PhocaGalleryFileUploadMultiple::getMultipleUploadSizeFormat($this->t['uploadmaxsize']);
			$mU->chunkSize			= '1mb';
			$mU->imageHeight		= $this->t['multipleresizeheight'];
			$mU->imageWidth			= $this->t['multipleresizewidth'];
			$mU->imageQuality		= 100;
			$mU->renderMultipleUploadJS(0, $this->t['multipleuploadchunk']);
			$this->t['mu_output']= $mU->getMultipleUploadHTML();
		}

		// - - - - - - - - - - -
		// Java Upload
		// - - - - - - - - - - -
		if((int)$this->t['enablejava']  >= 0) {
			$jU							= new PhocaGalleryFileUploadJava();
			$jU->width					= $this->t['javaboxwidth'];
			$jU->height					= $this->t['javaboxheight'];
			$jU->resizewidth			= $this->t['multipleresizewidth'];
			$jU->resizeheight			= $this->t['multipleresizeheight'];
			$jU->uploadmaxsize			= $this->t['uploadmaxsize'];
			$jU->returnUrl				= Uri::base().'index.php?option=com_phocagallery&view=phocagallerym&layout=edit&tab=javaupload&folder='. PhocaGalleryText::filterValue($this->currentFolder, 'folderpath');
			$jU->url					= Uri::base(). 'index.php?option=com_phocagallery&task=phocagalleryu.javaupload&'
									 .$this->session->getName().'='.$this->session->getId().'&'
									 . Session::getFormToken().'=1&viewback=phocagallerym&tab=javaupload&folder='. PhocaGalleryText::filterValue($this->currentFolder, 'folderpath');
			$jU->source 				= Uri::root(true).'/media/com_phocagallery/js/jupload/wjhk.jupload.jar';
			$this->t['ju_output']	= $jU->getJavaUploadHTML();

		}
		$this->t['ftp'] 			= !ClientHelper::hasCredentials('ftp');

		$this->addToolbar();
		parent::display($tpl);
		echo HTMLHelper::_('behavior.keepalive');
	}

	protected function addToolbar() {

		require_once JPATH_COMPONENT.'/helpers/phocagallerym.php';
		Factory::getApplication()->input->set('hidemainmenu', true);
		$state	= $this->get('State');
		$canDo	= PhocaGalleryMHelper::getActions($state->get('filter.multiple'));

		ToolbarHelper::title( Text::_( 'COM_PHOCAGALLERY_MULTIPLE_ADD' ), 'plus' );

		if ($canDo->get('core.create')){
			ToolbarHelper::save('phocagallerym.save', 'JToolbar_SAVE');
		}

		ToolbarHelper::cancel('phocagallerym.cancel', 'JToolbar_CLOSE');
		ToolbarHelper::divider();
		ToolbarHelper::help( 'screen.phocagallery', true );
	}
}
?>
