<?php

/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

namespace NRFramework\Notices\Notices;

defined('_JEXEC') or die;

use \NRFramework\Functions;
use Joomla\CMS\Language\Text;

class Notice
{
	/**
	 * The notice payload.
	 * 
	 * @var  array
	 */
	protected $notice_payload = [];
	
	/**
	 * The payload.
	 * 
	 * @var  array
	 */
	protected $payload = [
		/**
		 * The extension's element we are showing notices.
		 * 
		 * Example: com_rstbox, plg_system_acf
		 */
		'ext_element' => '',
	
		/**
		 * The extension's main XML file location folder.
		 */
		'ext_xml' => '',
	
		/**
		 * The extension type.
		 * 
		 * Example: component, plugin, module, etc...
		 */
		'ext_type' => 'component',
	
		/**
		 * The notice type.
		 */
		'type' => '',
	
		/**
		 * The notice icon.
		 * 
		 * Inner part of the SVG icon.
		 */
		'icon' => '',
	
		/**
		 * An array containing classes attached to the notice wrapper HTML Element.
		 */
		'class' => '',
	
		/**
		 * Whether the notice is dismissible.
		 */
		'dismissible' => true,
	
		/**
		 * The notice title.
		 */
		'title' => '',
	
		/**
		 * The notice description.
		 */
		'description' => '',
	
		/**
		 * The tooltip text explaining this action.
		 */
		'tooltip' => '',
	
		/**
		 * The notice actions.
		 */
		'actions' => ''
	];

	/**
	 * The extension name.
	 * 
	 * @var  String
	 */
	protected $extension_name;

	/**
     * Factory.
     *
     * @var  Factory
     */
    protected $factory;

	public function __construct($payload = [])
	{
		$this->payload = array_merge($this->payload, $this->notice_payload, $payload);

		$this->factory = new \NRFramework\Factory();
		
		$this->extension_name = $this->getExtensionName();
	}

	/**
	 * Renders notice.
	 * 
	 * @return  string
	 */
	public function render()
	{
		if (!$this->canRun())
		{
			return;
		}

		$this->prepare();

		return \JLayoutHelper::render('notices/notice', $this->payload, dirname(dirname(dirname(__DIR__))) . '/layouts');
	}

