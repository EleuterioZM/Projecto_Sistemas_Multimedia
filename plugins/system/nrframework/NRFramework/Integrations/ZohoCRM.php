<?php

/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

namespace NRFramework\Integrations;

// No direct access
defined('_JEXEC') or die;

class ZohoCRM extends Integration
{
	/**
	 *  Response Type
	 *
	 *  @var  string
	 */
	protected $response_type = 'xml';

	/**
	 *  Data Center API Endpoint
	 *
	 *  @var  string
	 */
	private $datacenter = 'crm.zoho.com';

	/**
	 * Create a new instance
	 *
	 * @param array $options The service's required options
	 */
	public function __construct($options)
	{
		parent::__construct();
		$this->setKey($options['authenticationToken']);

		if (isset($options['datacenter']) && !is_null($options['datacenter']) && !empty($options['datacenter']))
		{
			$this->datacenter = $options['datacenter'];
		}
	}
	
	/**
	 *  Subscribe user to ZohoCRM
	 *
	 *  https://www.zoho.eu/crm/help/api/insertrecords.html#Insert_records_into_Zoho_CRM_from_third-party_applications
	 *
	 *  @param   string   $email            User's email address
	 *  @param   array    $fields           Available form fields
	 *  @param   string   $module           Zoho module to be used
	 *  @param   boolean  $update_existing  Update existing users
	 *  @param   string   $workflow         Trigger the workflow rule while inserting record
	 *  @param   string   $approve          Approve records (Supports: Leads, Contacts, and Cases modules)
	 *
	 *  @return  void
	 */
	public function subscribe($email, $fields, $module = 'leads', $update_existing = true, $workflow = false, $approve = false)
	{
		$data = array(
			'authtoken'      => $this->key,
			'scope'          => 'crmapi',
			'xmlData'        => $this->buildModuleXML($email, $fields, $module),
			'duplicateCheck' => $update_existing ? '2' : '1',
			'wfTrigger'      => $workflow ? 'true' : 'false',
			'isApproval'     => $approve ? 'true' : 'false',
			'version'        => '4'
		);

		$this->endpoint = 'https://' . $this->datacenter . '/crm/private/xml/' . ucfirst($module) . '/insertRecords?' . http_build_query($data);

		$this->post('');
	}

	/**
	 *  Build the XML for each module
	 *
	 *  @param   string  $email            User's email address
	 *  @param   array   $fields           Form fields
	 *  @param   string  $module           Module to be used
	 *
	 *  @return  string                    The XML
	 */
	private function buildModuleXML($email, $fields, $module)
	{
		$xml = new SimpleXMLElement('<' . ucfirst($module) . '/>');
		$row = $xml->addChild('row');
		$row->addAttribute('no', '1');

		$xmlField = $row->addChild('FL', $email);
		$xmlField->addAttribute('val', 'Email');

		if (is_array($fields) && count($fields))
		{
			foreach ($fields as $field_key => $field_value)
			{
				$field_value = is_array($field_value) ? implode(',', $field_value) : $field_value;

				$xmlField = $row->addChild('FL', $field_value);
				$xmlField->addAttribute('val', $field_key);
			}
		}

		return $xml->asXML();
	}

	/**
	 *  Get the last error returned by either the network transport, or by the API.
	 *
	 *  @return  string
	 */
	public function getLastError()
	{
		$body = $this->last_response->body;

		if (isset($body->error))
		{
			return $body->error->message;
		}

		if (isset($body->result->row->error)) 
		{
			return $body->result->row->error->details;
		}

		return 'Unknown error';
	}

	/**
	 * Check if the response was successful or a failure. If it failed, store the error.
	 * 
	 * @return bool     If the request was successful
	 */
	public function determineSuccess()
	{
		$status  = $this->last_response->code;
		$success = ($status >= 200 && $status <= 299) ? true : false;

		if (!$success) 
		{
			return false;
		}

		$body = $this->last_response->body;

		if (!isset($body->result->row->success)) 
		{
			return false;
		}

		return ($this->request_successful = true);
	}
}