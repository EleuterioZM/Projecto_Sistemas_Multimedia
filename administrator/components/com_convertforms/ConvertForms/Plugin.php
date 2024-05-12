<?php

/**
 * @package         Convert Forms
 * @version         3.2.8 Free
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace ConvertForms;

defined('_JEXEC') or die('Restricted access');

use ConvertForms\Helper;

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

/**
 *  Services main class used by ConvertForms plugins
 */
class Plugin extends \JPlugin
{
    /**
     *  Application Object
     *
     *  @var  object
     */
    protected $app;

    /**
     *  Wrappers Directory
     *
     *  @var  string
     */
    private $wrappersDir = '/system/nrframework/helpers/wrappers/';

    /**
     *  Lead row to manipulate
     *
     *  @var  object
     */
    protected $lead;
    
    /**
     *  Auto loads the plugin language file
     *
     *  @var  boolean
     */
    protected $autoloadLanguage = true;

    /**
     *  The campaign data.
     * 
     *  @var  array
     */
    protected $campaignData;

    /**
     *  Method to retrieve available lists/campaigns from API
     *
     *  @param   string  $campaignData  The Campaign's Data
     *
     *  @return  mixed                  Array on success, Throws an exception on fail
     */
    public function getLists($campaignData)
    {
        $integration = $this->getCampaignIntegration($campaignData);

        $api = new $integration($campaignData);

        if (!method_exists($api, 'getLists'))
        {
            throw new \Exception('Method getLists() is missing from the ' . $this->getName() . ' wrapper');
        }

        $lists = $api->getLists();

        if (!$api->success())
        {
            throw new \Exception($api->getLastError());
        }

        return $lists;
    }

    /**
     * Returns the campaign integration.
     * 
     * @param   array   $campaignData
     * 
     * @return  string
     */
    protected function getCampaignIntegration($campaignData)
    {
        $class = str_replace('plgConvertForms', '', get_class($this));
        
        return '\NRFramework\Integrations\\' . $class;
    }

    /**
     *  Event ServiceName - Returns the service information
     *
     *  @return  array
     */
    public function onConvertFormsServiceName()
    {
        $service = array(
            'name'  => \JText::_('PLG_CONVERTFORMS_' . strtoupper($this->getName()) . '_ALIAS'),
            'alias' => $this->getName()
        );

        return $service;
    }

    /**
     *  Appends form.xml to Campaign editing form
     *
     *  @param   JForm   $form    The form to be altered.
     *  @param   mixed   $data    The associated data for the form.
     *  @param   string  $string  The associated service name.
     *
     *  @return  boolean
     */
    public function onConvertFormsCampaignPrepareForm($form, $data, $service)
    {
        if ($service != $this->getName())
        {
            return true;
        }

        // Try to load service form
        try
        {
            $form->loadFile($this->getForm(), false);
            $form->addFieldPath(JPATH_COMPONENT_ADMINISTRATOR . '/models/forms/fields');
        }
        catch (Exception $e)
        {
            $this->app->enqueueMessage($e->getMessage(), 'error');
        }

        return true;
    }

    /**
     *  Event that gets triggered whenever we want to retrieve service's account list 
     *
     *  @param   array  $campaignData      All the Campaign Data
     *
     *  @return  array                     An array with all available lists
     */
    public function onConvertFormsServiceLists($campaignData)
    {
        // Proceed only if we have a valid service
        if ($campaignData['service'] != $this->getName())
        {
            return;
        }

        // Load service wrapper
        $this->loadWrapper();
    
        // Try to get service's account lists
        try
        {
            return $this->getLists($campaignData);
        }
        // Catch any exception 
        catch (Exception $e)
        {
            Helper::log($e->getMessage(), 'error');
            return $e->getMessage();
        }
    }

