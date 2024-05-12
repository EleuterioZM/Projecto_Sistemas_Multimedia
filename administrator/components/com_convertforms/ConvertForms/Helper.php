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

use NRFramework\Cache;
use ConvertForms\Form;
use Joomla\Registry\Registry;

jimport('joomla.log.log');

class Helper
{
    /**
     * Ensure same script are added once on the page
     *
     * @param  string $styles   The script code
     * 
     * @return null on failure.
     */
    public static function addScriptDeclarationOnce($script)
    {
        $hash = 'cf_script_' . md5($script);

        if (Cache::has($hash))
        {
            return;
        }

        \JFactory::getDocument()->addScriptDeclaration($script);

        Cache::set($hash, true);
    }

    /**
     * Ensure same styles are added once on the page
     *
     * @param  string $styles   The CSS code
     * 
     * @return null on failure.
     */
    public static function addStyleDeclarationOnce($styles)
    {
        $hash = 'cf_styles_' . md5($styles);

        if (Cache::has($hash))
        {
            return;
        }

        \JFactory::getDocument()->addStyleDeclaration($styles);

        Cache::set($hash, true);
    }

    /**
     * Check if current logged in user is authorised to view a resource.
     *
     * @param  string $action
     *
     * @return void
     */
    public static function authorise($action, $throw_exception = false)
    {
        $authorised = \JFactory::getUser()->authorise($action, 'com_convertforms');

        if (!$authorised && $throw_exception)
        {
            throw new \JAccessExceptionNotallowed(\JText::_('JERROR_ALERTNOAUTHOR'), 403);
        }

        return $authorised;
    }

    /**
     * Trigger Error Event
     *
     * @param string    $error         The error message
     * @param string    $category      The error category
     * @param integer   $form_id       The form ID assosiated with the error
     * @param mixed     $data          Extra data related to the error occured
     *
     * @return void
     */
    public static function triggerError($error, $category, $form_id, $data = null)
    {
        \JPluginHelper::importPlugin('convertforms');
        \JFactory::getApplication()->triggerEvent('onConvertFormsError', [$error, $category, $form_id, $data]);
    }

    /**
     * Convert all applicable characters to HTML entities
     *
     * @param  string $input The input string.
     *
     * @return string
     */
    public static function escape($input)
    {
        if (!is_string($input))
        {
            return $input;
        }

        // Convert all HTML tags to HTML entities.
        $input = htmlspecialchars($input, ENT_NOQUOTES, 'UTF-8');

        // We do not need any Smart Tag replacements take place here, so we need to escape curly brackets too.
        $input = str_replace(['{', '}'], ['&#123;', '&#125;'], $input);

        // Respect newline characters, by converting them to <br> tags.
        $input = nl2br($input);

        return $input;
    }

    public static function getComponentParams()
    {
        $hash = 'cfComponentParams';

        if (Cache::has($hash))
        {
            return Cache::get($hash);
        }

        return Cache::set($hash, \JComponentHelper::getParams('com_convertforms'));
    }

    public static function getFormLeadsCount($form)
    {
        $hash = 'formLeadsCount' . $form;

        if (Cache::has($hash))
        {
            return Cache::get($hash);
        }

        if (!$form || $form == 0)
        {
            return 0;
        }

        $db = \JFactory::getDBO();
        $query = $db->getQuery(true);

        $query
            ->select('count(*)')
            ->from('#__convertforms_conversions')
            ->where('form_id = ' . $form);

        $db->setQuery($query);

        return Cache::set($hash, $db->loadResult());
    }

    /**
     *  Renders form template selection modal to the document
     *
     *  @return   void
     */
    public static function renderSelectTemplateModal()
    {
        echo \JHtml::_('bootstrap.renderModal', 'cfSelectTemplate', array(
            'url'         => 'index.php?option=com_convertforms&view=templates&tmpl=component',
            'title'       => \JText::_('COM_CONVERTFORMS_TEMPLATES_SELECT'),
            'closeButton' => true,
            'height'      => '100%',
            'width'       => '100%',
            'modalWidth'  => '70',
            'bodyHeight'  => '70',
            'footer'      => '
                <a href="' . \JURI::base() . 'index.php?option=com_convertforms&view=form&layout=edit" class="btn btn-primary">
                    <span class="icon-new"></span> ' . \JText::_('COM_CONVERTFORMS_TEMPLATES_BLANK') . '
                </a>
            '
        ));
    }

