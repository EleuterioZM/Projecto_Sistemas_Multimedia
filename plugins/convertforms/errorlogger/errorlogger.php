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

use NRFramework\WebClient;
use ConvertForms\Form;

class plgConvertFormsErrorLogger extends JPlugin
{
    /**
     * Joomla Application Object
     *
     * @var object
     */
    protected $app;

    /**
     *  Add plugin fields to the form
     *
     *  @param   JForm   $form  
     *  @param   object  $data
     *
     *  @return  boolean
     */
    public function onConvertFormsError($error, $category, $form_id, $data = null)
    {
        // Only on front-end
        if ($this->app->isClient('administrator'))
        {
            return;
        }

        if (isset($data['skip_error_logger']))
        {
            return;
        }

        $user = JFactory::getUser();

        // Get form's name
        $form_data = Form::load($form_id);
        $form_name = isset($form_data['name']) ? $form_data['name'] : 'Unknown Form';
        $form_name .= ' (' . $form_id . ')';

$error_message = '

Identity
---------------------------------------------------------------------------
Date Time:          ' . JFactory::getDate() . '
Error Category:     ' . $category . '
Error message:      ' . $error . '
Form:               ' . $form_name . '
Session ID:         ' . JFactory::getSession()->getId() . '
IP Address:         ' . $this->app->input->server->get('REMOTE_ADDR') . '
User Agent:         ' . WebClient::getClient()->userAgent . '
Device:             ' . WebClient::getDeviceType() . '
Logged In Username: ' . $user->username . '
Logged In Name:     ' . $user->name . '

Data
---------------------------------------------------------------------------
' . print_r($data, true) . '

Request Headers
---------------------------------------------------------------------------
' . print_r($this->app->input->server->getArray(), true) . '
';

        try {
            JLog::add($error_message, JLog::ERROR, 'convertforms_errors');
        } catch (\Throwable $th) {
        }
    }
}