    /**
     *  Syncs conversion data with the assosiated third-party service.
     *  A conversion is assosiated with a Form who has a Campaign who has a Service
     *  Sync is skipped if the service is empty.
     *  
     *  Content is passed by reference, but after the save, so no changes will be saved.
     *  Method is called right after the content is saved.
     * 
     *  @param   string  $conversion  The Conversion data
     *  @param   bool    $model       The Conversions Model
     *  @param   bool    $isNew       If the Conversion has just been created
     * 
     *  @return  void
     * 
     *  @todo Use onConvertFormsSubmissionAfterSave() event instead.
     */
    public function onConvertFormsConversionAfterSave($conversion, $model, $isNew)
    {
        // Proceed only if we have a valid service
        if ($conversion->campaign->service != $this->getName())
        {
            return;
        }

        // Validate Lead
        $this->lead = clone $conversion;
        $this->validateLead();

        // Load service wrapper
        $this->loadWrapper();

        // Load Lead row for update
        $table = $model->getTable();
        $table->load($conversion->id);

        $params = json_decode($table->params);

        if (!is_object($params))
        {
            $params = new stdClass();
        }

        $params->sync_service = $conversion->campaign->service;

        // Try to sync the Lead with the assosiated 3rd party service
        try
        {
            $this->subscribe($conversion);

            // Success. Update the Lead record.
            unset($params->sync_error);

            $table->params = json_encode($params);
            $table->store();

            // Log debug message
            Helper::log('Lead #' . $conversion->id . ' successfully synched with ' . $params->sync_service);
        }

        // Catch any exception and save it to the Lead row.
        // Then re-throw the same exception in order to be used by the AJAX handler.
        catch (\Exception $e)
        {
            $params->sync_error = $e->getMessage();
            $table->params = json_encode($params);
            $table->state = 0;
            $table->store();

            // Log error message
            Helper::log('Error syncing lead #' . $conversion->id . ' with ' . $params->sync_service . " - " . $e->getMessage(), 'error');
            // Re-throw the exception
            throw new \Exception($e->getMessage());
        }
    }

    /**
     *  Validate lead and make sure there is at least 1 email field
     *
     *  @return  void
     */
    protected function validateLead()
    {
        if (!isset($this->lead->params) || !is_array($this->lead->params))
        {
            throw new \Exception(\JText::_('COM_CONVERTFORMS_INVALID_LEAD'));
        }

        // First, try to find a field with a name set to 'email'.
        foreach($this->lead->params as $key => $value)
        {
            if (strtolower($key) != 'email')
            {
                continue;
            }

            // Email field found!
            $this->lead->email = $value;

            // Remove the parameter in order to avoid sending the email value twice
            unset($this->lead->params[$key]);
        }

        // If no email field found, make a second attempt to find an email field by type
        if (!isset($this->lead->email) || empty($this->lead->email))
        {
            if (isset($this->lead->form->fields) && is_array($this->lead->form->fields))
            {
                foreach ($this->lead->form->fields as $key => $field)
                {
                    if ($field['type'] != 'email')
                    {
                        continue;
                    }

                    if (!isset($this->lead->params[$field['name']]))
                    {
                        continue;
                    }

                    // Email field found!
                    $this->lead->email = $this->lead->params[$field['name']];
                    unset($this->lead->params[$field['name']]);
                }          
            }
        }

        // Make sure now we have found an email field
        if (!isset($this->lead->email) || empty($this->lead->email))
        {
            throw new \Exception(\JText::_('COM_CONVERTFORMS_FORM_IS_MISSING_THE_EMAIL_FIELD'));
        }
    }

    /**
     *  Loads Service Wrapper
     *
     *  @return  boolean
     */
    protected function loadWrapper()
    {
        $wrapper = $this->getWrapperFile();

    	if (!\JFile::exists($wrapper))
    	{
            throw new \Exception('Wrapper ' . $wrapper .  ' not found');
    	}

    	return include_once($wrapper);
    }

    /**
     *  Returns Service Wrapper File
     *
     *  @return  string
     */
    protected function getWrapperFile()
    {
        return JPATH_PLUGINS . $this->wrappersDir . $this->getName() . '.php';
    }

    /**
     *  Returns form.xml file path
     *
     *  @return  string
     */
    private function getForm()
    {
        $xml = JPATH_PLUGINS . '/convertforms/' . $this->getName() . '/form.xml';

        if (!\JFile::exists($xml))
        {
            throw new \Exception('XML file is missing: ' . $xml);
        }

        return $xml;
    }

    /**
     *  Insensitive search for array key
     *
     *  @param   string  $name   The array key to search for
     *  @param   array   $array  The array
     *
     *  @return  mixed           False if not found, string if found
     */
    public function findKey($name, $array)
    {
        $result = false;

        foreach ($array as $key => $value)
        {
            if (strtolower($key) == $name)
            {
                $result = $value;
                break;
            }
        }

        return $result;
    }

    /**
     *  Get plugin name alias
     *
     *  @return  string
     */
    public function getName()
    {
        return isset($this->name) ? $this->name : $this->_name;
    }
}