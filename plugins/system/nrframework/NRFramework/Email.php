<?php 

/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2022 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace NRFramework;

defined('_JEXEC') or die('Restricted access');

use NRFramework\Functions;
use NRFramework\URLHelper;

/**
 *  Novarain Framework Emailer
 */
class Email
{
    /**
     *  Indicates the last error
     *
     *  @var  string
     */
    public $error;

    /**
     *  Email Object
     *
     *  @var  email data to be sent
     */
    private $email;

    /**
     *  Required elements for a valid email object
     *
     *  @var  array
     */
    private $requiredKeys = [
        'from_email',
        'from_name',
        'recipient',
        'subject',
        'body'
    ];

    /**
     *  Class constructor
     */
    public function __construct($email)
    {
        $this->email = $email;
    }

    /**
     *  Validates Email Object
     *
     *  @param   array  $email  The email object
     *
     *  @return  boolean        Returns true if the email object is valid
     */
    public function validate()
    {
        // Validate email object
        if (!$this->email || !is_array($this->email) || !count($this->email))
        {
            $this->setError('Invalid email object.');
            return;
        }

        // Check for missing properties
        foreach ($this->requiredKeys as $key)
        {
            if (!isset($this->email[$key]) || empty($this->email[$key]))
            {
                $this->setError("The $key field is either missing or invalid.");
                return;
            }
        }

        // Validate recipient email addresses.
        $this->email['recipient'] = Functions::makeArray($this->email['recipient']);

        foreach ($this->email['recipient'] as $recipient)
        {
            if (!$this->validateEmailAddress($recipient))
            {
                $this->setError("Invalid recipient email address: $recipient");
                return;
            }
        }

        // Validate sender email address
        if (!$this->validateEmailAddress($this->email['from_email']))
        {
            $this->setError('Invalid sender email address: ' . $this->email['from_email']);
            return;
        }

        $this->email['bcc'] = isset($this->email['bcc']) ? Functions::makeArray($this->email['bcc']) : [];
        $this->email['cc']  = isset($this->email['cc']) ? Functions::makeArray($this->email['cc']) : [];

        // Convert special HTML entities back to characters on non text-only properties.
        // For instance, the subject line of an email is not parsed as HTML, it's just pure text. 
        // Because of this an HTML entity like &amp; it will be displayed as encoded.
        // To prevent this from happening we need decode the values.
        $this->email['subject']       = htmlspecialchars_decode($this->email['subject']);
        $this->email['from_name']     = htmlspecialchars_decode($this->email['from_name']);
        $this->email['reply_to_name'] = htmlspecialchars_decode($this->email['reply_to_name']);

        return true;
    }

    /**
     *  Sending emails
     *
     *  @param   array  $email  The mail objecta
     *
     *  @return  mixed          Returns true on success. Throws exeption on fail.
     */
    public function send()
    {
        // Proceed only if Mail Sending is enabled.
        if (!\JFactory::getConfig()->get('mailonline'))
        {
            $this->error = \JText::_('NR_ERROR_EMAIL_IS_DISABLED');
            return;
        }

        // Validate first the email object
        if (!$this->validate($this->email))
        {
            return;
        }

        $email  = $this->email;
        $mailer = \JFactory::getMailer();
        $mailer->CharSet = 'UTF-8';

        // Email Sender
        $mailer->setSender([
            $email['from_email'],
            $email['from_name']
        ]);

        // Reply-to
        if (isset($email['reply_to']) && !empty($email['reply_to']))
        {
            $name = (isset($email['reply_to_name']) && !empty($email['reply_to_name'])) ? $email['reply_to_name'] : '';
            $mailer->addReplyTo($email['reply_to'], $name);
        }

        // Convert all relative paths found in <a> and <img> elements to absolute URLs
        $email['body'] = URLHelper::relativePathsToAbsoluteURLs($email['body']);

        // Fix space characters displayed as ???? in old email clients like SquirrelMail.
        // Ticket reference: https://smilemotive.teamwork.com/desk/tickets/96313487/messages
        $specialSpace = [
            "\xC2\xA0",
            "\xE1\xA0\x8E",
            "\xE2\x80\x80",
            "\xE2\x80\x81",
            "\xE2\x80\x82",
            "\xE2\x80\x83",
            "\xE2\x80\x84",
            "\xE2\x80\x85",
            "\xE2\x80\x86",
            "\xE2\x80\x87",
            "\xE2\x80\x88",
            "\xE2\x80\x89",
            "\xE2\x80\x8A",
            "\xE2\x80\x8B",
            "\xE2\x80\xAF",
            "\xE2\x81\x9F",
            "\xEF\xBB\xBF",
        ];

        $email['body'] = str_replace($specialSpace, " ", $email['body']);

        $mailer
            ->addRecipient($email['recipient'])
            ->isHTML(true)
            ->setSubject($email['subject'])
            ->setBody($email['body']);

        $mailer->AltBody = strip_tags(str_ireplace(['<br />', '<br>', '<br/>'], "\r\n", $email['body']));

        // Add CC
        if (!empty($email['cc']))
        {
            $mailer->addCc($email['cc']);
        }

        // Add BCC
        if (!empty($email['bcc']))
        {
            $mailer->addBcc($email['bcc']);
        }

        // Attachments
        if (!empty($email['attachments']))
        {
            if (!is_array($email['attachments']))
            {
                $attachments = explode(',', $email['attachments']);
            }

            foreach ($attachments as $attachment)
            {
                $file_path = $this->toRelativePath($attachment);
                $mailer->addAttachment($file_path);
            }
        }

        // Send mail
        $send = $mailer->Send();
        
        if ($send !== true)
        {
            $this->setError($send->__toString());
            return;
        }

        return true;
    }

    /**
     *  Set Class Error
     *
     *  @param  string   $error   The error message
     */
    private function setError($error)
    {
        $this->error = 'Error sending email: ' . $error;
        Functions::log($error);
    }

    /**
     *  Removes all illegal characters and validates an email address
     *
     *  @param   string  $email  Email address string
     *
     *  @return  bool
     */
    private function validateEmailAddress($email)
    {
		// If the email address contains an ampersand, throw an error
		if (strpos($email, '&') !== false)
		{
			return false;
		}

        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    /**
     * Attempts to transform an absolute URL to path relative to the site's root.
     *
     * @param  string $url
     *
     * @return string
     */
    private function toRelativePath($url)
    {
        $needles = [
            \JURI::root(),
            JPATH_SITE,
            JPATH_ROOT
        ];

        $path = str_replace($needles, '', $url);

        $path = \JPath::clean($path);

        return $path;
    }
}