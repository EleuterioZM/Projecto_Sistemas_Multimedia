<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_contact
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use ConvertForms\Helper;

/**
 * Controller for single contact view
 *
 * @since  1.5.19
 */
class ConvertFormsControllerCron extends JControllerForm
{
    /**
     * Joomla Application Object
     * 
     * Joomla 4 requires this to be protected or weaker.
     *
     * @var object
     */
    protected $app;

    /**
     * The secret key configured in the configuration page
     *
     * @var string
     */
    private $secret;

	/**
     *  Class Constructor
     *
     *  @param  string  $key  User API Key
     */
    public function __construct()
    {
        // Register a new generic Convert Forms CRON logger
        \JLog::addLogger(['text_file' => 'convertforms_cron.php'], \JLog::ALL, ['convertforms.cron']);

        $this->app    = \JFactory::getApplication();
        $this->secret = Helper::getComponentParams()->get('api_key');
        
		parent::__construct();
    }

    /**
     * Start the cron task
     *
     * @return void
     */
    public function cron()
    {
        $this->log('Starting CRON job', \JLog::DEBUG);

        // Makes sure SiteGround's SuperCache doesn't cache the CRON view
        $this->app->setHeader('X-Cache-Control', 'False', true);

        if (empty($this->secret))
        {
            $this->log('No secret key configured', \JLog::ERROR);
			header('HTTP/1.1 503 Service unavailable due to configuration');
            jexit();
        }

        // Authenticate request
        if ($this->app->input->get('secret', null, 'raw') != $this->secret)
        {
            $this->log('Wrong secret key provided in URL', \JLog::ERROR);
			header('HTTP/1.1 403 Forbidden');
            jexit();
        }

        // Validate command to run
        $command        = $this->app->input->get('command', null, 'raw');
        $command        = trim(strtolower($command));
        $commandEscaped = \JFilterInput::getInstance()->clean($command, 'cmd');

        if (empty($command))
        {
            $this->log('No command provided in URL', \JLog::ERROR);
			header('HTTP/1.1 501 Not implemented');
            jexit();
        }

        // Register a new task-specific Convert Forms CRON logger
        \JLog::addLogger(['text_file' => "convertforms_cron_$commandEscaped.php"], \JLog::ALL, ['convertforms.cron.' . $command]);
        $this->log("Starting execution of command $commandEscaped", \JLog::DEBUG);

        // Import plugins and trigger the cron task event
        \JPluginHelper::importPlugin('system');
        \JPluginHelper::importPlugin('convertforms');
        \JPluginHelper::importPlugin('convertformstools');
        $this->app->triggerEvent('onConvertFormsCronTask', [$command, ['time_limit' => 10]]);

        $this->log("Finished running command $commandEscaped", \JLog::DEBUG);
        
        echo $commandEscaped . ' OK';
        jexit();
    }

    /**
     * Log message to the default log file
     *
     * @param string $msg
     * @param object $type
     *
     * @return void
     */
    private function log($msg, $type)
    {
        try {
            \JLog::add($msg, $type, 'convertforms.cron');
        } catch (\Throwable $th) {
        }
    }
}
