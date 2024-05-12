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

class IContact extends Integration
{
	public $accountID;

	public $clientFolderID;

	/**
	 * Create a new instance
	 * @param array $options The service's required options
	 */
	public function __construct($options)
	{
		parent::__construct();
		$this->endpoint = 'https://app.icontact.com/icp/a';
		$this->options->set('headers.API-Version', '2.2');
		$this->options->set('headers.API-AppId', $options['appID']);
		$this->options->set('headers.API-Username', $options['username']);
		$this->options->set('headers.API-Password', $options['appPassword']);
		$this->setAccountID($options['accountID']);
		$this->setClientFolderID($options['clientFolderID']);
	}

	/**
	 *  Finds and sets the iContact AccountID
	 *
	 *  @param  mixed  $accountID
	 */
	public function setAccountID($accountID = false)
	{
		if ($accountID)
		{
			$this->accountID = $accountID;
		}
		
		$accounts = $this->get('');

		if (!$this->success())
		{
			throw new \Exception($this->getLastError());
		}

		// Make sure the account is active
		if (intval($accounts['accounts'][0]['enabled']) === 1)
		{
			$this->accountID = (integer) $accounts['accounts'][0]['accountId'];
		}
		else
		{
			throw new \Exception(\JText::_('NR_ICONTACT_ACCOUNTID_ERROR'), 1);
		}
	}

	/**
	 *  Finds and sets the iContact ClientFolderID
	 *
	 *  @param  mixed  $clientFolderID
	 */
	public function setClientFolderID($clientFolderID = false)
	{
		if ($clientFolderID)
		{
			$this->clientFolderID = $clientFolderID;
		}

		// We need an existant accountID
		if (empty($this->accountID))
		{
			try
			{
				$this->setAccountID();
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}

		if ($clientFolder = $this->get($this->accountID . '/c/'))
		{
			$this->clientFolderID = $clientFolder['clientfolders'][0]['clientFolderId'];
		}
	}

	/**
	 *  Subscribes a user to an iContact List
	 *
	 *  API REFERENCE
	 *  https://www.icontact.com/developerportal/documentation/contacts
	 *
	 *  @param   string   $email
	 *  @param   object   $params  The extra form fields
	 *  @param   mixed    $list  The iContact List ID
	 *
	 *  @return  boolean            
	 */
	public function subscribe($email, $params, $list)
	{
		$data = array('contact' => array_merge(array('email' => $email, 'status' => 'normal'), (array) $params));
		
		try 
		{
			$contact = $this->post($this->accountID .'/c/' . $this->clientFolderID . '/contacts', $data);
		}
		catch (Exception $e) 
		{
			throw $e;	
		}
		
		if ((isset($contact['contacts'])) && (is_array($contact['contacts'])) && (count($contact['contacts']) > 0)) 
		{
			$this->addToList($list, $contact['contacts'][0]['contactId']);
		}

		return true;
	}

	/**
	 *  Adds a contact to an iContact List
	 *
	 *  API REFERENCE
	 *  https://www.icontact.com/developerportal/documentation/subscriptions
	 *
	 *  @param  string  $listID     
	 *  @param  string  $contactID  
	 */
	public function addToList($listID, $contactID)
	{
		$data = array(
			array(
				'contactId' => $contactID,
				'listId' => $listID,
				'status' => 'normal'
				)
			);
		$this->post($this->accountID .'/c/' . $this->clientFolderID . '/subscriptions',$data);
	}

	/**
	 *  Returns all Client lists
	 *
	 *  API REFERENCE
	 *  https://www.icontact.com/developerportal/documentation/lists
	 *
	 *  @return  array
	 */
	public function getLists()
	{
		$data = $this->get($this->accountID .'/c/' . $this->clientFolderID . '/lists');

		if (!$this->success())
		{
			return;
		}

		$lists = array();

		if (!isset($data["lists"]) || !is_array($data["lists"]))
		{
			return $lists;
		}

		foreach ($data["lists"] as $key => $list)
		{
			$lists[] = array(
				'id'   => $list['listId'],
				'name' => $list['name']
			);
		}

		return $lists;
	}

	/**
	 * Get the last error returned by either the network transport, or by the API.
	 * If something didn't work, this should contain the string describing the problem.
	 * 
	 * @return  string  describing the error
	 */
	public function getLastError()
	{
		$body    = $this->last_response->body;
		$message = '';

		if (isset($body['errors']))
		{
			foreach ($body['errors'] as $error) {
				$message .= $error . ' ';
			}
		}

		return trim($message);
	}
}