<?php

/**
 * @package         Convert Forms
 * @version         3.2.8 Free
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2020 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace ConvertForms;

defined('_JEXEC') or die('Restricted access');

use ConvertForms\Helper;

/**
 *  ConvertForms API Class
 */
class JsonApi
{
    /**
     *  Joomla Application Object
     *
     *  @var  object
     */
    private $app;

    /**
     *  API Key
     *
     *  @var  string
     */
    private $apikey;

    /**
     *  Class Constructor
     *
     *  @param  string  $key  User API Key
     */
    public function __construct($apikey)
    {
        if (!isset($apikey) || empty($apikey))
        {
            $this->throwError('API Key is missing');
        }

        $this->apikey = $apikey;

        if (!$this->authenticate())
        {
            $this->throwError('Invalid API Key: '. $this->apikey);
        }

        $this->app = \JFactory::getApplication();
    }

    /**
     *  Throws an exception and logs the error message to the log file
     *
     *  @param   string  $error  The error message
     *
     *  @return  void
     */
    private function throwError($error)
    {
        $error = 'ConvertForms API: ' . $error;
        
        Helper::log($error, 'error');
        throw new \Exception($error);
    }

    /**
     *  Authenticate API Call
     *
     *  @return  bool          
     */
    public function authenticate()
    {
        return $this->getSiteKey() === $this->apikey;
    }

    /**
     *  Returns Domain Key
     *
     *  @return  string
     */
    public static function generateDomainKey()
    {
        $parse  = parse_url(\JURI::root());
        $domain = isset($parse['host']) ? $parse['host'] : '-';
        $hash   = md5('CF' . $domain);

        return $hash;
    }

    /**
     *  Returns Domain Key
     *
     *  @return  string
     */
    public static function getSiteKey()
    {
        return Helper::getComponentParams()->get('api_key', null);
    }

    /**
     *  Route API endpoint to the proper method
     *
     *  @param   string  $endpoint  The API Endpoint Name
     *
     *  @return  string             Response Array
     */
    public function route($endpoint)
    {
        $endPointMethod = 'endPoint' . $endpoint;

        if (!method_exists($this, $endPointMethod))
        {
            $this->throwError('Endpoint not found:' . $endpoint);
        }

        return $this->$endPointMethod();
    }

    /**
     *  Loads and populates model
     *
     *  @param   JModel  &$model  
     *
     *  @return  JModel           
     */
    private function getModel($modelName)
    {
        if (!$modelName)
        {
            return;
        }

        $model = \JModelLegacy::getInstance($modelName, 'ConvertFormsModel', array('ignore_request' => true));

        $model->setState('list.limit',     $this->app->input->get('limit', 1000));
        $model->setState('list.start',     $this->app->input->get('start', 0));
        $model->setState('list.ordering',  $this->app->input->get('order', 'a.id'));
        $model->setState('list.direction', $this->app->input->get('dir', 'desc'));

        $state = $this->app->input->get('state', null);

        if (!is_null($state))
        {
            $model->setState('filter.state', $state);
        }

        return $model;
    }

    /**
     *  Backwards compatibility support for old Leads endpoint
     * 
     *  @deprecated 2.2.0
     * 
     *  @return  array  Database array
     */
    private function endPointLeads()
    {
        return $this->endPointSubmissions();
    }
    
    /**
     *  Submissions Endpoint
     * 
     *  @since 2.2.0
     * 
     *  @return  array  Database array
     */
    private function endPointSubmissions()
    {
        // Load Model
        $model = $this->getModel('Conversions');

        // Apply form filter
        $formID = $this->app->input->get('form', null);
        if (!is_null($formID))
        {
            $model->setState('filter.form_id', $formID);
        }

        // Apply campaign filter
        $campaignID = $this->app->input->get('campaign', null);
        if (!is_null($campaignID))
        {
            $model->setState('filter.campaign_id', $campaignID);
        }

        // Get Data
        $leads = $model->getItems();
        $data  = array();

        $tz = new \DateTimeZone($this->app->getCfg('offset', 'UTC'));

        foreach ($leads as $key => $lead)
        {
            // Convert created date to ISO8601 format to make Zapier happy.
            $date_ = new \JDate($lead->created, $tz);
            $lead->created = $date_->toISO8601(true);

            $data_ = array(
                'id'          => $lead->id,
                'state'       => $lead->state,
                'created'     => $lead->created,
                'form_id'     => $lead->form_id,
                'campaign_id' => $lead->campaign_id,
                'user_id'     => $lead->user_id,
                'visitor_id'  => $lead->visitor_id
            );

            // Include custom fields as well
            if (isset($lead->prepared_fields))
            {
                foreach ($lead->prepared_fields as $key => $field)
                {
                    // This is a temporary workaround for File Upload field, to display files as absolute URLs in array
                    // We can't use $field->value directly as it transforms the array to string.
                    if ($field->options->get('type') == 'fileupload')
                    {
                        $field->value_raw = array_filter(array_map('trim', explode(',', $field->value)));
                    }
    
                    $data_['field_' . $key] = $field->value_raw;

                    if ($field->value_raw !== $field->value_html)
                    {
                        $data_['field_' . $key . '.html'] = $field->value_html;
                    }
                }
            }

            $data[] = $data_;
        }

        return $data;
    }

    /**
     *  Forms Endpoint
     *
     *  @return  array  Database array
     */
    private function endPointForms()
    {
        return $this->endPointDefault('Forms');
    }

    /**
     *  Campaigns Endpoint
     *
     *  @return  array  Database array
     */
    private function endPointCampaigns()
    {
        return $this->endPointDefault('Campaigns');
    }

    /**
     *  Common Default Endpoint
     *
     *  @param   string  $model  ConvertForms model name
     *
     *  @return  array           
     */
    private function endPointDefault($model)
    {
        // Load Model
        $model = $this->getModel($model);

        if (!$model)
        {
            return;
        }

        // Get Data
        $items = $model->getItems();

        if (!$items)
        {
            return;
        }

        $data  = array();

        foreach ($items as $key => $item)
        {
            $data[] = array(
                'id'      => $item->id,
                'name'    => $item->name,
                'created' => $item->created,
                'state'   => $item->state
            );
        }

        return $data;
    }
}

?>