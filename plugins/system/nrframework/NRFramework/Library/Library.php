<?php

/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2022 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

namespace NRFramework\Library;

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

class Library
{
	/**
	 * Library item info popup.
	 * 
	 * @var  string
	 */
	private $info_modal_id = 'tf-library-item-info-popup';

	/**
	 * Library preview popup.
	 * 
	 * @var  string
	 */
	private $preview_modal_id = 'tf-library-preview-popup';

	/**
	 * The library settings
	 * 
	 * @var  array
	 */
	public $library_settings = [];

	/**
	 * Favorites.
	 * 
	 * @var  Faovirtes
	 */
	public $favorites;

	/**
	 * Templates.
	 * 
	 * @var  Templates
	 */
	public $templates;

	public function __construct($library_settings = [])
	{
		$this->library_settings = $library_settings;

		$this->favorites = new Favorites($this);
		$this->templates = new Templates($this);
	}

	public function init()
	{
		$this->prepare();

		// Enqueue media
		$this->register_media();

		// Add library popups
		$this->add_library_popup();
		$this->add_library_item_info_popup();
		$this->add_library_preview_template_popup();
	}

	/**
	 * Prepares the Library.
	 * 
	 * @return  void
	 */
	private function prepare()
	{
		$this->library_settings['preview_url'] = TF_TEMPLATES_SITE_URL . '?template_preview=1&template=TEMPLATE_ID&project=' . $this->library_settings['project'];
		
		$this->prepareModal();
	}

