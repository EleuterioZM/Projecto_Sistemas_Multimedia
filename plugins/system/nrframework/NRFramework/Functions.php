<?php 

/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace NRFramework;

defined('_JEXEC') or die;

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

use Joomla\Registry\Registry;
use \NRFramework\Cache;
use Joomla\CMS\Factory;
use Joomla\CMS\Date\Date;

class Functions
{
    /**
     * Add HTML before the closing </body> tag.
     *
     * @param   string  $html   The HTML to prepend to </body>
     * @param   boolean $once   If true, the HTML will be added only once.
     * 
     * @return  void
     */
    public static function appendToBody($html, $once = false)
    {
        if ($once)
        {
            $hash = md5($html);

            if (Cache::has($hash))
            {
                return;
            }

            Cache::set($hash, true);
        }

        $app = \JFactory::getApplication();

        $app->registerEvent('onAfterRender', function() use ($app, $html)
        {
            $buffer = $app->getBody();

            $closingTag = '</body>';
    
            if (strpos($buffer, $closingTag))
            {
                // If </body> exists prepend the given HTML
                $buffer = str_replace($closingTag, $html . $closingTag, $buffer);
            } else 
            {
                // If </body> does not exist append to document's end
                $buffer .= $html;
            }
            
            $app->setBody($buffer);
        });
    }

    /**
     * Fix arrays, remove duplicate items, null items and whitespace around item values.
     *
     * @param  array $subject
     * 
     * @return array    The new cleaned array
     */
    public static function cleanArray($subject)
    {
        if (!is_array($subject))
        {
            return $subject;
        }

        $subject = array_unique($subject);
        $subject = array_map('trim', $subject);

        // Remove empty items. We use a custom callback here because the default behavior of array_filter removes 0 values as well.
        $subject = array_filter($subject, function($value)
        {
            return ($value !== null && $value !== false && $value !== ''); 
        });

        return $subject;
    }

    /**
     *  Attempt to convert a subject to array
     *
     *  @param  mixed  $subject
     * 
     *  @return array
     */
    public static function makeArray($subject)
    {
        if (empty($subject))
        {
            return [];
        }

        if (is_object($subject))
        {
            return (array) $subject;
        }

        if (!is_array($subject))
        {
            // replace newlines with commas
            $subject = str_replace(PHP_EOL, ',', $subject);
    
            // split keywords on commas
            $subject = explode(',', $subject);
        }

        // Now that we have an array, run some housekeeping.
        $arr = $subject;

        $arr = self::cleanArray($arr);

        // Reset keys
        $arr = array_values($arr);

        return $arr;
    }

    /**
     * Return the real site base URL by ignoring the live_site configuration option.
     *
     * @param  bool $ignore_admin   If enabled and we are browsing administrator, we will get the front-end site root URL.
     *
     * @return string
     */
    public static function getRootURL($ignore_admin = true) 
    {
        $factory = \JFactory::getConfig();

        // Store the original live_site value
        $live_site_original = $factory->get('live_site', '');

        // If we live_site is not set, do not proceed further. Return the default website base URL.
        if (empty($live_site_original))
        {
            return $ignore_admin ? \JURI::root() : \JURI::base();
        }

        // Remove the live site
        $factory->set('live_site', '');

        // Remove all cached JURI instances
        \JURI::reset();

        // Get a new URL. The live_site option should be ignored.
        $base_url = $ignore_admin ? \JURI::root() : \JURI::base();

        // Set back the original live_site
        $factory->set('live_site', $live_site_original);
        \JURI::reset();

        return $base_url;
    }

    /**
     * Insert an associative array into a specific position in an array
     *
     * @param $original array 	The original array to add to
     * @param $new array 		The new array of values to insert into the original
     * @param $offset int 		The position in the array ( 0 index ) where the new array should go
     *
     * @return array 		The new combined array
     */
    public static function array_splice_assoc($original,$new,$offset)
    {
        return array_slice($original, 0, $offset, true) + $new + array_slice($original, $offset, NULL, true);  
    }

