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

use ConvertForms\Helper;

/**
 * Convert Forms form submit controller
 *
 * @since  2.7.5
 */
class ConvertFormsControllerField extends JControllerForm
{
    /**
     * Joomla Application Object
     *
     * @var object
     */
    protected $app;

    /**
     * The components parameters
     *
     * @var object
     */
    protected $params;

    /**
     *  Class Constructor
     *
     *  @param  string  $key  User API Key
     */
    public function __construct()
    {
        $this->app    = JFactory::getApplication();
        $this->params = Helper::getComponentParams();

        // Load component language file
        NRFramework\Functions::loadLanguage('com_convertforms');
        
		parent::__construct();
    }

    /**
     * The main submit method
     *
     * @return void
     */
    public function ajax()
    {  
        // Prevent AJAX response pollution by disabling PHP reporting notices.
        if (!$this->params->get('debug', false))
        {
            error_reporting(E_ALL & ~E_NOTICE);
        }

        $form_id    = $this->app->input->get('form_id');
        $field_key  = $this->app->input->get('field_key');
        $field_type = $this->app->input->get('field_type');

        $this->checkCSRFTokenOrDie($field_key, $form_id);

        $field_class = ConvertForms\FieldsHelper::getFieldClass($field_type);

        if (!method_exists($field_class, 'onAjax'))
        {
            return;
        }

        $response = $field_class->onAjax($form_id, $field_key);

        echo json_encode($response);

        // Stop execution
        jexit();
    }

    /**
     * Check for a CSRF Token only if the respective option is enabled.
     *
     * @return void
     */
    private function checkCSRFTokenOrDie($task, $form_id)
    {
        if (!$this->params->get('csrf_check', false))
        {
            return;
        }

        if (JSession::checkToken('request'))
        {
            return;
        }

        Helper::triggerError(JText::_('JINVALID_TOKEN'), $task, $form_id, $this->app->input->request->getArray());

        jexit(JText::_('JINVALID_TOKEN'));
    }
}