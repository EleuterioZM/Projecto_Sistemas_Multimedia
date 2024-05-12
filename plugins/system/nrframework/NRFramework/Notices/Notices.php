<?php

/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2022 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 * @credits			https://github.com/codeigniter4/CodeIgniter4/blob/develop/app/Config/Mimes.php
*/

namespace NRFramework\Notices;

// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use \NRFramework\Extension;

class Notices
{
	/**
	 * The payload.
	 * 
	 * @var  array
	 */
	private $payload;
	
	/**
	 * The extension's ext_element we are showing notices.
	 * 
	 * Example: acf
	 * 
	 * @var  string
	 */
	private $ext_element;

	/**
	 * The extension's main XML file location folder.
	 * 
	 * Example: plg_system_acf, com_rstbox
	 * 
	 * @var  string
	 */
	private $ext_xml;

	/**
	 * The extension type.
	 * 
	 * Example: plugin, component, module, etc...
	 * 
	 * @var  string
	 */
	private $ext_type = 'component';

	/**
	 * The notices to exclude.
	 * 
	 * @var  array
	 */
	private $exclude = [];

	/**
	 * Define how old (in days) the file that holds all extensions data needs to be set as expired,
	 * so we can fetch new data.
	 * 
	 * @var  int
	 */
	private $extensions_data_file_days_old = 1;

	/**
     * Download Key.
     *
     * @var  String
     */
    protected $download_key = null;

	/**
	 * The license data for the given download key.
	 * 
	 * @var  array
	 */
	protected $license_data = [];
	
	/**
     * Notices Instance.
     *
     * @var  Notices
     */
    private static $instance;
	
	public function __construct($payload = [])
	{
		$this->payload = $payload;

		$this->ext_element = isset($this->payload['ext_element']) ? $this->payload['ext_element'] : '';
		$this->ext_xml = isset($this->payload['ext_xml']) ? $this->payload['ext_xml'] : '';
		$this->ext_type = isset($this->payload['ext_type']) ? $this->payload['ext_type'] : $this->ext_type;
		$this->exclude = isset($this->payload['exclude']) ? $this->payload['exclude'] : [];

		$this->download_key = \NRFramework\Functions::getDownloadKey();
	}

    /**
     * Returns class instance
	 * 
	 * @param   array   $payload
     *
     * @return  object
     */
    public static function getInstance($payload = [])
    {
        if (is_null(self::$instance))
        {
            self::$instance = new self($payload);
        }

        return self::$instance;
    }

	/**
	 * Show all available notices.
	 * 
	 * @return  void
	 */
	public function show()
	{
		\JHtml::stylesheet('plg_system_nrframework/notices.css', ['relative' => true, 'version' => 'auto']);
		\JHtml::script('plg_system_nrframework/notices.js', ['relative' => true, 'version' => 'auto']);

		$payload = [
			'ext_element' => $this->ext_element,
			'ext_xml' => $this->ext_xml,
			'ext_type' => $this->ext_type,
			'exclude' => $this->exclude
		];

		echo \JLayoutHelper::render('notices/tmpl', $payload, dirname(dirname(__DIR__)) . '/layouts');
	}

	/**
	 * Returns the base notices.
	 * 
	 * @param   array  $notices
	 * 
	 * @return  void
	 */
	private function getBaseNotices()
	{
		$base_notices = [
			'Outdated',
			'DownloadKey',
			'Geolocation',
			'UpgradeToPro',
			'UpgradeToBundle'
		];

		// Exclude notices we should not display
		if (count($this->exclude))
		{
			foreach ($base_notices as $key => $notice)
			{
				if (!in_array($notice, $this->exclude))
				{
					continue;
				}

				unset($base_notices[$key]);
			}
		}

		$notices = [];

		// Initialize notices
		foreach ($base_notices as $key => $notice)
		{
			$class = '\NRFramework\Notices\Notices\\' . $notice;

			// Skip empty notice
			if (!$html = (new $class($this->payload))->render())
			{
				continue;
			}
			
			$notices[strtolower($notice)] = $html;
		}

		return $notices;
	}