    /**
     *  Writes the Not Found Items message
     *
     *  @param   string  $view 
     *
     *  @return  string
     */
    public static function noItemsFound($view = 'submissions')
    {
        $html[] = '<span style="font-size:16px; position:relative; top:2px;" class="icon-smiley-sad-2"></span>';
        $html[] = \JText::sprintf('COM_CONVERTFORMS_NO_RESULTS_FOUND', strtolower(\JText::_('COM_CONVERTFORMS_' . $view)));

        return implode(' ', $html);
    }

    /**
     *  Get Visitor ID
     *
     *  @return  string
     */
    public static function getVisitorID()
    {
        return \NRFramework\VisitorToken::getInstance()->get();
    }

    /**
     *  Returns campaigns list visitor is subscribed to
     *  If the user is logged in, we try to get the campaigns by user's ID
     *  Otherwise, the visitor cookie ID will be used instead
     *
     *  @return  array  List of campaign IDs
     */
    public static function getVisitorCampaigns()
    {
        $db    = \JFactory::getDBO();
        $query = $db->getQuery(true);
        $user  = \JFactory::getUser();

        $query
            ->select('campaign_id')
            ->from('#__convertforms_conversions')
            ->where('state = 1')
            ->group('campaign_id');

        // Get submissions by user id if visitor is logged in
        if ($user->id)
        {
            $query->where('user_id = ' . (int) $user->id);
        } else 
        {
            // Get submissions by Visitor Cookie
            if (!$visitorID = self::getVisitorID())
            {
                return false;
            }

            $query->where('visitor_id = ' . $db->q($visitorID));
        }

        $db->setQuery($query);
        return $db->loadColumn();
    }

    /**
     *  Returns an array with all available campaigns
     *
     *  @return  array
     */
    public static function getCampaigns()
    {
        \JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_convertforms/models');

        $model = \JModelLegacy::getInstance('Campaigns', 'ConvertFormsModel', array('ignore_request' => true));
        $model->setState('filter.state', 1);

        return $model->getItems();
    }

    /**
     *  Logs messages to log file
     *
     *  @param   string  $msg   The message
     *  @param   object  $type  The log type
     *
     *  @return  void
     */
    public static function log($msg, $type = 'debug')
    {
        $debugIsEnabled = self::getComponentParams()->get('debug', false);

        if ($type == 'debug' && !$debugIsEnabled)
        {
            return;
        }

        $type = ($type == 'debug') ? \JLog::DEBUG : \JLog::ERROR;

        try {
            \JLog::add($msg, $type, 'com_convertforms');
        } catch (\Throwable $th){
        }
    }

    /**
     *  Loads pre-made form template 
     *
     *  @param   string  $name  The template name
     *
     *  @return  object         
     */
    public static function loadFormTemplate($name)
    {
        $form = JPATH_ROOT . '/media/com_convertforms/templates/' . $name . '.cnvf';

        if (!\JFile::exists($form))
        {
            return;
        }

        $content = file_get_contents($form);

        if (empty($content))
        {
            return;
        }

        $item = json_decode($content, true);
        $item = $item[0];

        $data = (object) array_merge((array) $item, (array) json_decode($item['params']));
        $data->id = 0;
        $data->campaign = null;
        $data->fields = (array) $data->fields;

        return $data;
    }