    /**
     * Adds the toolbar to the modal's header.
     *
     * @return   void
     */
    public function prepareModal()
    {
		// Upgrade to Pro Button
		if ($this->library_settings['project_license_type'] === 'lite')
		{
			?>
			<a href="#" style="display:none;" class="tf-button outline red tf-header-upgrade-button" data-pro-only="<?php echo Text::_('NR_PRO_TEMPLATES'); ?>">
				<svg class="icon" width="16" height="14" viewBox="0 0 16 14" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M7.5 10C7.5 10.2761 7.72386 10.5 8 10.5C8.27614 10.5 8.5 10.2761 8.5 10L7.5 10ZM8.35355 3.64645C8.15829 3.45118 7.84171 3.45118 7.64645 3.64645L4.46447 6.82843C4.2692 7.02369 4.2692 7.34027 4.46447 7.53553C4.65973 7.7308 4.97631 7.7308 5.17157 7.53553L8 4.70711L10.8284 7.53553C11.0237 7.7308 11.3403 7.7308 11.5355 7.53553C11.7308 7.34027 11.7308 7.02369 11.5355 6.82843L8.35355 3.64645ZM8.5 10L8.5 4L7.5 4L7.5 10L8.5 10Z" fill="currentColor"/>
					<path d="M14 7C14 10.3137 11.3137 13 8 13C4.68629 13 2 10.3137 2 7C2 3.68629 4.68629 1 8 1C11.3137 1 14 3.68629 14 7Z" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
				<?php echo Text::_('NR_UPGRADE_TO_PRO'); ?>
			</a>
			<?php
		}
		// Main Library Modal Header Toolbar
        ?>
		<div style="display:none;" class="actions-wrapper tfTemplatesLibraryModalToolbar">
			<ul class="actions">
				<li>
					<a href="<?php echo $this->library_settings['create_new_template_link']; ?>" title="<?php echo Text::_('NR_START_FROM_SCRATCH'); ?>">
						<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
							<circle cx="12" cy="12" r="8" stroke="currentColor"/>
							<line x1="11.9277" y1="8.5" x2="11.9277" y2="15.5" stroke="currentColor" stroke-linecap="round"/>
							<line x1="15.5" y1="11.9285" x2="8.5" y2="11.9285" stroke="currentColor" stroke-linecap="round"/>
						</svg>
					</a>
				</li>
				<li>
					<a href="#" class="tf-templates-refresh-btn" title="<?php echo Text::_('NR_REFRESH_TEMPLATES'); ?>">
						<svg class="checkmark" width="18" height="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
							<circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none" />
							<path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8" stroke-width="5" />
						</svg>
						<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M20 12C20 7.58172 16.4183 4 12 4C7.58172 4 4 7.58172 4 12C4 16.4183 7.58172 20 12 20C14.2879 20 16.3514 19.0396 17.8095 17.5" stroke="currentColor" stroke-linecap="round"/>
							<path class="tip" d="M22.25 9.99999L20 12.25L17.75 9.99999" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
						</svg>
					</a>
				</li>
				<li>
					<a href="https://www.tassos.gr/contact?topic=Custom Development&extension=<?php echo $this->library_settings['project_name']; ?>" title="<?php echo Text::_('NR_REQUEST_TEMPLATE'); ?>" target="_blank">
						<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M8.8 16H9.3C9.3 15.7239 9.07614 15.5 8.8 15.5V16ZM8.8 20H8.3C8.3 20.1905 8.40823 20.3644 8.57912 20.4486C8.75002 20.5327 8.95387 20.5125 9.10486 20.3963L8.8 20ZM13.7304 16.2074L14.0353 16.6037L13.7304 16.2074ZM5 4.5H19V3.5H5V4.5ZM19.5 5V15H20.5V5H19.5ZM4.5 15V5H3.5V15H4.5ZM8.8 15.5H5V16.5H8.8V15.5ZM9.3 20V16H8.3V20H9.3ZM19 15.5H14.3401V16.5H19V15.5ZM13.4256 15.8111L8.49514 19.6037L9.10486 20.3963L14.0353 16.6037L13.4256 15.8111ZM3.5 15C3.5 15.8284 4.17157 16.5 5 16.5V15.5C4.72386 15.5 4.5 15.2761 4.5 15H3.5ZM19.5 15C19.5 15.2761 19.2761 15.5 19 15.5V16.5C19.8284 16.5 20.5 15.8284 20.5 15H19.5ZM14.3401 15.5C14.0093 15.5 13.6878 15.6094 13.4256 15.8111L14.0353 16.6037C14.1227 16.5365 14.2299 16.5 14.3401 16.5V15.5ZM19 4.5C19.2761 4.5 19.5 4.72386 19.5 5H20.5C20.5 4.17157 19.8284 3.5 19 3.5V4.5ZM5 3.5C4.17157 3.5 3.5 4.17157 3.5 5H4.5C4.5 4.72386 4.72386 4.5 5 4.5V3.5Z" fill="currentColor"/>
						</svg>
					</a>
				</li>
				<li>
					<a href="#" class="tf-templates-library-toggle-fullscreen">
						<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M9 2H14V7" stroke="currentColor" stroke-width="1"/>
							<path d="M7 14L2 14L2 9" stroke="currentColor" stroke-width="1"/>
						</svg>
						<svg class="on-fullscreen" width="16" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M14 5L9 5L9 3.97232e-08" stroke="currentColor" stroke-width="1"/>
							<path d="M0 9H5L5 14" stroke="currentColor" stroke-width="1"/>
						</svg>
					</a>
				</li>
				<li>
					<a href="#" class="tf-modal-close" data-bs-dismiss="modal" data-dismiss="modal">
						<svg height="14" viewBox="0 0 14 15" fill="none" xmlns="http://www.w3.org/2000/svg">
							<rect x="14" y="12.5933" width="2.47487" height="17.3241" transform="rotate(135 14 12.5933)" fill="currentColor"/>
							<rect width="2.47487" height="17.3241" transform="matrix(-0.707109 -0.707105 0.707109 -0.707105 1.75 14.3433)" fill="currentColor"/>
						</svg>
					</a>
				</li>
			</ul>
		</div>
		<?php // Templates Library Info Popup Header Toolbar ?>
		<div style="display:none;" class="actions-wrapper tfInfoTemplatesLibraryModalToolbar">
			<ul class="actions">
				<li>
					<a href="#" class="tf-modal-close" data-bs-dismiss="modal" data-dismiss="modal">
						<svg height="14" viewBox="0 0 14 15" fill="none" xmlns="http://www.w3.org/2000/svg">
							<rect x="14" y="12.5933" width="2.47487" height="17.3241" transform="rotate(135 14 12.5933)" fill="currentColor"/>
							<rect width="2.47487" height="17.3241" transform="matrix(-0.707109 -0.707105 0.707109 -0.707105 1.75 14.3433)" fill="currentColor"/>
						</svg>
					</a>
				</li>
			</ul>
		</div>
		<?php // Templates Library Preview Popup Header Toolbar ?>
		<div style="display:none;" class="actions-wrapper tfPreviewTemplatesLibraryModalToolbar">
			<ul class="actions">
				<li>
					<a href="#" class="tf-modal-close" data-bs-dismiss="modal" data-dismiss="modal">
						<svg height="14" viewBox="0 0 14 15" fill="none" xmlns="http://www.w3.org/2000/svg">
							<rect x="14" y="12.5933" width="2.47487" height="17.3241" transform="rotate(135 14 12.5933)" fill="currentColor"/>
							<rect width="2.47487" height="17.3241" transform="matrix(-0.707109 -0.707105 0.707109 -0.707105 1.75 14.3433)" fill="currentColor"/>
						</svg>
					</a>
				</li>
			</ul>
		</div>
		<?php // Templates Library Preview Popup Actions on the left side of the header ?>
		<div style="display:none;" class="modal-title-wrapper tfPreviewTemplatesLibraryModalToolbarLeft">
			<a href="#" class="tf-modal-close tf-templates-library-preview-go-back" data-bs-dismiss="modal" data-dismiss="modal">
				<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M16 4L8 12L16 20" stroke="currentColor" stroke-linecap="round"/>
				</svg>
			</a>
			<a href="#" class="tf-templates-library-refresh-demo">
				<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M20 12C20 7.58172 16.4183 4 12 4C7.58172 4 4 7.58172 4 12C4 16.4183 7.58172 20 12 20C14.2879 20 16.3514 19.0396 17.8095 17.5" stroke="currentColor" stroke-linecap="round"/>
					<path d="M22.25 9.99999L20 12.25L17.75 9.99999" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
			</a>
			<h3 class="modal-title"></h3>
		</div>
		<?php // Templates Library Preview Popup responsive control actions in the middle of the header ?>
		<div style="display:none;" class="tf-templates-library-preview-responsive-devices tfPreviewTemplatesLibraryModalToolbarCenter">
			<svg class="tf-templates-library-preview-responsive-device active" data-device="desktop" width="35" height="24" viewBox="0 0 35 24" fill="none" xmlns="http://www.w3.org/2000/svg">
				<rect x="1" y="1" width="33" height="19" rx="2" stroke="currentColor" stroke-width="2"/>
				<path d="M16 21.5V21C16 20.4477 16.4477 20 17 20H19C19.5523 20 20 20.4477 20 21V21.5C20 22.0523 20.4477 22.5 21 22.5H23.25C23.6642 22.5 24 22.8358 24 23.25C24 23.6642 23.6642 24 23.25 24H12.75C12.3358 24 12 23.6642 12 23.25C12 22.8358 12.3358 22.5 12.75 22.5H15C15.5523 22.5 16 22.0523 16 21.5Z" fill="currentColor"/>
			</svg>
			<svg class="tf-templates-library-preview-responsive-device" data-device="tablet" width="19" height="24" viewBox="0 0 19 24" fill="none" xmlns="http://www.w3.org/2000/svg">
				<rect x="1" y="1" width="17" height="22" rx="2" stroke="currentColor" stroke-width="2"/>
				<circle cx="9.5" cy="19.5" r="1.5" fill="currentColor"/>
			</svg>
			<svg class="tf-templates-library-preview-responsive-device" data-device="mobile" width="15" height="24" viewBox="0 0 15 24" fill="none" xmlns="http://www.w3.org/2000/svg">
				<rect x="1" y="1" width="13" height="22" rx="2" stroke="currentColor" stroke-width="2"/>
				<line x1="5" y1="2" x2="10" y2="2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
			</svg>
		</div>
        <?php

		/**
		 * Add the necessary HTML for each modal's header by using the
		 * main popup ID given by the initiator extension.
		 */
        \JFactory::getDocument()->addScriptDeclaration('
            document.addEventListener("DOMContentLoaded", function() {
				/**
				 * Main Templates Library Popup
				 */
				let mainPopup = document.querySelector("#' . $this->library_settings['id'] . '");

				/**
				 * Append Upgrade to Pro button to header
				 */
				let upgradeButton = document.querySelector(".tf-header-upgrade-button");
				if (upgradeButton) {
					upgradeButton.removeAttribute("style");
					mainPopup.querySelector(".modal-header").append(upgradeButton);
				}
				
				// Append actions
                let modalToolbar = document.querySelector(".tfTemplatesLibraryModalToolbar")
                modalToolbar.removeAttribute("style");
                mainPopup.querySelector(".modal-header").append(modalToolbar);

				// Add class to library popup
                mainPopup.classList.add("tf-templates-library", "tf-templates-library-popup", "' . (defined('nrJ4') ? 'isJ4' : 'isJ3') . '");

				/**
				 * Info Templates Library Popup
				 */
				let infoPopup = document.querySelector("#' . $this->info_modal_id . '");

				// Append actions
                modalToolbar = document.querySelector(".tfInfoTemplatesLibraryModalToolbar")
                modalToolbar.removeAttribute("style");
                infoPopup.querySelector(".modal-header").append(modalToolbar);

				// Add class to info popup
                infoPopup.classList.add("tf-templates-library-item-info", "tf-templates-library-popup", "' . (defined('nrJ4') ? 'isJ4' : 'isJ3') . '");

				/**
				 * Preview Templates Library Popup
				 */
				let previewPopup = document.querySelector("#' . $this->preview_modal_id . '");

				// Append toolbar on the left side of the header
                modalToolbar = document.querySelector(".tfPreviewTemplatesLibraryModalToolbarLeft").cloneNode(true);
                modalToolbar.removeAttribute("style");
                previewPopup.querySelector(".modal-header").insertBefore(modalToolbar, previewPopup.querySelector(".modal-header").firstChild);

				// Append responsive icons on the center of the header
                modalToolbar = document.querySelector(".tfPreviewTemplatesLibraryModalToolbarCenter").cloneNode(true);
                modalToolbar.removeAttribute("style");
                previewPopup.querySelector(".modal-header").append(modalToolbar);

				// Append actions
                modalToolbar = document.querySelector(".tfPreviewTemplatesLibraryModalToolbar")
                modalToolbar.removeAttribute("style");
                previewPopup.querySelector(".modal-header").append(modalToolbar);

				// Add class to preview popup
                previewPopup.classList.add("tf-templates-library-popup-preview", "tf-templates-library-popup", "' . (defined('nrJ4') ? 'isJ4' : 'isJ3') . '");
            });
        ');
    }

	/**
	 * Adds admin media
	 * 
	 * @return  void
	 */
	public function register_media()
	{
		// Templates Library CSS
		\JHtml::stylesheet('plg_system_nrframework/tf_templates_library.css', ['relative' => true, 'version' => 'auto']);
		
		// Templates Library JS
		\JHtml::script('plg_system_nrframework/tf_templates_library.js', ['relative' => true, 'version' => 'auto']);

		// Add Javascript options
		$doc = \JFactory::getDocument();
		$options = $doc->getScriptOptions('tassos_framework');
		$options = is_array($options) ? $options : [];
		$options = [
			'project_name' => $this->library_settings['project_name'],
			'pro' => Text::_('NR_PRO'),
			'lite' => Text::_('NR_LITE'),
			'license_key' => Text::_('NR_LICENSE_KEY'),
			'license' => $this->library_settings['license_key'],
			'install_extension' => TEXT::_('NR_INSTALL_EXTENSION'),
			'update_extension' => TEXT::_('NR_UPDATE_EXTENSION'),
			'templates_library_ajax_url' => \JURI::base() . '?option=com_ajax&format=raw&plugin=nrframework&task=TemplatesLibrary',
			'csrf_token' => \JSession::getFormToken()
		];
		$doc->addScriptOptions('tassos_framework', $options);
	}

	/**
	 * Adds the popup at the footer of the page. Appears when you click the "New" / "Add New" button.
	 * 
	 * @return  void
	 */
	public function add_library_popup()
	{
		$payload = [
			'title' => $this->library_settings['title'],
			'closeButton' => false,
			'backdrop' => 'static'
		];

        $content = \JLayoutHelper::render('library/tmpl', $this->library_settings, JPATH_PLUGINS . '/system/nrframework/layouts');

		echo \JHtml::_('bootstrap.renderModal', $this->library_settings['id'], $payload, $content);
	}

	/**
	 * Adds the popup that displays the info for each template.
	 * 
	 * @return  void
	 */
	public function add_library_item_info_popup()
	{
		$info_payload = [
			'category_label' => $this->library_settings['main_category_label']
		];
        $content = \JLayoutHelper::render('library/info_popup', $info_payload, JPATH_PLUGINS . '/system/nrframework/layouts');

		$payload = [
			'title' => 'Template Title',
			'closeButton' => false,
			'backdrop' => 'static'
		];
		
		echo \JHtml::_('bootstrap.renderModal', $this->info_modal_id, $payload, $content);
	}

	/**
	 * Adds the popup at that allows us to preview a template.
	 * 
	 * @return  void
	 */
	public function add_library_preview_template_popup()
	{
        $content = \JLayoutHelper::render('library/preview', [], JPATH_PLUGINS . '/system/nrframework/layouts');

		$payload = [
			'title' => 'Template Title',
			'closeButton' => false,
			'backdrop' => 'static'
		];
		
		echo \JHtml::_('bootstrap.renderModal', $this->preview_modal_id, $payload, $content);
	}

	/**
	 * Return templates folder path
	 *  
	 * @return  string
	 */
	public function getTemplatesPath()
	{
		return JPATH_ROOT . '/media/com_rstbox/templates/';
	}

	/**
	 * Returns the Novarain Framework Plugin URL.
	 * 
	 * @return  string
	 */
	public function getNRFrameworkPluginURL()
	{
		return \JURI::base() . 'index.php?option=com_plugins&task=plugin.edit&extension_id=' . \NRFramework\Extension::getID('nrframework', 'plugin', 'system');
	}

	/**
	 * Returns a library settings value.
	 * 
	 * @param   string  $key
	 * @param   string  $default
	 * 
	 * @return  string
	 */
	public function getLibrarySetting($key, $default = '')
	{
		return isset($this->library_settings[$key]) ? $this->library_settings[$key] : $default;
	}
}