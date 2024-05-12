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

defined('_JEXEC') or die('Restricted access');

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

/**
 *  Convert Forms Plugin
 */
class PlgSystemConvertForms extends JPlugin
{
    /**
     *  Application Object
     *
     *  @var  object
     */
    protected $app;

    /**
     *  Component's param object
     *
     *  @var  JRegistry
     */
    private $param;

    /**
     *  The loaded indicator of helper
     *
     *  @var  boolean
     */
    private $init;

    /**
     *  Log Object
     *
     *  @var  Object
     */ 
    private $log;

    /**
     *  AJAX Response
     *
     *  @var  stdClass
     */
    private $response;

    /**
     *  Plugin constructor
     *
     *  @param  mixed   &$subject
     *  @param  array   $config
     */
    public function __construct(&$subject, $config = array())
    {
        $component = JComponentHelper::getComponent('com_convertforms', true);

        /**
         * Εxecute parent constructor early as $app is not available when
         * we uninstall the administrator component.
         */
        parent::__construct($subject, $config);

        if (!$component->enabled)
        {   
            return;
        }

        $this->param = $component->params;

        // Load required classes
        if (!$this->loadClasses())
        {
            return;
        }

        // Declare extension logger
        JLog::addLogger(
            array('text_file' => 'com_convertforms.php'),
            JLog::ALL, 
            array('com_convertforms')
        );
    }

    /**
     *  onAfterRoute Event
     */
    public function onAfterRoute()
    {
        // Get Helper
        if (!$this->getHelper())
        {
            return;
        }
    }

    /**
     *  Handles the content preparation event fired by Joomla!
     *
     *  @param   mixed     $context     Unused in this plugin.
     *  @param   stdClass  $article     An object containing the article being processed.
     *  @param   mixed     $params      Unused in this plugin.
     *  @param   int       $limitstart  Unused in this plugin.
     *
     *  @return  bool
     */
    public function onContentPrepare($context, &$article, &$params, $limitstart = 0)
    {
        // Get Helper
        if (!$this->getHelper())
        {
            return true;
        }

        // Check whether the plugin should process or not
        if (Joomla\String\StringHelper::strpos($article->text, 'convertforms') === false)
        {
            return true;
        }

        // Search for this tag in the content
        $regex = "#{convertforms(\s)(\d+)(\s?)(.*?)}#s";
        $article->text = preg_replace_callback($regex, array('self', 'process'), $article->text);
    }

    /**
     * Improve performance and prevent conflicts by not allowing modules to be rendered in the form builder.
     *
     * @param  array $modules The list of modules to be rendered in the page
     * 
     * @return void
     */
    public function onPrepareModuleList(&$modules)
    {
        if ($this->app->isClient('administrator') && $this->app->input->get('option') == 'com_convertforms' && $this->app->input->get('view') == 'form')
        {
            $modules = [];
        }
    }

    /**
     *  Callback to preg_replace_callback in the onContentPrepare event handler of this plugin.
     *
     *  @param   array   $match  A match to the {convertforms} plugin tag
     *
     *  @return  string  The processed result
     */
    private static function process($match)
    {
        $form_id = $match[2];
        $task = $match[4];

        switch ($task)
        {
            case 'submissions_total':
                return ConvertForms\Api::getFormSubmissionsTotal($form_id);
                break;
            
            default:
                return ConvertForms\Helper::renderFormById($form_id);
        }
    }

    /**
     *  Listens to AJAX requests on ?option=com_ajax&format=raw&plugin=convertforms
     *  Method aborts on invalid token or task
     *
     *  @return void
     */
    public function onAjaxConvertForms()
    {
        // Disable all PHP reporting to ensure a success AJAX response.
        $debug = ConvertForms\Helper::getComponentParams()->get('debug', false);
        if (!$debug)
        {
            error_reporting(E_ALL & ~E_NOTICE);
        }

        $input = $this->app->input;
        $form_id = isset($input->getArray()['cf']) ? $input->getArray()['cf']['form_id'] : 0;

        // Check if we have a valid task
        $task = $input->get('task', null);

        if (is_null($task))
        {
            die('Invalid task');
        }

        // An access token is required on all requests except on the API task which 
        // has a native authentication method through an API Key
        if (!in_array($task, ['api']) && !JSession::checkToken('request'))
        {
            ConvertForms\Helper::triggerError(JText::_('JINVALID_TOKEN'), $task, $form_id, $input->request->getArray());
            jexit(JText::_('JINVALID_TOKEN'));
        }

        // Cool access granted.
        $componentPath = JPATH_ADMINISTRATOR . '/components/com_convertforms/';
        JModelLegacy::addIncludePath($componentPath . 'models');
        JTable::addIncludePath($componentPath . 'tables');

        // Load component language file
        NRFramework\Functions::loadLanguage('com_convertforms');

        // Check if we have a valid method task
        $taskMethod = 'ajaxTask' . $task;

        if (!method_exists($this, $taskMethod))
        {
            die('Task not found');
        }

        // Success! Let's call the method.
        $this->response = new stdClass();

        try
        {
           $this->$taskMethod();
        }
        catch (Exception $e)
        {
            ConvertForms\Helper::triggerError($e->getMessage(), $task, $form_id, $input->request->getArray());
            $this->response->error = $e->getMessage();
        }

        echo json_encode($this->response);

        // Stop execution
        jexit();
    }