    /**
     * Configure the Linkbar.
     *
     * @param   string  $vName  The name of the active view.
     *
     * @return  void
     */
    public static function addSubmenu($vName)
    {
        $items = [
            [
                'label' => 'NR_DASHBOARD',
                'view'  => 'convertforms',
                'skip_auth' => true
            ],
            [
                'label' => 'COM_CONVERTFORMS_FORMS',
                'view'  => 'forms',
            ],
            [
                'label' => 'COM_CONVERTFORMS_CAMPAIGNS',
                'view'  => 'campaigns',
            ],
            [
                'label' => 'COM_CONVERTFORMS_SUBMISSIONS',
                'view'  => 'conversions',
                'view_rule' => 'submissions'
            ],
            [
                'label' => 'COM_CONVERTFORMS_ADDONS',
                'view'  => 'addons',
            ]
        ];

        foreach ($items as $item)
        {
            if (!isset($item['skip_auth']) && !self::authorise('convertforms.' . (isset($item['view_rule']) ? $item['view_rule'] : $item['view']) . '.manage'))
            {
                continue;
            }

            \JHtmlSidebar::addEntry(\JText::_($item['label']), 'index.php?option=com_convertforms&view=' . $item['view'], $item['view']);   
        }
    }

    /**
     *  Returns permissions
     *
     *  @return  object
     */
    public static function getActions()
    {
        $user = \JFactory::getUser();
        $result = new \JObject;
        $assetName = 'com_convertforms';

        $actions = array(
            'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.state', 'core.delete'
        );

        foreach ($actions as $action)
        {
            $result->set($action, $user->authorise($action, $assetName));
        }

        return $result;
    }
  
    /**
     *  Prepares a form object for rendering
     *
     *  @param   Object  $form  The form object
     *
     *  @return  array          The prepared form array
     */
    public static function prepareForm($item)
    {
        $item['id'] = isset($item['id']) ? $item['id'] : 0;
        $classPrefix = 'cf';

        // Replace variables
        $item = \ConvertForms\SmartTags::replace($item, null, $item['id']);

        $params = new Registry($item['params']);
        $item['params'] = $params;

        /* Box Classes */
        $boxClasses = array(
            "convertforms",
            $classPrefix,
            $classPrefix . "-" . $params->get("imgposition"),
            $classPrefix . "-" . $params->get("formposition"),
            $params->get("hideform", true) ? $classPrefix . "-success-hideform" : null,
            $params->get("hidetext", false) ? $classPrefix . "-success-hidetext" : null,
            !$params->get("hidelabels", false) ? $classPrefix . "-hasLabels" : null,
            $params->get("centerform", false) ? $classPrefix . "-isCentered" : null,
            $params->get("classsuffix", null),
            $classPrefix . '-labelpos-' . $params->get('labelposition', 'top'),
        );

        /* Box Styles */
        $font = trim($params->get('font'));

        // Form HTML Attributes
        $item['boxattributes'] = implode(" ",
            array(
                'id="' . $classPrefix . '_' . $item['id'] . '"',
                'class="' . implode(" ", $boxClasses) . '"',
            )
        );

        // Main Image Checks
        $imageOption = $params->get("image");
        if ($imageOption == '1')
        {
            $imageFile = Helper::cleanLocalImage($params->get('imagefile'));

            if (\JFile::exists(JPATH_SITE . '/' . $imageFile))
            {
                $item['image'] = \JURI::root() . $imageFile;
            }
        }
        if ($imageOption == '2')
        {
            $item['image'] = $params->get("imageurl");
        }

        // Image Container
        $item['imagecontainerclasses'] = implode(" ", array(
            (in_array($params->get("imgposition"), array("img-right", "img-left")) ? $classPrefix . "-col-medium-" . $params->get("imagesize", 6) : null),
        ));

        // Image
        $item['imagestyles'] = array(
            "width:" . ($params->get("imageautowidth", "auto") == "auto" ? "auto" : (int) $params->get("imagewidth", "500") . "px"),
            "left:". (int) $params->get("imagehposition", "0") . "px ",
            "top:". (int) $params->get("imagevposition", "0") . "px"
        );
        $item['imageclasses'] = array(
            ($params->get("hideimageonmobile", false) ? "cf-hide-mobile" : "")
        );

        // Form Container
        $item['formclasses'] = array(
            (in_array($params->get("formposition"), array("form-left", "form-right")) ? $classPrefix . "-col-large-" . $params->get("formsize", 6) : null),
        );
        $item['formstyles'] = array(
            "background-color:" . $params->get("formbgcolor", "none")
        );

        // Content
        $item['contentclasses'] = implode(" ", array(
            (in_array($params->get("formposition"), array("form-left", "form-right")) ? $classPrefix . "-col-large-" . (16 - $params->get("formsize", 6)) : null),
        ));

        // Text Container
        $item['textcontainerclasses'] = implode(" ", array(
            (in_array($params->get("imgposition"), array("img-right", "img-left")) ? $classPrefix . "-col-medium-" . (16 - $params->get("imagesize", 6)) : null),
        ));

        $textContent = trim($params->get("text", ''));
        $footerContent = trim($params->get("footer", ''));

        $item['textIsEmpty']   = empty($textContent);
        $item['footerIsEmpty'] = empty($footerContent);
        $item['hascontent']    = !$item['textIsEmpty'] || (bool) isset($item['image']) ?  1 : 0;

        // Prepare Fields
        $item['fields_prepare'] = \ConvertForms\FieldsHelper::prepare($item);

        // Load custom fonts into the document
        \NRFramework\Fonts::loadFont($font);

        return $item;
    }