    public static function renderField($fieldname)
    {
        $fieldname = strtolower($fieldname);

		require_once JPATH_PLUGINS . '/system/nrframework/fields/' . $fieldname . '.php';

        $classname = '\JFormField' . $fieldname;

		$field = new $classname();

        $element = new \SimpleXMLElement('
            <field name="' . $classname . '" type="' . $classname . '"

			/>');
			
        $field->setup($element, null);
        
        return $field->__get('input');
    }

	/**
	 *  Checks if an array of values (needle) exists in a text (haystack)
	 *
	 *  @param   array   $needle            The searched array of values.
	 *  @param   string  $haystack          The text
	 *  @param   bool    $case_insensitive  Indicates whether the letter case plays any role
	 *
	 *  @return  bool
	 */
	public static function strpos_arr($needles, $haystack, $case_insensitive = false)
	{
        $needles = !is_array($needles) ? (array) $needles : $needles;
        $haystack = $case_insensitive ? strtolower($haystack) : $haystack;

		foreach ($needles as $needle)
		{
            $needle = $case_insensitive ? strtolower($needle) : $needle;

			if (strpos($haystack, $needle) !== false) 
			{
				// stop on first true result
				return true; 
			}
		}

		return false;
	}

    /**
     *  Log message to framework's log file
     *
     *  @param   mixed  $data    Log message
     *
     *  @return  void
     */
    public static function log($data)
    {
        $data = (is_object($data) || is_array($data)) ? print_r($data, true) : $data;

        try {
            \JLog::add($data, \JLog::DEBUG, 'nrframework');
        } catch (\Throwable $th) {
        }
    }

    /**
     *  Return's a URL with the Google Analytics Campaign Parameters appended to the end
     *
     *  @param   string  $url       The URL
     *  @param   string  $medium    Campaign Medium
     *  @param   string  $campaign  Campaign Name
     *
     *  @return  string
     */
    public static function getUTMURL($url, $medium = 'upgradebutton', $campaign = 'freeversion')
    {
        if (!$url)
        {
            return;
        }

        $utm  = 'utm_source=CustomerBackend&utm_medium=' . $medium . '&utm_campaign=' . $campaign;
        $char = strpos($url, '?') === false ? '?' : '&';

        return $url . $char . $utm;
    }

    /**
     *  Returns user's Download Key
     *
     *  @return  string
     */
    public static function getDownloadKey()
    {
        $class = new Updatesites();
        return $class->getDownloadKey();
    }

    /**
     *  Adds a script or a stylesheet to the document
     *
     *  @param  Mixed    $files           The files to be to added to the document
     *  @param  boolean  $appendVersion   Adds file versioning based on extension's version
     *
     *  @return void
     */
    public static function addMedia($files, $extension = "plg_system_nrframework", $appendVersion = true)
    {
        $doc       = \JFactory::getDocument();
        $version   = self::getExtensionVersion($extension);
        $mediaPath = \JURI::root(true) . "/media/" . $extension;

        if (!is_array($files))
        {
            $files = array($files);
        }

        foreach ($files as $key => $file)
        {
            $fileExt  = \JFile::getExt($file);
            $filename = $mediaPath . "/" . $fileExt . "/" . $file;
            $filename = ($appendVersion) ? $filename . "?v=" . $version : $filename;

            if ($fileExt == "js")
            {
                $doc->addScript($filename);
            }

            if ($fileExt == "css")
            {
                $doc->addStylesheet($filename);
            }
        }
    }

    /**
     *  Get the Framework version
     *
     *  @return  string  The framework version
     */
    public static function getVersion()
    {
        return self::getExtensionVersion("plg_system_nrframework");
    }

    /**
     *  Checks if document is a feed document (xml, rss, atom)
     *
     *  @return  boolean
     */
    public static function isFeed()
    {
        return (
            \JFactory::getDocument()->getType() == 'feed'
            || \JFactory::getDocument()->getType() == 'xml'
            || \JFactory::getApplication()->input->getWord('format') == 'feed'
            || \JFactory::getApplication()->input->getWord('type') == 'rss'
            || \JFactory::getApplication()->input->getWord('type') == 'atom'
        );
    }

    public static function loadLanguage($extension = 'plg_system_nrframework', $basePath = '')
    {
        if ($basePath && \JFactory::getLanguage()->load($extension, $basePath))
        {
            return true;
        }

        $basePath = self::getExtensionPath($extension, $basePath, 'language');

        return \JFactory::getLanguage()->load($extension, $basePath);
    }

    /**
     *  Returns extension ID
     *
     *  @param   string  $extension  Extension name
     *
     *  @return  integer
     * 
     *  @deprecated Use \NRFramework\Extension::getID instead
     */
    public static function getExtensionID($extension, $folder = null)
    {
        $type = is_null($folder) ? 'component' : 'plugin';
        return \NRFramework\Extension::getID($extension, $type, $folder);
    }

    /**
     *  Checks if extension is installed
     *
     *  @param   string  $extension  The extension element name
     *  @param   string  $type       The extension's type 
     *  @param   string  $folder     Plugin folder     * 
     *
     *  @return  boolean             Returns true if extension is installed
     * 
     *  @deprecated Use \NRFramework\Extension::isInstalled instead
     */
    public static function extensionInstalled($extension, $type = 'component', $folder = 'system')
    {
        return \NRFramework\Extension::isInstalled($extension, $type, $folder);
    }

    /**
     *  Returns the version number from the extension's xml file
     *
     *  @param   string  $extension  The extension element name
     *
     *  @return  string              Extension's version number
     */
    public static function getExtensionVersion($extension, $type = false)
    {
        $hash  = MD5($extension . "_" . ($type ? "1" : "0"));
        $cache = Cache::read($hash);

        if ($cache)
        {
            return $cache;
        }

        $xml = self::getExtensionXMLFile($extension);

        if (!$xml)
        {
            return false;
        }

        $xml = \JInstaller::parseXMLInstallFile($xml);

        if (!$xml || !isset($xml['version']))
        {
            return '';
        }

        $version = $xml['version'];

        if ($type)
        {
            $extType = Extension::isPro($extension) ? 'Pro' : 'Free';
            $version = $xml["version"] . " " . $extType;
        }

        return Cache::set($hash, $version);
    }

    public static function getExtensionXMLFile($extension, $basePath = JPATH_ADMINISTRATOR)
    {
        $alias = explode("_", $extension);
        $alias = end($alias);

        $filename = (strpos($extension, 'mod_') === 0) ? "mod_" . $alias : $alias;
        $file = self::getExtensionPath($extension, $basePath) . "/" . $filename . ".xml";

        if (\JFile::exists($file))
        {
            return $file;
        }
        
        return false;
    }

    /**
     * @deprecated // Use Extension::isPro();
     */
    public static function extensionHasProInstalled($extension)
    {
        return Extension::isPro($extension);
    }

    public static function getExtensionPath($extension = 'plg_system_nrframework', $basePath = JPATH_ADMINISTRATOR, $check_folder = '')
    {
        $path = '';

        switch (true)
        {
            case (strpos($extension, 'com_') === 0):
                $path = 'components/' . $extension;
                break;

            case (strpos($extension, 'mod_') === 0):
                $path = 'modules/' . $extension;
                break;

            case (strpos($extension, 'plg_system_') === 0):
                $path = 'plugins/system/' . substr($extension, strlen('plg_system_'));
                break;

            case (strpos($extension, 'plg_editors-xtd_') === 0):
                $path = 'plugins/editors-xtd/' . substr($extension, strlen('plg_editors-xtd_'));
                break;
        }

        if (empty($path))
        {
            return;
        }

        $check_folder = $check_folder ? '/' . $check_folder : '';
        $basePath = empty($basePath) ? JPATH_ADMINISTRATOR : $basePath;

        if (is_dir($basePath . '/' . $path . $check_folder))
        {
            return $basePath . '/' . $path;
        }

        if (is_dir(JPATH_ADMINISTRATOR . '/' . $path . $check_folder))
        {
            return JPATH_ADMINISTRATOR . '/' . $path;
        }

        if (is_dir(JPATH_SITE . '/' . $path . $check_folder))
        {
            return JPATH_SITE . '/' . $path;
        }

        return $basePath;
    }

    public static function loadModule($id, $moduleStyle = null)
    {  
        // Return if no module id passed
        if (!$id) 
        {
            return;
        }

        // Fetch module from db
        $db = \JFactory::getDBO();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__modules')
            ->where('id='.$db->q($id));

        $db->setQuery($query);

        // Return if no modules found
        if (!$module = $db->loadObject()) 
        {
            return;
        }

        // Success! Return module's html
        return \JModuleHelper::renderModule($module, $moduleStyle);
    }

    public static function fixDate(&$date)
    {
        if (!$date)
        {
            $date = null;

            return;
        }

        $date = trim($date);
        
        // Check if date has correct syntax: 00-00-00 00:00:00
        if (preg_match('#^[0-9]+-[0-9]+-[0-9]+( [0-9][0-9]:[0-9][0-9]:[0-9][0-9])$#', $date))
        {
            return;
        }
        
        // Check if date has syntax: 00-00-00 00:00
        // If so, add :00 (seconds)
        if (preg_match('#^[0-9]+-[0-9]+-[0-9]+ [0-9][0-9]:[0-9][0-9]$#', $date))
        {
            $date .= ':00';

            return;
        }

        // Check if date has a prepending date syntax: 00-00-00 ...
        // If so, add 00:00:00 (hours:mins;secs)
        if (preg_match('#^([0-9]+-[0-9]+-[0-9]+)#', $date, $match))
        {
            $date = $match[1] . ' 00:00:00';
            
            return;
        }

        // Date format is not correct, so return null
        $date = null;
    }

    /**
     * Change date's timezone to UTC by modyfing the offset
     *
     * @param  string   $date   The date in timezone other than UTC
     * 
     * @return string   The date in UTC
     */
    public static function dateToUTC($date)
    {
		$date = is_string($date) ? trim($date) : $date;

		if (empty($date) || is_null($date) || $date == '0000-00-00 00:00:00')
		{
			return $date;
		}

        $timezone = Factory::getUser()->getParam('timezone', Factory::getConfig()->get('offset'));

        $date = new Date($date, $timezone);
        $date->setTimezone(new \DateTimeZone('UTC'));

        $dateUTC = $date->format('Y-m-d H:i:s', true, false);

        return $dateUTC;
    }

    /**
     * Applies the site's or the user's timezone to a given date.
     * 
     * @param   string  $date
     * @param   string  $format
     * 
     * @return  string
     */
    public static function applySiteTimezoneToDate($date, $format = 'Y-m-d H:i:s')
    {
        $timezone = new \DateTimeZone(Factory::getUser()->getParam('timezone', Factory::getConfig()->get('offset')));
        return Factory::getDate($date)->setTimezone($timezone)->format($format, true);
    }

    /**
     * Change date's timezone to UTC by modyfing the offset
     *
     * @param  string   $date   The date in timezone other than UTC
     * 
     * @return string   The date in UTC
     * 
     * @deprecated Use dateToUTC()
     */
    public static function fixDateOffset(&$date)
    {
        $date = self::dateToUTC($date);
    }

    // Text
    public static function clean($string) 
    {
        $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
        return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
    }

    public static function dateTimeNow() 
    {
        return \JFactory::getDate()->format("Y-m-d H:i:s");
    }

    /**
     *  Get framework plugin's parameters
     *
     *  @return  JRegistry   The plugin parameters
     */
    public static function params()
    {
        $hash = md5('frameworkParams');

        if (Cache::has($hash))
        {
            return Cache::read($hash);
        }

        $db = \JFactory::getDBO();

        $result = $db->setQuery(
            $db->getQuery(true)
            ->select('params')
            ->from('#__extensions')
            ->where('element = ' . $db->quote('nrframework'))
        )->loadResult();

        return Cache::set($hash, new Registry($result));
    }

    /**
     * Checks whether string starts with substring.
     * 
     * @param   string  $string
     * @param   string  $query
     * 
     * @return  bool
     */
    public static function startsWith($string, $query)
    {
        return substr($string, 0, strlen($query)) === $query;
    }

    /**
     * Updates the Download Key in the Novarain Framework system plugin.
     * 
     * @param   string  $key
     * 
     * @return  bool
     */
    public static function updateDownloadKey($key)
    {
        if (empty($key))
        {
            return false;
        }

		// Update params
        $db = \JFactory::getDBO();

		// Get params
		$query = $db->getQuery(true)
            ->select($db->quoteName('params'))
            ->from($db->quoteName('#__extensions'))
            ->where($db->quoteName('type') . ' = ' . $db->quote('plugin'))
            ->where($db->quoteName('element') . ' = ' . $db->quote('nrframework'));

        $db->setQuery($query);
        $params = $db->loadResult();

        $params = json_decode($params, true);
        
        // Set Download Key
        $params['key'] = $key;

        // Update params
		$query->clear()
            ->update('#__extensions')
            ->set($db->quoteName('params') . ' = ' . $db->quote(json_encode($params)))
            ->where($db->quoteName('type') . ' = ' . $db->quote('plugin'))
            ->where($db->quoteName('element') . ' = ' . $db->quote('nrframework'));
        $db->setQuery($query);
        $db->execute();

        return true;
    }
}