	/**
	 * Returns which license-related notices to show.
	 * 
	 * Notices:
	 * - Extension expires in date
	 * - Extension expired at date
	 * 
	 * @return  array
	 */
	private function getLicensesBasedNoticesToShow()
	{
		// If no data found for this extension, abort
		if (!$extension_data = \NRFramework\Notices\Helper::getExtensionDetails($this->license_data, $this->ext_element))
		{
			return false;
		}

		if (!array_key_exists('active', $extension_data))
		{
			return;
		}

		$notices = [];

		// Active subscription and we have a expiration date
		if ($extension_data['active'] && array_key_exists('expires_in', $extension_data) && $extension_data['expires_in'])
		{
			$notices[] = (new Notices\Expiring(array_merge($this->payload, [
				'expires_in' => $extension_data['expires_in'],
				'plan' => $extension_data['plan']
			])))->render();
		}

		/**
		 * We should not have an active subscription and the "expired_at" date must be set.
		 * 
		 * If "active" is true and an "expired_at" date is set, it means we have a Bundle plan.
		 */
		if (!$extension_data['active'] && array_key_exists('expired_at', $extension_data) && $extension_data['expired_at'])
		{
			$notices[] = (new Notices\Expired(array_merge($this->payload, [
				'expired_at' => $extension_data['expired_at'],
				'plan' => $extension_data['plan']
			])))->render();
		}

		if (!$notices)
		{
			return;
		}

		return implode('', $notices);
	}

	/**
	 * Returns the based notices:
	 * 
	 * Notices:
	 * - Base notices
	 * 	 - Outdated
	 * 	 - Download Key
	 * 	 - Geolocation
	 * 	 - Upgrade To Pro
	 * 	 - Upgrade To Bundle
	 * - Update notice
	 * - Extension expires in date
	 * - Extension expired at date
	 * - Rate (If none of the license-related notices appear)
	 * 
	 * @return  string
	 */
	public function getNotices()
	{
		// Check and Update the local licenses data
		$this->checkAndUpdateExtensionsData();
		
		$notices = $this->getBaseNotices();
		
		// Show Update Notice
		if ($update_html = (new Notices\Update($this->payload))->render())
		{
			$notices['update'] = $update_html;
		}

		if ($license_notices = $this->getLicensesBasedNoticesToShow())
		{
			$notices['license'] = $license_notices;
		}
		else if ($rate_html = (new Notices\Rate($this->payload))->render())
		{
			$notices['rate'] = $rate_html;
		}

		return $notices;
	}

	/**
	 * Checks whether the current extensions data has expired and updates the data file.
	 * 
	 * Also checks and sets the installation date of the extension.
	 * 
	 * @return  bool
	 */
	public function checkAndUpdateExtensionsData()
	{
		// Sets licenses information
		$this->license_data = $this->getLicenseData();

		// Add the license data to the payload as well
		$this->payload['license_data'] = $this->license_data;

		// Set installation date
		Extension::setInstallationDate($this->ext_element, gmdate('Y-m-d H:i:s'));
	}

	/**
	 * Returns the license data from the server for the given download key.
	 * 
	 * @return  array
	 */
	private function getLicenseData()
	{
		/***
		 * Fetch new data
		 */

		// License Check Endpoint
		$url = TF_CHECK_LICENSE;
		// Set Download Key
		$url = str_replace('{{DOWNLOAD_KEY}}', $this->download_key, $url);
		
		// No response, abort
		if (!$response = $this->curlRequest($url))
		{
			return;
		}

		return json_decode($response, true);
	}
	
	/**
	 * Executes a cURL request.
	 * 
	 * @param   string  $url
	 * 
	 * @return  mixed
	 */
	private function curlRequest($url)
	{
		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);

        $response = curl_exec($ch);
        curl_close($ch);

		return $response;
	}
}