    /**
     *  Renders form by ID
     *
     *  @param   integer  $id  The form ID
     *
     *  @return  string        The form HTML
     */
    public static function renderFormById($id)
    {
        if (!$data = Form::load($id))
        {
            return;
        }

        self::loadassets(true);

        return self::renderForm($data);
    }

    /**
     *  Renders Form
     *
     *  @param   integer  $formid  The form id
     *
     *  @return  string            The form HTML
     */
    public static function renderForm($data)
    {
        $app = \JFactory::getApplication();

        \JPluginHelper::importPlugin('convertforms');
        \JPluginHelper::importPlugin('convertformstools');

        // load translation strings
        self::loadTranslations();

        // @todo - Move PHP Scripts logic into a separate plugin
        // Let user manipulate the form's settings by running their own PHP script
        $payload_1 = ['form' => &$data];
        Form::runPHPScript($data['id'], 'formprepare', $payload_1);

        $app->triggerEvent('onConvertFormsFormBeforeRender', [&$data]);

        // Prepare form and fields
        $data = self::prepareForm($data);
        $html = self::layoutRender('form', $data);

        // Let user manipulate the form's HTML by running their own PHP script
        $payload_2 = [
            'formLayout' => &$html,
            'form'       => $data
        ];
        Form::runPHPScript($data['id'], 'formdisplay', $payload_2);

        $app->triggerEvent('onConvertFormsFormAfterRender', [&$html, $data]);

        // Prevent user frustration by fixing broken images in the backend. 
        // This is required since v2.8.0 where we no longer forces absolute URLs in the text editors.
        if ($app->isClient('administrator'))
        {
            $html = \NRFramework\URLHelper::relativePathsToAbsoluteURLs($html, null, false);
        }        

        return $html;
    }
    
    /**
     * Enqueues translations for the front-end
     * 
     * @return  void
     */
    private static function loadTranslations()
    {
        \JText::script('COM_CONVERTFORMS_INVALID_RESPONSE');
        \JText::script('COM_CONVERTFORMS_INVALID_TASK');
    }

    /**
     * Render HTML overridable layout
     *
     * @param  string $layout   The layout name
     * @param  object $data     The data passed to layout
     *
     * @return string   The rendered HTML layout
     */
    public static function layoutRender($layout, $data)
    {
        return \JLayoutHelper::render($layout, $data, null, ['debug' => false, 'client' => 1, 'component' => 'com_convertforms']);
    }

