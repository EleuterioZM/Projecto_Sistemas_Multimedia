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

class plgConvertFormsEmails extends JPlugin
{
    /**
     * Form Object
     *
     * @var object
     */
    private $form;

    /**
     *  Auto loads the plugin language file
     *
     *  @var  boolean
     */
    protected $autoloadLanguage = true;

    /**
     *  Add plugin fields to the form
     *
     *  @param   JForm   $form  
     *  @param   object  $data
     *
     *  @return  boolean
     */
    public function onConvertFormsFormPrepareForm($form, $data)
    {
        $form->loadFile(__DIR__ . '/form/form.xml', false);
        return true;
    }

    /**
     * Event triggered during fieldset rendering in the form editing page in the backend.
     *
     * @param string $fieldset_name The name of the fieldset is going to be rendered
     * @param string $fieldset      The HTML output of the fieldset
     *
     * @return void
     */
    public function onConvertFormsBackendFormPrepareFieldset($fieldset_name, &$fieldset)
    {
        if ($this->_name != $fieldset_name)
        {
            return;
        }

        // Proceed only if Mail Sending is disabled.
        if ((bool) \JFactory::getConfig()->get('mailonline'))
        {
            return;
        }

        $warning = '
            <div class="alert alert-error">
                <span class="icon-warning"></span>' .
                \JText::_('PLG_CONVERTFORMS_EMAILS_ERROR_MAIL_SENDING_DISABLED') . '
            </div>';

        $fieldset = $warning . $fieldset;
    }
    
	/**
	 *  Create the final credentials with the auth code
	 *
	 *  @param   string  $context  The context of the content passed to the plugin (added in 1.6)
	 *  @param   object  $article  A JTableContent object
	 *  @param   bool    $isNew    If the content has just been created
	 *
	 *  @return  boolean
	 */
	public function onContentBeforeSave($context, $form, $isNew)
	{
		if ($context != 'com_convertforms.form')
		{
			return;
        }

        if (!is_object($form) || !isset($form->params))
        {
            return;
        }

        $params = json_decode($form->params);

        if (!isset($params->emails))
        {
            return true;
        }

        // Proceed only if Send Notifications option is enabled
        if ($params->sendnotifications != '1')
        {
            return true;
        }

        $this->form = clone $form;
        $this->form->params = $params;

        foreach ($params->emails as $key => $email)
        {
            $keyToID = ((int) str_replace('emails', '', $key)) + 1;
            $error = JText::_('COM_CONVERTFORMS_EMAILS') . ' #' . $keyToID . ' - ';

            $options = [
                'recipient'     => ['COM_CONVERTFORMS_EMAILS_RECIPIENT', true, true],
                'subject'       => ['COM_CONVERTFORMS_EMAILS_SUBJECT', false, true],
                'from_name'     => ['COM_CONVERTFORMS_EMAILS_FROM', false, true],
                'from_email'    => ['COM_CONVERTFORMS_EMAILS_FROM_EMAIL', true, true],
                'reply_to'      => ['COM_CONVERTFORMS_EMAILS_REPLY_TO', true, false],
                'reply_to_name' => ['COM_CONVERTFORMS_EMAILS_REPLY_TO_NAME', false, false],
                'body'          => ['COM_CONVERTFORMS_EMAILS_BODY', false, true],
                'attachments'   => ['COM_CONVERTFORMS_EMAILS_ATTACHMENT', false, false]
            ];

            foreach ($options as $key => $option)
            {
                $acceptsCommaSeparatedValues = $option[1];
                $optionValues = $acceptsCommaSeparatedValues ? explode(',', $email->$key) : (array) $email->$key;

                foreach ($optionValues as $optionValue)
                {
                    $result = $this->validateOption($optionValue, $option[1], $option[2]);

                    if (is_string($result))
                    {
                        $form->setError($error . JText::_($option[0]) . ' - ' . $result);
                        return false;
                        break;
                    }
                }
            }
        }

        return true;
    }

    /**
     * Validates string as an Email Notification option.
     *
     * @param   string  $string            The option name as found in the xml file
     * @param   bool    $validateAsEmail   If enabled, the option should be validated as an Email Address
     * @param   bool    $required          If enabled, string should not be left blank
     *
     * @return  void
     */
    private function validateOption($string, $validateAsEmail = true, $required = true)
    {
        // Check if it's empty
        if ($required && (empty($string) || is_null($string)))
        {
            return JText::sprintf('PLG_CONVERTFORMS_EMAILS_ERROR_BLANK', $string);
        }

        $string = trim($string);

        // Check if has a valid field-based Smart Tag in the form: {field.field-name}
        $pattern = "#\{field.([^{}]*)\}#s";
        preg_match_all($pattern, $string, $result);

        if (!empty($result[1]) && count($result[1]) > 0)
        {
            foreach ($result[1] as $key => $match)
            {
                // Keep only the actual field name
                list($field_name, $options) = explode('--', $result[1][$key]) + ['', ''];
                
                if (!$this->formHasField(trim($field_name)))
                {
                    return JText::sprintf('PLG_CONVERTFORMS_EMAILS_ERROR_UNKNOWN_SMART_TAG', $result[0][$key]);
                    break;
                }
            }

            return true;
        }

        // Check if has a valid Email Address info@mail.com
        if ($validateAsEmail && !empty($string))
        {
            // Check common email-based Smart Tags
            if (in_array($string, ['{user.email}', '{site.email}']))
            {
                return true;
            }

            if (!ConvertForms\Validate::email($string))
            {
                return JText::sprintf('PLG_CONVERTFORMS_EMAILS_ERROR_INVALID_EMAIL_ADDRESS', $string);
            }
        }

        return true;
    }

    /**
     * Check if given name exists as a form field
     *
     * @param string $name
     *
     * @return bool
     */
    private function formHasField($name)
    {
        $name = strtolower($name);

        foreach ($this->form->params->fields as $field)
        {
            if (!isset($field->name))
            {
                continue;
            }

            if (strtolower($field->name) == $name)
            {
                return true;
            }

            // In case a sub Smart Tag is being used. Eg: {field.dropdown.label} to get dropdown's selected text.
            if (stripos($name, $field->name . '.') !== false)
            {
                return true;
            }
        }

        return false;
    }

    /**
     *  Content is passed by reference, but after the save, so no changes will be saved.
     * 
     *  @param   string  $submission    The submission information object
     * 
     *  @return  void
     */
    public function onConvertFormsSubmissionAfterSave($submission)
    {
        if (!isset($submission->form->sendnotifications) || !$submission->form->sendnotifications)
        {
            return;
        }

        if (!isset($submission->form->emails) || !is_array($submission->form->emails))
        {
            return;
        }

        // Send email queue
        foreach ($submission->form->emails as $key => $email)
        {
            // Trigger Content Plugins
            $email['body'] = \JHtml::_('content.prepare', $email['body']);

            // Replace {variables}
            $email = ConvertForms\SmartTags::replace($email, $submission);

            // Send mail
            $mailer = new NRFramework\Email($email);
            
            if (!$mailer->send())
            {
                throw new \Exception($mailer->error);
            }
        }
    }   
}