    # PRO-START
    /**
     *  AJAX Method to retrieve service account lists
     *  
     *  Listens to requests on ?option=com_ajax&format=raw&plugin=convertforms&task=lists
     *  Required arguments: service=[SERVICENAME]&key=[APIKEY/ACCESSTOKEN]
     *
     *  @return void
     */
    private function ajaxTaskLists()
    {
        $campaignData = $this->app->input->get('jform', null, 'array');

        if (is_null($campaignData) || empty($campaignData))
        {
            die('No Campaign Data Found');
        }

        // Yeah! We have a service! Dispatcher call the plugins please!
        JPluginHelper::importPlugin('convertforms');

        $lists = JFactory::getApplication()->triggerEvent('onConvertFormsServiceLists', array($campaignData));

        if (is_array($lists[0]))
        {
            $this->response->lists = $lists[0];
        } else 
        {
            $this->response->error = $lists[0];
        }
    }

    /**
     *  API Task help us query ConvertForms tables and output the result as JSON
     *
     *  @return  void
     */
    private function ajaxTaskAPI()
    {
        // Run only if API is enabled
        if (!ConvertForms\Helper::getComponentParams()->get('api', false))
        {
            ConvertForms\Helper::log('JSON-API is disabled. Enable it through ConvertForms configuration page.');
            die();
        }

        $endpoint = $this->app->input->get('endpoint', 'forms');
        $apikey   = $this->app->input->get('api_key');

        $api = new ConvertForms\JsonApi($apikey);

        $this->response = $api->route($endpoint);

        // JFactory::getDocument()->setMimeEncoding('application/json'); doesn't work here
        header('Content-Type: application/json');
    }

    # PRO-END
    
    /**
     *  Map onContentAfterSave event to onConvertFormsConversionAfterSave
     *  
     *  Content is passed by reference, but after the save, so no changes will be saved.
     *  Method is called right after the content is saved.
     *
     *  @param   string  $context  The context of the content passed to the plugin (added in 1.6)
     *  @param   object  $article  A JTableContent object
     *  @param   bool    $isNew    If the content has just been created
     *
     *  @return  void
     * 
     *  @deprecated Remove this code block and update the onConvertFormsConversionAfterSave() method to use onConvertFormsSubmissionAfterSave().
     */
    public function onContentAfterSave($context, $article, $isNew)
    {
        if ($context != 'com_convertforms.conversion' || $this->app->isClient('administrator'))
        {
            return;
        }

        JPluginHelper::importPlugin('convertforms');
        JPluginHelper::importPlugin('convertformstools');

        // Load item row
        $model = JModelLegacy::getInstance('Conversion', 'ConvertFormsModel', array('ignore_request' => true));
        if (!$conversion = $model->getItem($article->id))
        {
            return;
        }

        JFactory::getApplication()->triggerEvent('onConvertFormsConversionAfterSave', array($conversion, $model, $isNew));
    }

     /**
     *  Prepare form.
     *
     *  @param   JForm  $form  The form to be altered.
     *  @param   mixed  $data  The associated data for the form.
     *
     *  @return  boolean
     */
    public function onContentPrepareForm($form, $data)
    {
        // Return if we are in frontend
        if ($this->app->isClient('site'))
        {
            return true;
        }

        // Check we have a form
        if (!($form instanceof JForm))
        {
            return false;
        }

        // Check we have a valid form context
        $validForms = array(
            "com_convertforms.campaign",
            "com_convertforms.form"
        );

        if (!in_array($form->getName(), $validForms))
        {
            return true;
        }

        // Load ConvertForms plugins
        JPluginHelper::importPlugin('convertforms');
        JPluginHelper::importPlugin('convertformstools');

        // Campaign Forms
        if ($form->getName() == 'com_convertforms.campaign')
        {
            if (!isset($data->service) || !$service = $data->service)
            {
                return true;
            }
            
            $result = \JFactory::getApplication()->triggerEvent('onConvertFormsCampaignPrepareForm', [$form, $data, $service]);
        }

        // Form Editing Page
        if ($form->getName() == 'com_convertforms.form')
        {
            $result = \JFactory::getApplication()->triggerEvent('onConvertFormsFormPrepareForm', [$form, $data]);
        }

        return true;
    }

    /**
     *  Silent load of Convert Forms and Framework classes
     *
     *  @return  boolean
     */
    private function loadClasses()
    {
        // Initialize Convert Forms Library
        if (!@include_once(JPATH_ADMINISTRATOR . '/components/com_convertforms/autoload.php'))
        {
            return false;
        }

        // Load Framework
        if (!@include_once(JPATH_PLUGINS . '/system/nrframework/autoload.php'))
        {
            return false;
        }

        // Declare extension's error log file
        JLog::addLogger(
            [
                'text_file' => 'convertforms_errors.php',
                'text_entry_format' => '{MESSAGE}'
            ], 
            JLog::ERROR, 
            ['convertforms_errors']
        );

        return true;
    }

    /**
     *  Loads the helper classes of plugin
     *
     *  @return  bool
     */
    private function getHelper()
    {
        // Return if is helper is already loaded
        if ($this->init)    
        {
            return true;
        }

        // Return if we are not in frontend
        if (!$this->app->isClient('site'))
        {
            return false;
        }

        // Handle the component execution when the tmpl request paramter is overriden
        if (!$this->param->get("executeoutputoverride", false) && $this->app->input->get('tmpl', null, "cmd") != null)
        {
            return false;
        }

        // Handle the component execution when the format request paramter is overriden
        if (!$this->param->get("executeonformat", false) && $this->app->input->get('format', "html", "cmd") != "html")
        {
            return false;
        }

        // Return if document type is Feed
        if (NRFramework\Functions::isFeed())
        {
            return false;
        }

        // Load language
        JFactory::getLanguage()->load('com_convertforms', JPATH_ADMINISTRATOR . '/components/com_convertforms');

        return ($this->init = true);
    }
}