    /**
     *  Loads components media files
     *
     *  @param   boolean  $front
     *
     *  @return  void
     */
    public static function loadassets($frontend = false)
    {
        static $run;

        if ($run)
        {
            return;
        }

        $run = true;

        // Front-end media files
        if ($frontend)
        {
            // Load core.js needed by keepalive script. 
            \JHtml::_('behavior.core');
            \JHtml::_('behavior.keepalive');

			\JHtml::script('com_convertforms/site.js', ['relative' => true, 'version' => 'auto']);

            $params = self::getComponentParams();

            if ($params->get("loadCSS", true))
            {
                \JHtml::stylesheet('com_convertforms/convertforms.css', ['relative' => true, 'version' => 'auto']);
            }

            $doc = \JFactory::getDocument();
            $options = $doc->getScriptOptions('com_convertforms');
            $options = is_array($options) ? $options : [];

            $options = [
                // Remove trailing slash from the base URL to prevent unwanted redirections during AJAX submission
                'baseURL' => \Joomla\String\StringHelper::rtrim(\JRoute::_('index.php?option=com_convertforms'), '/'),
                'debug' => (bool) $params->get('debug', false)
            ];

            $doc->addScriptOptions('com_convertforms', $options);

            return;
        }

        \JHtml::_('jquery.framework');
        \JHtml::stylesheet('com_convertforms/convertforms.sys.css', ['relative' => true, 'version' => 'auto']);
    }

    /**
     *  Get Campaign Item by ID
     *
     *  @param   integer  $id  The campaign ID
     *
     *  @return  object
     */
    public static function getCampaign($id)
    {
        $model = \JModelLegacy::getInstance('Campaign', 'ConvertFormsModel', array('ignore_request' => true));
        return $model->getItem($id);
    }

    /**
     * Write the given error message to log file. 
     *
     * @param  string $error_message    The error message
     *
     * @return void
     */
    public static function logError($error_message)
    {
        try {
            \JLog::add($error_message, \JLog::ERROR, 'convertforms_errors');
        } catch (\Throwable $th) {
        }
    }

    /**
     * Format given date based on the DATE_FORMAT_LC5 global format
     *
     * @param  string $date
     *
     * @return string
     */
    public static function formatDate($date)
    {
        if (!$date || $date == '0000-00-00 00:00:00')
        {
            return $date;
        }

        return \JHtml::_('date', $date, \JText::_('DATE_FORMAT_LC5'));
    }

    public static function getColumns($form_id)
    {
        if (!$form_id)
        {
            return [];
        }

        $fields = Form::load($form_id, true, true);

        if (!is_array($fields) || !is_array($fields['fields']))
        {
            return [];
        }

        // Form Fields
        $form_fields = array_map(function($value)
        {
            return 'param_' . $value;
        }, array_keys($fields['fields']));

        $default_columns = [
            'id',
            'created', 
            'user_id',
            'user_username',
            'visitor_id',
            'form_name',
            'param_leadnotes'
        ];

        // Set ID and Date Submitted as the first 2 columns
        $columns = array_merge(array_slice($default_columns, 0, 2), $form_fields, array_slice($default_columns, 2, count($default_columns)));

        return $columns;
    }

	/**
	 * Return absolute full URL of a path
	 *
	 * @param	string	$path
	 *
	 * @return	string
	 */
	public static function pathTorelative($path)
	{
		return str_replace([JPATH_SITE, JPATH_ROOT], '', $path);
	}

	/**
	 * Return absolute full URL of a path
	 *
	 * @param	string	$path
	 *
	 * @return	string
	 */
	public static function absURL($path)
	{
		$path = str_replace([JPATH_SITE, JPATH_ROOT, \JURI::root()], '', $path);
		$path = \JPath::clean($path);

		// Convert Windows Path to Unix
		$path = str_replace('\\','/',$path);

		$path = ltrim($path, '/');
		$path = \JURI::root() . $path;

		return $path;
    }

    /**
     * This is a joke. Joomla 4's media field started including width and height information in the path. 
     * So, we need to clean the path before we can use it. 
     * 
     * images/headers/blue-flower.jpg#joomlaImage://local-images/headers/blue-flower.jpg?width=700&height=180)
     * 
     * @param  string $path
     * 
     * @return string
     */
    public static function cleanLocalImage($path)
    {
        return defined('nrJ4') ? \Joomla\CMS\Helper\MediaHelper::getCleanMediaFieldValue($path) : $path;
    }
}