	/**
	 * Prepares the notice.
	 * 
	 * @return  void
	 */
	private function prepare()
	{
		// Set title
		if (method_exists($this, 'getTitle'))
		{
			$this->payload['title'] = $this->getTitle();
		}

		// Set description
		if (method_exists($this, 'getDescription'))
		{
			$this->payload['description'] = $this->getDescription();
		}

		// Set actions
		if (method_exists($this, 'getActions'))
		{
			$this->payload['actions'] = $this->getActions();
		}
		
		if (isset($this->payload['type']) && !empty($this->payload['type']))
		{
			// Set type of notice
			$this->payload['class'] .= ' ' . $this->payload['type'];

			// Set Set icon
			switch ($this->payload['type'])
			{
				case 'warning':
					$icon = '<mask id="mask0_105_19" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="40" height="40"><rect width="40" height="40" fill="#D9D9D9"/></mask><g mask="url(#mask0_105_19)"><path d="M1.66669 35L20 3.33331L38.3334 35H1.66669ZM7.41669 31.6666H32.5834L20 9.99998L7.41669 31.6666ZM20 30C20.4722 30 20.8684 29.84 21.1884 29.52C21.5072 29.2011 21.6667 28.8055 21.6667 28.3333C21.6667 27.8611 21.5072 27.4655 21.1884 27.1466C20.8684 26.8266 20.4722 26.6666 20 26.6666C19.5278 26.6666 19.1322 26.8266 18.8134 27.1466C18.4934 27.4655 18.3334 27.8611 18.3334 28.3333C18.3334 28.8055 18.4934 29.2011 18.8134 29.52C19.1322 29.84 19.5278 30 20 30ZM18.3334 25H21.6667V16.6666H18.3334V25Z" fill="#F4B400"/></g>';
					break;
				case 'error': 
					$icon = '<mask id="mask0_105_7" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="40" height="40"><rect width="40" height="40" fill="#D9D9D9"/></mask><g mask="url(#mask0_105_7)"><path d="M20.0001 28.3334C20.4723 28.3334 20.8684 28.1734 21.1884 27.8534C21.5073 27.5345 21.6668 27.139 21.6668 26.6668C21.6668 26.1946 21.5073 25.7984 21.1884 25.4784C20.8684 25.1595 20.4723 25.0001 20.0001 25.0001C19.5279 25.0001 19.1323 25.1595 18.8134 25.4784C18.4934 25.7984 18.3334 26.1946 18.3334 26.6668C18.3334 27.139 18.4934 27.5345 18.8134 27.8534C19.1323 28.1734 19.5279 28.3334 20.0001 28.3334ZM20.0001 21.6668C20.4723 21.6668 20.8684 21.5068 21.1884 21.1868C21.5073 20.8679 21.6668 20.4723 21.6668 20.0001V13.3334C21.6668 12.8612 21.5073 12.4651 21.1884 12.1451C20.8684 11.8262 20.4723 11.6668 20.0001 11.6668C19.5279 11.6668 19.1323 11.8262 18.8134 12.1451C18.4934 12.4651 18.3334 12.8612 18.3334 13.3334V20.0001C18.3334 20.4723 18.4934 20.8679 18.8134 21.1868C19.1323 21.5068 19.5279 21.6668 20.0001 21.6668ZM20.0001 36.6668C17.6945 36.6668 15.5279 36.229 13.5001 35.3534C11.4723 34.479 9.70844 33.2918 8.20844 31.7918C6.70844 30.2918 5.52121 28.5279 4.64677 26.5001C3.77121 24.4723 3.33344 22.3057 3.33344 20.0001C3.33344 17.6945 3.77121 15.5279 4.64677 13.5001C5.52121 11.4723 6.70844 9.70844 8.20844 8.20844C9.70844 6.70844 11.4723 5.52066 13.5001 4.6451C15.5279 3.77066 17.6945 3.33344 20.0001 3.33344C22.3057 3.33344 24.4723 3.77066 26.5001 4.6451C28.5279 5.52066 30.2918 6.70844 31.7918 8.20844C33.2918 9.70844 34.479 11.4723 35.3534 13.5001C36.229 15.5279 36.6668 17.6945 36.6668 20.0001C36.6668 22.3057 36.229 24.4723 35.3534 26.5001C34.479 28.5279 33.2918 30.2918 31.7918 31.7918C30.2918 33.2918 28.5279 34.479 26.5001 35.3534C24.4723 36.229 22.3057 36.6668 20.0001 36.6668ZM20.0001 33.3334C23.7223 33.3334 26.8751 32.0418 29.4584 29.4584C32.0418 26.8751 33.3334 23.7223 33.3334 20.0001C33.3334 16.2779 32.0418 13.1251 29.4584 10.5418C26.8751 7.95844 23.7223 6.66677 20.0001 6.66677C16.2779 6.66677 13.1251 7.95844 10.5418 10.5418C7.95844 13.1251 6.66677 16.2779 6.66677 20.0001C6.66677 23.7223 7.95844 26.8751 10.5418 29.4584C13.1251 32.0418 16.2779 33.3334 20.0001 33.3334Z" fill="#DB4437"/></g>';
					break;
				case 'info':
					$icon = '<mask id="mask0_105_43" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="40" height="40"><rect width="40" height="40" fill="#D9D9D9"/></mask><g mask="url(#mask0_105_43)"><path d="M18.3333 28.3333H21.6666V18.3333H18.3333V28.3333ZM20 15C20.4722 15 20.8683 14.84 21.1883 14.52C21.5072 14.2011 21.6666 13.8056 21.6666 13.3333C21.6666 12.8611 21.5072 12.465 21.1883 12.145C20.8683 11.8261 20.4722 11.6667 20 11.6667C19.5278 11.6667 19.1322 11.8261 18.8133 12.145C18.4933 12.465 18.3333 12.8611 18.3333 13.3333C18.3333 13.8056 18.4933 14.2011 18.8133 14.52C19.1322 14.84 19.5278 15 20 15ZM20 36.6667C17.6944 36.6667 15.5278 36.2289 13.5 35.3533C11.4722 34.4789 9.70831 33.2917 8.20831 31.7917C6.70831 30.2917 5.52109 28.5278 4.64665 26.5C3.77109 24.4722 3.33331 22.3056 3.33331 20C3.33331 17.6944 3.77109 15.5278 4.64665 13.5C5.52109 11.4722 6.70831 9.70833 8.20831 8.20833C9.70831 6.70833 11.4722 5.52056 13.5 4.645C15.5278 3.77056 17.6944 3.33333 20 3.33333C22.3055 3.33333 24.4722 3.77056 26.5 4.645C28.5278 5.52056 30.2916 6.70833 31.7916 8.20833C33.2916 9.70833 34.4789 11.4722 35.3533 13.5C36.2289 15.5278 36.6666 17.6944 36.6666 20C36.6666 22.3056 36.2289 24.4722 35.3533 26.5C34.4789 28.5278 33.2916 30.2917 31.7916 31.7917C30.2916 33.2917 28.5278 34.4789 26.5 35.3533C24.4722 36.2289 22.3055 36.6667 20 36.6667ZM20 33.3333C23.7222 33.3333 26.875 32.0417 29.4583 29.4583C32.0416 26.875 33.3333 23.7222 33.3333 20C33.3333 16.2778 32.0416 13.125 29.4583 10.5417C26.875 7.95833 23.7222 6.66667 20 6.66667C16.2778 6.66667 13.125 7.95833 10.5416 10.5417C7.95831 13.125 6.66665 16.2778 6.66665 20C6.66665 23.7222 7.95831 26.875 10.5416 29.4583C13.125 32.0417 16.2778 33.3333 20 33.3333Z" fill="#4285F4"/></g>';
					break;
				case 'success':
					$icon = '<mask id="mask0_105_31" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="40" height="40"><rect width="40" height="40" fill="#D9D9D9"/></mask><g mask="url(#mask0_105_31)"><path d="M17.6666 27.6667L29.4166 15.9167L27.0833 13.5833L17.6666 23L12.9166 18.25L10.5833 20.5833L17.6666 27.6667ZM20 36.6667C17.6944 36.6667 15.5278 36.2289 13.5 35.3533C11.4722 34.4789 9.70831 33.2917 8.20831 31.7917C6.70831 30.2917 5.52109 28.5278 4.64665 26.5C3.77109 24.4722 3.33331 22.3056 3.33331 20C3.33331 17.6945 3.77109 15.5278 4.64665 13.5C5.52109 11.4722 6.70831 9.70834 8.20831 8.20834C9.70831 6.70834 11.4722 5.52057 13.5 4.64501C15.5278 3.77057 17.6944 3.33334 20 3.33334C22.3055 3.33334 24.4722 3.77057 26.5 4.64501C28.5278 5.52057 30.2916 6.70834 31.7916 8.20834C33.2916 9.70834 34.4789 11.4722 35.3533 13.5C36.2289 15.5278 36.6666 17.6945 36.6666 20C36.6666 22.3056 36.2289 24.4722 35.3533 26.5C34.4789 28.5278 33.2916 30.2917 31.7916 31.7917C30.2916 33.2917 28.5278 34.4789 26.5 35.3533C24.4722 36.2289 22.3055 36.6667 20 36.6667ZM20 33.3333C23.7222 33.3333 26.875 32.0417 29.4583 29.4583C32.0416 26.875 33.3333 23.7222 33.3333 20C33.3333 16.2778 32.0416 13.125 29.4583 10.5417C26.875 7.95834 23.7222 6.66668 20 6.66668C16.2778 6.66668 13.125 7.95834 10.5416 10.5417C7.95831 13.125 6.66665 16.2778 6.66665 20C6.66665 23.7222 7.95831 26.875 10.5416 29.4583C13.125 32.0417 16.2778 33.3333 20 33.3333Z" fill="#0F9D58"/></g>';
					break;
			}

			$this->payload['icon'] = $icon;
		}

		// Set whether dismissible
		if ($this->payload['dismissible'])
		{
			$this->payload['class'] .= ' alert-dismissible';
		}
	}

	/**
	 * Whether the notice can run.
	 * 
	 * @return  bool
	 */
	protected function canRun()
	{
		// If no title or description is given, do not run
		if (empty($this->payload['title']) && empty($this->payload['description']))
		{
			return false;
		}
		
		return true;
	}

	/**
	 * Returns the date difference between today and a given date in the future.
	 * 
	 * @param   string  $date1
	 * @param   string  $date2
	 * 
	 * @return  string
	 */
	protected function getDaysDifference($date1, $date2)
	{
		return (int) round(($date1 - $date2) / (60 * 60 * 24));
	}

	/**
	 * Returns the extension name.
	 * 
	 * @return  string
	 */
	protected function getExtensionName()
	{
		// Load extension's language file
		Functions::loadLanguage($this->payload['ext_element']);

		// Remove plugin folder prefix from plugins
		return str_replace('System -', '', Text::_($this->payload['ext_element']));
	}
}