<?php
/**
* @package		Komento
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(__DIR__ . '/dependencies.php');
require_once(__DIR__ . '/legacy.php');

use Foundry\Libraries\Pagination;
use Foundry\Libraries\Scripts;

class KT extends KTLegacy
{
	public static $package = 'paid';
	public static $component;
	public static $application;
	private static $messages = [];
	static private $views = [];
	static private $scripts = [];

	/**
	 * Accessing foundry library should be done here
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function fd()
	{
		static $fd = null;

		if (is_null($fd)) {
			KT::initFoundry();

			$config = KT::config();

			$fd = new FoundryLibrary('com_komento', 'kt', 'Komento', '', [
				'appearance' => $config->get('layout_appearance')
			]);
		}

		return $fd;
	}

	/**
	 * Initializes Foundry
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function initFoundry()
	{
		require_once(JPATH_LIBRARIES . '/foundry/foundry.php');
	}

	/**
	 * Check if foundry plugin enabled or not.
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function isFoundryEnabled()
	{
		static $isEnabled = null;

		if (is_null($isEnabled)) {

			$isEnabled = true;

			if (!JFile::exists(JPATH_LIBRARIES . '/foundry/foundry.php')) {
				$isEnabled = false;
			}

			// Check if the foundry plugin enabled or not.
			if (!JPluginHelper::isEnabled('system', 'foundry')) {
				$isEnabled = false;
			}
		}

		// passed. do nothing.
		return $isEnabled;
	}

	/**
	 * Allows caller to store scripts that needs to be added on the page
	 *
	 * @since	3.1.3
	 * @access	public
	 */
	public static function addScript($contents)
	{
		self::$scripts[] = $contents;
	}

	/**
	 * Allows caller to retrieve scripts that needs to be added on the page
	 *
	 * @since	3.1.3
	 * @access	public
	 */
	public static function getScripts()
	{
		if (empty(self::$scripts)) {
			return false;
		}
		
		return implode(' ', self::$scripts);
	}

	public static function getPackage()
	{
		return self::$package;
	}

	/**
	 * Initializes Komento by rendering the necessary scripts and css
	 *
	 * @since	3.0
	 * @access	public
	 */
	public static function initialize($location = 'site')
	{
		static $loaded = [];

		if (!isset($loaded[$location])) {

			$app = JFactory::getApplication();
			$config = KT::config();

			$location = FH::isFromAdmin() ? 'admin' : 'site';

			$theme = $location == 'admin' ? 'default' : strtolower($config->get('layout_theme'));

			// Run initialization codes for javascript side of things.
			$input = $app->input;

			// Only for Komento
			$kt = $input->get('option', '', 'default');

			// Compile scripts
			if ($kt == 'com_komento' && $input->get('compile', false, 'bool') != false && KT::isSiteAdmin()) {

				// Determines if we should minify the output.
				$minify = $input->get('minify', false, 'bool');

				$compiler = KT::compiler();
				$result = [];

				// Compile with jquery.komento.js
				$result['standard'] = $compiler->compile($location, $minify);

				header('Content-type: text/x-json; UTF-8');
				echo json_encode($result);
				exit;
			}

			// Attach foundry scripts
			Scripts::init();

			// Attach the scripts
			$scripts = KT::scripts();
			$scripts->attach();

			// Attach css files
			$stylesheet = KT::stylesheet($location);
			$stylesheet->attach();

			$loaded[$location] = true;
		}

		return $loaded[$location];
	}

	/**
	 * Includes a file given a particular namespace in POSIX format.
	 *
	 * @since	3.0
	 * @access	public
	 */
	public static function import($namespace)
	{
		static $locations	= [];

		if (!isset($locations[$namespace])) {
			// Explode the parts to know exactly what to lookup for
			$parts = explode(':', $namespace);

			// Non POSIX standard.
			if (count($parts) <= 1) {
				return false;
			}

			$base = $parts[0];

			// Default path would be the front-end
			$basePath = KT_ROOT;

			if ($base === 'admin') {
				$basePath = KT_ADMIN;
			}

			if ($base === 'themes') {
				$basePath = KT_THEMES;
			}

			// Replace / with proper directory structure.
			$path = str_ireplace('/', DIRECTORY_SEPARATOR, $parts[1]);

			// Get the absolute path now.
			$path = $basePath . $path . '.php';

			// Include the file now.
			include_once($path);

			$locations[$namespace] = true;
		}

		return true;
	}

	/**
	 * Loads a library dynamically
	 *
	 * @since	3.0
	 * @access	public
	 */
	public static function __callStatic($method, $arguments)
	{
		$file = dirname(__FILE__) . '/' . strtolower($method) . '/' . strtolower($method) . '.php';

		require_once($file);

		$class = 'Komento' . ucfirst($method);

		if (count($arguments) == 1) {
			$arguments = $arguments[0];
		}

		$obj = null;

		if (!$arguments) {
			$obj = new $class();
		} 

		if ($arguments) {
			$obj = new $class($arguments);
		}

		return $obj;
	}

	/**
	 * Loads a library
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function load($library)
	{
		// We do not need to use FCJString here because files are not utf-8 anyway.
		$library = strtolower($library);
		$obj = false;

		$path = __DIR__ . '/' . $library . '/' . $library . '.php';

		include_once($path);
	}

	/**
	 * Retrieves the cdn url for the site
	 *
	 * @since	3.0
	 * @access	public
	 */
	public static function getCdnUrl()
	{
		$config = KT::config();
		$url = $config->get('komento_cdn_url');

		if (!$url) {
			return false;
		}

		// Ensure that the url contains http:// or https://
		if (stristr($url, 'http://') === false && stristr($url, 'https://') === false) {
			$url = '//' . $url;
		}

		return $url;
	}

	/**
	 * Retrieves the current version of Komento
	 *
	 * @since	3.1.0
	 * @access	public
	 */
	public static function getLocalVersion()
	{
		static $version = null;

		if (is_null($version)) {
			$parser = FH::getXml(JPATH_ADMINISTRATOR . '/components/com_komento/komento.xml', true);

			$version = (string) $parser->version;
		}

		return $version;
	}

	/**
	 * Alternative method to get a table
	 *
	 * @since	3.0
	 * @access	public
	 */
	public static function table($tableName, $prefix = 'KomentoTable')
	{
		require_once(KOMENTO_TABLES . '/parent.php');

		$table = KomentoTable::getInstance($tableName, $prefix);

		return $table;
	}

	/**
	 * Retrieves the model for Komento
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function model($name, $config = [])
	{
		static $models = [];

		$key = md5(strtolower($name));

		// Determines if we should run the initialize state for the model
		$initializeStates = \FH::normalize($config, 'initState', false);

		if ($initializeStates) {
			unset($config['initState']);
		}

		if (!isset($models[$key])) {

			// Include the base komento model
			$base = KOMENTO_ADMIN_ROOT . '/includes/model.php';
			require_once($base);

			$file = KOMENTO_MODELS . '/' . strtolower($name) . '.php';

			require_once($file);

			$classname = 'KomentoModel' . ucfirst($name);

			$config = array_merge($config, [
				'fd' => KT::fd()
			]);

			$models[$key] = new $classname($config);
		}

		if ($models[$key] && $initializeStates && method_exists($models[$key], 'initStates')) {
			$models[$key]->initStates();
		}
		
		return $models[$key];
	}

	/**
	 * Retrieve Komento's configuration.
	 *
	 * @return	object	JParameter object.
	 **/
	public static function config($component = '', $default = true)
	{
		static $config = null;

		if (!$config) {

			$default = KOMENTO_ADMIN_ROOT . '/defaults/configuration.json';
			$raw = file_get_contents($default);

			$config = FH::registry($raw);
			$config->default = clone $config->toObject();

			// @task: Now we need to get the user defined configuration that is stored in the database.
			if (!defined('KOMENTO_CLI')) {
				//get config stored in db
				$table = KT::table('Configs');
				$table->load('config');

				$stored = new JRegistry($table->params);

				$config->merge($stored);
			}
		}

		return $config;
	}

	/**
	 * If the current user is a super admin, allow them to change the environment via the query string
	 *
	 * @since	3.1.0
	 * @access	public
	 */
	public static function checkEnvironment()
	{
		if (!KT::isSiteAdmin()) {
			return;
		}

		$app = JFactory::getApplication();
		$environment = $app->input->get('kt_env', '', 'word');
		$allowed = ['production', 'development'];

		// Nothing has changed
		if (!$environment || !in_array($environment, $allowed)) {
			return;
		}

		// We also need to update the database value
		$model = KT::model('Settings');
		$model->save([
			'komento_environment' => $environment
		]);

		KT::info()->set('Updated system environment to <b>' . $environment . '</b> mode', 'success');

		return $app->redirect('index.php?option=com_komento');
	}

	/**
	 * Retrieve a list of user groups from the site.
	 *
	 * @since	3.0
	 * @access	public
	 */
	public static function getUserGroups()
	{
		$db	= KT::db();

		$sql = $db->sql();

		$sql->select('#__usergroups', 'a');
		$sql->column('a.*');
		$sql->column('b.id', 'level', 'count distinct');
		$sql->join('#__usergroups', 'b');
		$sql->on('a.lft', 'b.lft', '>');
		$sql->on('a.rgt', 'b.rgt', '<');
		$sql->group('a.id', 'a.title', 'a.lft', 'a.rgt', 'a.parent_id');
		$sql->order('a.lft', 'ASC');

		$db->setQuery($sql);

		$result = $db->loadObjectList();

		if (!$result) {
			return $result;
		}

		foreach($result as &$row) {
			$sql->clear();

			$sql->select('#__user_usergroup_map');
			$sql->where('group_id', $row->id);

			$db->setQuery($sql->getTotalSql());

			$row->total = $db->loadResult();
		}

		return $result;
	}

	/**
	 * Retrieve the user's profile object
	 *
	 * @since	3.0
	 * @access	public
	 */
	public static function user($id = null)
	{
		KT::load('user');

		return KomentoUser::getUser($id);
	}

	public static function getComment($id = 0, $process = 0, $admin = 0)
	{
		static $commentsObj = [];

		if (empty($commentsObj[$id])) {
			$comment = new KomentoComment($id);

			if ($comment->getError()) {
				return false;
			}

			$commentsObj[$id] = $comment;
		}

		if ($process) {
			self::import('admin:/includes/comment/comment');
			$commentsObj[$id] = KomentoCommentHelper::process($commentsObj[$id], $admin);
		}

		return $commentsObj[$id];
	}

	/**
	 * Retrieves the captcha library
	 *
	 * @since	3.0
	 * @access	public
	 */
	public static function captcha()
	{
		static $captcha = null;

		if (is_null($captcha)) {
			require_once(__DIR__ . '/captcha/captcha.php');

			$captcha = new KomentoCaptcha();
		}

		return $captcha;
	}

	/**
	 * Method to display Joomla's core alert
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function raiseWarning($errCode, $msg)
	{
		if (class_exists('JError')) {
			return JError::raiseWarning($errCode, JText::_($msg));
		}

		return JFactory::getApplication()->enqueueMessage(JText::_($msg), 'error');
	}

	/**
	 * A model to get data from a component's content item
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function loadApplication($component = '')
	{
		static $instances = null;

		$component = preg_replace('/[^A-Z0-9_\.-]/i', '', $component);

		// Create a copy of the name so that the original $component won't get affected
		$componentName = $component;

		// If component is empty, then try to load it from getCurrentComponent
		if (empty($componentName)) {
			$componentName = KT::getCurrentComponent();

			// If component is still empty, then assign it as error
			if (empty($componentName)) {
				$componentName = 'error';
			}
		}

		if (!isset($instances[$componentName])) {
			require_once(KOMENTO_ROOT . '/komento_plugins/abstract.php');
			require_once(KOMENTO_ROOT . '/komento_plugins/error.php');
				
			$instances[$componentName] = new KomentoError($component);

			// Check if the component has a komento adapter
			$file = JPATH_ROOT . '/components/' . $componentName . '/komento_plugin.php';

			// If it doesn't exist in component path, then look for Komento's native plugin
			if (!JFile::exists($file)) {
				// Load from Komento's plugin folder
				$file = KOMENTO_ROOT . '/komento_plugins/' . $componentName . '.php';

				if (!JFile::exists($file)) {
					return $instances[$componentName];
				}
			}

			require_once($file);
			$className = 'Komento' . ucfirst(strtolower(preg_replace('/[^A-Z0-9]/i', '', $componentName)));

			if (!class_exists($className)) {
				return $instances[$componentName];
			}

			$classObject = new $className($component);

			if (!($classObject instanceof KomentoExtension) || !$classObject->state) {
				return $instances[$componentName];
			}

			$instances[$componentName] = $classObject;
		}

		return $instances[$componentName];
	}

	/**
	 * Document library can only be instantiated once
	 *
	 * @since	3.0
	 * @access	public
	 */
	public static function document()
	{
		static $doc = null;

		if (is_null($doc)) {
			require_once(__DIR__ . '/document/document.php');

			$doc = new KomentoDocument();
		}

		return $doc;
	}

	/**
	 * Renders a generic error application to generate errors
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function getErrorApplication($component, $cid)
	{
		static $instances = [];
		static $itemInstances = [];

		if (empty($instances[$component])) {
			require_once(KT_PLUGINS . '/error.php');
			$instances[$component] = new KomentoError($component);
		}

		if (empty($itemInstances[$component][$cid])) {
			$itemInstances[$component][$cid] = $instances[$component]->load($cid);
		}

		return $cidInstances[$component][$cid];
	}

	/**
	 * Prerequisites check, right after an event is triggered.
	 * (Forced hack on Komento side to work with multiple components properly because sometimes component doesn't care if their plugin file conflicts with other things or not)
	 *
	 * @param	$plugin			string
	 * @param	$eventTrigger	string
	 * @param	$extension		string
	 * @param	$context		string
	 * @return 	boolean
	 */
	public static function onAfterEventTriggered( $plugin, $eventTrigger, $extension, $context, $article, $params )
	{
		if( $extension === 'com_k2' )
		{
			return true;
		}

		// modules check, generally, don't run komento within modules
		if( !empty( $context ) && stristr( $context , 'mod_' ) !== false )
		{
			return false;
		}

		if ($params instanceof JRegistry) {

			$modSfx = $params->get('moduleclass_sfx', '');
			// exception to ohanah
			if (($extension != 'com_ohanah' || $extension != 'com_ohanahvenue') && $modSfx) {
				return false;
			}
		}

		return true;
	}

	/**
	 * This is where the integration happens where Komento prepares the html output
	 * that can be incuded by the extension.
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function commentify($component, &$article, $options = [])
	{
		KT::load('commentify');

		$commentify = new KomentoCommentify($component);
		return $commentify->render($article, $options);
	}

	public static function mergeOptions($defaults, $options)
	{
		$options = array_merge($defaults, $options);
		foreach ($options as $key => $value) {
			if (!array_key_exists($key, $defaults))
				unset($options[$key]);
		}

		return $options;
	}

	public static function setMessageQueue($message, $type = 'info')
	{
		$session = JFactory::getSession();

		$msgObj = new stdClass();
		$msgObj->message = $message;
		$msgObj->type = strtolower($type);

		//save messsage into session
		$session->set('komento.message.queue', $msgObj, 'KOMENTO.MESSAGE');
	}

	public static function getMessageQueue()
	{
		$session = JFactory::getSession();
		$msgObj = $session->get('komento.message.queue', null, 'KOMENTO.MESSAGE');

		//clear messsage into session
		$session->set('komento.message.queue', null, 'KOMENTO.MESSAGE');

		return $msgObj;
	}

	public static function getCurrentComponent()
	{
		return self::$component;
	}

	/**
	 * Sets the current component being accessed
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function setCurrentComponent($component = 'com_component')
	{
		$component = preg_replace('/[^A-Z0-9_\.-]/i', '', $component);

		self::$component = $component;

		return self::$component;
	}

	/**
	 * Used in J1.6!. To retrieve list of superadmin users's id.
	 * array
	 */
	public static function getSAUsersIds()
	{
		$saGroup = KT::getSAIds();

		//now we got all the SA groups. Time to get the users
		$saUsers = [];
		if (count($saGroup) > 0) {
			foreach ($saGroup as $saId) {
				$userArr = JAccess::getUsersByGroup($saId);

				if (count($userArr) > 0) {
					foreach($userArr as $user) {
						$saUsers[] = $user;
					}
				}
			}
		}

		return $saUsers;
	}

	public static function getSAIds()
	{
		$db = KT::db();

		$query = 'SELECT a.`id`';
		$query .= ' FROM `#__usergroups` AS a';
		$query .= ' LEFT JOIN `#__usergroups` AS b ON a.lft > b.lft AND a.rgt < b.rgt';
		$query .= ' GROUP BY a.id';
		$query .= ' ORDER BY a.lft ASC';

		$db->setQuery($query);
		$result = $db->loadObjectList();

		$saGroup = [];
		foreach ($result as $group) {
			if (JAccess::checkGroup($group->id, 'core.admin')) {
				$saGroup[]  = $group->id;
			}
		}

		return $saGroup;
	}

	/**
	 * Creates an instance of the info library from Foundry
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function info()
	{
		return KT::fd()->info();
	}

	// Method to route standard links (bugged)
	public static function route($link)
	{
		if (JPATH_BASE == JPATH_ADMINISTRATOR) {
			JFactory::$application = JApplication::getInstance('site');
		}

		$link = JRoute::_($link);

		if (JPATH_BASE == JPATH_ADMINISTRATOR) {
			$link = str_ireplace('/administrator/', '/', $link);
			JFactory::$application = JApplication::getInstance('administrator');
		}

		return $link;
	}

	/**
	 * Retrieves an instance of the ajax library
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function ajax()
	{
		static $lib = null;

		if (is_null($lib)) {
			KT::load('ajax');

			$lib = new KomentoAjax(KT::fd());
		}

		return $lib;
	}

	public static function addJomSocialPoint($action, $userId = 0)
	{
		$jsCoreFile	= JPATH_ROOT . '/components/com_community/libraries/core.php';

		if (!JFile::exists($jsCoreFile)) {
			return false;
		}

		require_once($jsCoreFile);

		$my	= JFactory::getUser();

		if (!empty($userId)) {
			$my	= JFactory::getUser($userId);
		}

		if ($my->id != 0) {
			CUserPoints::assignPoint($action, $my->id);
		}

		return true;
	}

	public static function addAUP($plugin_function = '', $referrerid = '', $keyreference = '', $datareference = '')
	{
		$my	= JFactory::getUser();

		if (!empty($referrerid)) {
			$my	= JFactory::getUser($referrerid);
		}

		if ($my->id != 0) {
			$aup = JPATH_ROOT . '/components/com_alphauserpoints/helper.php';
			if (JFile::exists($aup)) {
				require_once($aup);
				AlphaUserPointsHelper::newpoints($plugin_function, AlphaUserPointsHelper::getAnyUserReferreID($referrerid), $keyreference, $datareference);
			}
		}
	}


	public static function addALP($plugin_function = '', $referrerid = '', $keyreference = '', $datareference = '')
	{
		$my	= JFactory::getUser();

		if (!empty($referrerid)) {
			$my	= JFactory::getUser($referrerid);
		}

		if ($my->id != 0) {
			$alp = JPATH_ROOT . '/components/com_altauserpoints/helper.php';
			if (JFile::exists($alp)) {
				require_once($alp);
				AltaUserPointsHelper::newpoints($plugin_function, AltaUserPointsHelper::getAnyUserReferreID($referrerid), $keyreference, $datareference);
			}
		}
	}


	public static function addDiscussPoint($action, $userId = 0, $title = '')
	{
		$my	= JFactory::getUser();

		if (!empty($userId)) {
			$my	= JFactory::getUser( $userId );
		}

		if ($my->id) {
			
			jimport('joomla.filesystem.file');
			$file = JPATH_ADMINISTRATOR . '/components/com_easydiscuss/includes/easydiscuss.php';

			if (!JFile::exists($file)) {
				return false;
			}

			include_once($file);

			ED::points()->assign($action, $my->id);

			if ($title != '' && KT::getConfig()->get('enable_discuss_log')) {
				ED::history()->log($action, $userId, $title, 0);
			}

			return true;
		}
	}

	/**
	 * Purge all internal captcha in the database
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function clearCaptcha($days = '7')
	{
		$db = KT::db();

		$query = 'DELETE FROM ' . $db->nameQuote( '#__komento_captcha' ) . ' WHERE ' . $db->nameQuote( 'created' ) . ' <= DATE_SUB(NOW(), INTERVAL ' . $days . ' DAY)';

		$db->setQuery($query);
		$db->query();

		return $query;
	}

	/*
	General trigger function to trigger custom Komento events
	List of triggers:
		void onBeforeKomentoBar( int &$commentCount )
		void onBeforeKomentoBox( object &$system, object &$comments )
		bool onBeforeSaveComment object( &$comment )
		void onAfterSaveComment( object &$comment )
		void onBeforeProcessComment( object &$comment )
		void onAfterProcessComment( object &$comment )
		bool onBeforeSendNotification( object &$recipient )
		bool onBeforeDeleteComment( object &$comment )
		void onAfterDeleteComment( object &$comment )
		bool onBeforePublishComment( object &$comment )
		void onAfterPublishComment( object &$comment )
		bool onBeforeUnpublishComment( object &$comment )
		void onAfterUnpublishComment( object &$comment )
	*/
	public static function trigger($event, $params = [])
	{
		$config = KT::getConfig();
		$component = null;
		$cid = null;

		if (isset($params['component'])) {
			$component = $params['component'];
			unset($params['component']);
		}

		if (isset($params['cid'])) {
			$cid = $params['cid'];
			unset($params['cid']);
		}

		if ($config->get('trigger_method') === 'joomla') {

			static $plugin = false;

			if ($plugin === false) {
				$plugin = true;
				JPluginHelper::importPlugin('komento');
			}

			$application = JFactory::getApplication();

			$arguments = [];

			if (!empty($component)) {
				$arguments[] = $component;
			}

			if (!empty($cid)) {
				$arguments[] = $cid;
			}

			$arguments[] = &$params;
			$results = $application->triggerEvent($event, $arguments);

			if (is_array($results) && in_array(false, $results)) {
				return false;
			}

			return true;
		}

		if ($config->get('trigger_method') === 'component') {

			if (!$component) {
				return false;
			}

			$application = KT::loadApplication($component);

			if ($cid) {
				$application->load($cid);
			}

			return call_user_func_array(array($application, $event), $params);
		}

		return true;
	}

	public static function setMessage($msg, $type = 'notice')
	{
		KT::$messages[] = ['message' => $msg, 'type' => $type];
	}

	public static function getMessages($type = 'all')
	{
		if ($type === 'all') {
			return KT::$messages;
		} else {
			$filtered = [];

			foreach (KT::$messages as $message)	{
				if ($message['type'] === $type)	{
					$filtered[] = $message['message'];
				}
			}

			return $filtered;
		}
	}

	public static function getErrors()
	{
		return KT::getMessages('error');
	}

	public static function getPaidComponents()
	{
		return ['com_aceshop', 'com_flexicontent', 'com_hwdmediashare', 'com_jevents', 'com_k2', 'com_mtree', 'com_ohanah', 'com_redshop', 'com_sobipro', 'com_virtuemart', 'com_zoo'];
	}

	/**
	 * Get the paid features' setting
	 *
	 * @since	4.0
	 */
	public static function getPaidSettings()
	{
		$paidSettings = [
			'layout_appearance',
			'layout_accent',
			'layout_comment_placement',
			'bbcode_giphy',
			'bbcode_emoji',
			'giphy_enabled',
			'enable_live_notification',
			'layout_frontpage_instant_comment',
			'onesignal_enabled'
		];

		return $paidSettings;
	}

	/**
	 * Get's the database object.
	 *
	 * @since	3.0
	 */
	public static function db()
	{
		static $db = null;

		if (!$db) {
			$db = KT::database();
		}

		return $db;
	}

	/**
	 * Content formatter for the comments
	 *
	 * @since	3.0
	 * @access	public
	 */
	public static function formatter($type, $items, $options = [], $cache = true)
	{
		KT::import('admin:/includes/formatter/formatter');

		$formatter 	= new KomentoFormatter($type, $items, $options, $cache);

		return $formatter->execute();
	}

	/**
	 * Renders the pagination library
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function pagination($total, $limitstart, $limit)
	{
		$pagination = FH::pagination(KT::fd(), $total, $limitstart, $limit);

		return $pagination;
	}

	/**
	 * profiles class
	 *
	 * @since	3.0
	 * @access	public
	 */
	public static function profiles($profile, $type = 'default')
	{
		KT::import('admin:/includes/profiles/profiles');

		$profile = new KomentoProfiles($profile, $type);

		return $profile;
	}


	/**
	 * cache for comment related items.
	 *
	 * @since	3.0
	 * @access	public
	 */
	public static function cache()
	{
		static $cache = null;

		if (!$cache) {
			KT::import('admin:/includes/cache/cache');
			$cache = new KomentoCache();
		}

		return $cache;
	}

	public static function getThemeObject($name)
	{
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');

		$obj = new stdClass();
		$obj->element = $name;
		$obj->name = ucwords($name);
		$obj->path = KOMENTO_ROOT . '/themes/' . $obj->element;
		$obj->writable = is_writable($obj->path);
		$obj->version = '&mdash;';
		$obj->author = 'Stack Ideas';

		return $obj;
	}

	/**
	 * Synchronizes database versions
	 *
	 * @since   5.0
	 * @access  public
	 */
	public static function sync($from = '')
	{
		$db = KT::db();

		// List down files within the updates folder
		$path = KOMENTO_ADMIN_ROOT . '/updates';

		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');

		$scripts= array();

		if ($from) {
			$folders = JFolder::folders($path);

			if ($folders) {

				foreach ($folders as $folder) {

					// Because versions always increments, we don't need to worry about smaller than (<) versions.
					// As long as the folder is greater than the installed version, we run updates on the folder.
					// We cannot do $folder > $from because '1.2.8' > '1.2.15' is TRUE
					// We want > $from, NOT >= $from
					if (version_compare($folder, $from) === 1) {
						$fullPath = $path . '/' . $folder;

						// Get a list of sql files to execute
						$files = JFolder::files( $fullPath , '.json$' , false , true );

						foreach ($files as $file) {
							$data = json_decode(file_get_contents($file));
							$scripts = array_merge($scripts, $data);
						}
					}
				}
			}
		} else {

			$files = JFolder::files($path, '.json$', true, true);

			// If there is nothing to process, skip this
			if (!$files) {
				return false;
			}

			foreach ($files as $file) {
				$data = json_decode(file_get_contents($file));
				$scripts = array_merge($scripts, $data);
			}
		}

		if (!$scripts) {
			return false;
		}

		$tables = [];
		$indexes = [];
		$affected = 0;


		foreach ($scripts as $script) {

			$columnExist = true;
			$indexExist = true;

			if (isset($script->column)) {

				// Store the list of tables that needs to be queried
				if (!isset($tables[$script->table])) {
					$tables[$script->table] = $db->getColumns($script->table);
				}

				// Check if the column is in the fields or not
				$columnExist = in_array($script->column, $tables[$script->table]);
			}

			if (isset($script->index)) {

				// Get the list of indexes on a table
				if (!isset($indexes[$script->table])) {
					$indexes[$script->table] = $db->getIndexes($script->table);
				}

				$indexExist = in_array($script->index, $indexes[$script->table]);
			}

			if (!$columnExist || !$indexExist) {
				$db->setQuery($script->query);
				$db->Query();

				$affected += 1;
			}
		}

		return $affected;
	}

	/**
	 * Retrieves the view object.
	 *
	 * @since	3.0
	 * @access	public
	 * @param	string	The view's name.
	 * @param	bool	True for back end , false for front end.
	 */
	public static function view($name , $backend = true)
	{
		$className 	= 'KomentoView' . ucfirst( $name );

		if (!isset(self::$views[$className]) || (!self::$views[$className] instanceof KomentoView)) {

			if (!class_exists($className)) {
				$path = $backend ? KOMENTO_ADMIN_ROOT : KOMENTO_ROOT;
				$doc = JFactory::getDocument();
				$path .= '/views/' . strtolower($name) . '/view.' . $doc->getType() . '.php';

				if (!JFile::exists($path)) {
					return false;
				}

				// Include the view
				require_once($path);
			}

			if (!class_exists($className)) {
				throw FH::exception(JText::sprintf('View class not found: %1s' , $className), 500);
				return false;
			}

			self::$views[$className] = new $className([]);
		}

		return self::$views[$className];
	}

	/**
	 * Determine if this komento is paid version
	 *
	 * @since   4.0
	 * @access  public
	 */
	public static function isFreeVersion()
	{
		return KT_DOWNLOAD_PACKAGE === 'free';
	}

	/**
	 * Renders the stylesheet library
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function stylesheet($location)
	{
		KT::load('stylesheet');

		static $lib = null;

		if (is_null($lib)) {
			$lib = new KomentoStylesheet($location);
		}

		return $lib;
	}

	/**
	 * Renders the GIPHY library
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function giphy()
	{
		$giphy = FH::giphy(self::fd());

		return $giphy;
	}

	/**
	 * Renders the textavatar library
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function textavatar()
	{
		$config = self::config();

		$textavatar = FH::textavatar([
			'colors' => $config->get('layout_avatar_character_background_color'),
			'fontColor' => $config->get('layout_avatar_character_font_color')
		]);

		return $textavatar;
	}

	/**
	 * Reads a XML file.
	 *
	 * @since   4.0
	 * @access  public
	 */
	public static function getXml($data, $isFile = true)
	{
		$class = 'SimpleXMLElement';

		if ($isFile) {
			// Try to load the XML file
			$xml = simplexml_load_file($data, $class);

		} else {
			// Try to load the XML string
			$xml = simplexml_load_string($data, $class);
		}

		if ($xml === false) {
			foreach (libxml_get_errors() as $error) {
				echo "\t", $error->message;
			}
		}

		return $xml;
	}
}

// Include bootstrap file
if (!defined('KOMENTO_CLI')) {
	require_once(JPATH_ROOT . '/components/com_komento/bootstrap.php');
}

class KMT extends KT {}
class Komento extends KT {}
