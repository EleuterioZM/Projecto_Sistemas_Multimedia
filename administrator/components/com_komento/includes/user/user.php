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

class KomentoUser
{
	public $name = null;
	public $username = null;
	public $email = null;
	public $avatar = null;
	public $link = null;
	protected $juser = null;
	protected $config = null;

	public function __construct($id = null)
	{
		$this->config = KT::config();
		$this->juser = JFactory::getUser($id);
		$this->app = JFactory::getApplication();

		$this->name = $this->juser->name;
		$this->username = $this->juser->username;
		$this->email = $this->juser->email;

		// if (empty($id)) {
		// 	$this->set('name', JText::_('COM_KOMENTO_GUEST'));
		// 	$this->set('username', JText::_('COM_KOMENTO_GUEST'));
		// }
	}

	/**
	 * Magic method to get properties which don't exist on this object but on the table
	 *
	 * @since   3.0
	 * @access  public
	 */
	public function __get($key)
	{
		if (property_exists($this->juser, $key)) {
			return $this->juser->$key;
		}

		if (property_exists($this, $key)) {
			return $this->$key;
		}

		return $this->juser->$key;
	}

	/**
	 * Magic method to set properties which don't exist on this object but on the table
	 *
	 * @since   3.0
	 * @access  public
	 */
	public function __set($key, $value = '')
	{
		if (property_exists('KomentoTableComments', $key)) {
			$this->table->$key = $value;
		} else {
			$this->$key = $value;
		}
	}

	/**
	 * Allows caller to initialize the property if this is a guest
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function setGuest($name, $email, $url)
	{
		$this->name = $name;
		$this->email = $email;
		$this->link = $url;
	}

	/**
	 * Static method to cache copies of users object
	 *
	 * @since	3.0
	 * @access	public
	 */
	public static function getUser($id = null, $debug = false)
	{
		// For guest user, we cannot cache the user object because there could be occurrences where
		// multiple guest post comments. We just need to use the KomentoUser to simulate postings
		if (!is_null($id) && $id == 0) {
			$user = new KomentoUser($id);
			return $user;
		}

		static $users = [];

		$juser = JFactory::getUser($id);
		$id = $juser->id;

		if (!isset($users[$id])) {
			$user = new KomentoUser($id);

			$users[$id] = $user;
		}

		return $users[$id];
	}

	/**
	 * Determines if the user is a site admin
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function isAdmin()
	{
		$isAdmin = $this->juser->authorise('core.admin');

		return $isAdmin;
	}

	/**
	 * Determines if the user is a site moderator
	 *
	 * @since	3.1
	 * @access	public
	 */
	public function isModerator()
	{
		if (!$this->juser->id) {
			return false;
		}

		// Site admin is always a moderator
		if (KT::isSiteAdmin($this->juser->id)) {
			return true;
		}

		// check for ACL moderation
		if ($this->allow('edit_all_comment') && $this->allow('delete_all_comment') && $this->allow('publish_all_comment') && $this->allow('unpublish_all_comment') && $this->allow('delete_all_attachment')) {
			return true;
		}

		return false;
	}

	/**
	 * Retrieves the name of the user
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function getName($name = '')
	{
		if ($this->config->get('guest_label') && !$this->id) {
			return JText::_('COM_KOMENTO_GUEST');
		}

		// For guests, we cannot use properties from this table
		if (!$this->id) {
			return $name;
		}

		// Name format from easysocial
		if ($this->config->get('name_type') == 'easysocial' && KT::easysocial()->exists()) {
			$user = ES::user($this->id);
			return $user->getName();
		}

		// Name format from easydiscuss
		if ($this->config->get('name_type') == 'easydiscuss' && KT::easydiscuss()->exists()) {
			$user = ED::user($this->id);
			return $user->getName();
		}

		// Name format from easyblog
		if ($this->config->get('name_type') == 'easyblog' && KT::easyblog()->exists()) {
			$user = EB::user($this->id);
			return $user->getName();
		}

		if ($this->config->get('name_type') == 'username') {
			return $this->username;
		}

		return $this->name;
	}

	/**
	 * Method to get initial name of the user
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function getNameInitial($defaultName = '')
	{
		$name = $this->getName();

		if ($this->guest) {
			$name = $defaultName;
		}

		$initial = new stdClass();
		$initial->text = '';
		$initial->code = '';

		$text = '';
		$max = 1;

		if (function_exists('mb_strlen')) {
			$max = mb_strlen($name) > 1 ? 2 : 1;
		}

		$segments = explode(' ', $name);

		if (count($segments) >= 2) {
			$tmp = [];
			$tmp[] = FCJString::substr($segments[0], 0, 1);
			$tmp[] = FCJString::substr($segments[count($segments) - 1], 0, 1);

			$text = implode('', $tmp);

			$initial->text = $text;
		} else {
			$initial->text = FCJString::substr($name, 0, $max);
		}

		$initial->text = strtoupper($initial->text);
		$text = $initial->text;

		// get the color code
		$initial->code = $this->getNameInitialCode($text);

		return $initial;
	}

	/**
	 * Method to get the color code based on initial name given
	 *
	 * @since	3.0
	 * @access	public
	 */
	private function getNameInitialCode($text)
	{
		if (!$this->id) {
			// guest always return 1;
			return '1';
		}

		$char = substr($text, 0, 1);
		$codes = [
			1 => ['A','B','C','D','E'],
			2 => ['F','G','H','I','J'],
			3 => ['K','L','M','N','O'],
			4 => ['P','Q','R','S','T'],
			5 => ['U','V','W','X','Y','Z']
		];


		foreach ($codes as $key => $sets) {
			if (in_array($char, $sets)) {
				return $key;
			}
		}

		// if nothing found, just return 1
		return '1';
	}

	/**
	 * Retrieves user's avatar
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function getAvatar($email = '')
	{
		static $avatars = [];

		$key = $this->id ? md5($this->id) : md5($email);

		if (!isset($avatars[$key])) {

			$email = $this->id ? $this->email : $email;

			$vendor = $this->getVendor();
			$source = $vendor->getAvatar($email);

			if (FH::isFromAdmin()) {
				$source = str_ireplace('/administrator/', '/', $source);
			}

			$avatars[$key] = $source;
		}

		return $avatars[$key];
	}

	/**
	 * Generate html for user avatar
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function getAvatarHtml($name = '', $email = '', $website = '', $size = 'md')
	{
		$options = [
			'permalink' => FH::escape($this->getProfileLink($email, $website)),
			'style' => $this->config->get('layout_avatar_style'),
			'name' => FH::escape($name)
		];

		if ($this->id && $this->config->get('layout_avatar_integration') === 'easysocial' && $this->config->get('easysocial_profile_popbox')) {
			$options['anchorAttributes'] = 'data-user-id="' . $this->id . '" data-popbox="module://easysocial/profile/popbox"';
		}
		
		$themes = KT::themes();
		$themes->set('user', $this);
		$themes->set('email', $email);
		$themes->set('website', $website);
		$themes->set('name', $name);
		$themes->set('size', $size);
		$themes->set('options', $options);

		$output = $themes->output('site/helpers/user/avatar');

		return $output;
	}

	/**
	 * Retrieves the profile link from this author
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function getProfileLink($email = '', $website = '', $debug = false)
	{
		$integration = $this->config->get('layout_avatar_integration');
		$textAvatar = $this->config->get('layout_avatar_character');

		// Normalize the url if there is a website property
		if ($website) {

			// Prefix with the protocol if the website doesn't contain a protocol
			if (stripos($website, 'http://') === false && stripos($website, 'https://') === false) {
				$website = 'http://' . $website;
			}
		}

		// For guests, we need to get the proper website links
		if (!$this->id) {

			if ($website) {
				return $website;
			}

			return 'javascript:void(0);';
		}

		static $links = [];

		if (isset($links[$this->id])) {
			return $links[$this->id];
		}

		if ($integration == 'default' || $textAvatar) {
			$link = 'javascript:void(0);';

			if ($website) {
				$link = $website;
			}

			$links[$this->id] = $link;
		} else {
			$links[$this->id] = $this->getVendor()->getLink($email, $website);
		}

		return $links[$this->id];
	}

	/**
	 * Easyblog integration compatibility
	 *
	 * @since   2.0
	 * @access  public
	 */
	public function getPermalink()
	{
		return $this->getProfileLink();
	}

	/**
	 * Retrieves the ranking of the user
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function getRankProgress()
	{
		static $ranks = [];

		if (!isset($ranks[$this->id])) {

			$model = KT::model('Comments');
			$totalComments = $model->getTotalComment();
			$userComments = $this->getCommentCount();

			$ranks[$this->id] = 0;

			if ($userComments) {
				$ranks[$this->id] = $userComments / $totalComments * 100;
			}
		}

		return $ranks[$this->id];
	}

	/**
	 * Retrieves the profile vendor
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function getVendor($name = '')
	{
		static $vendors	= [];

		$preferred = $this->config->get('layout_avatar_integration');

		$vendorName	= $name !== '' ? $name : $preferred;

		if (empty($vendors[$vendorName][$this->id])) {
			$vendor = KT::profiles($this, $preferred);

			$vendors[$vendorName][$this->id] = $vendor;
		}

		return $vendors[$vendorName][$this->id];
	}

	public function allow( $action = '', $component = '' )
	{
		// debug code. need to remove later when the acl refactoring complete.
		// return true;

		// We no longer define the ACL based on component
		// $component	= $component ? $component : KT::getCurrentComponent();

		return KT::ACL()->check($action, $this->id);
	}

	// no need to recurse because we don't deal with ACL usergroups inheritance
	public function getUsergroups( $recursive = false )
	{
		return JAccess::getGroupsByUser( $this->id, $recursive );
	}

	/**
	 * Determines if the user is allowed to upload files in the form
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function canUploadAttachments()
	{
		static $allowed = null;

		if (is_null($allowed)) {
			$allowed = false;
			$config = KT::config();

			if ($this->allow('upload_attachment') && $config->get('upload_enable')) {
				$allowed = true;
			}
		}

		return $allowed;
	}

	/**
	 * Determines if the user is allowed to delete comment from the user dashboard
	 *
	 * @since	3.1
	 * @access	public
	 */
	public function canDeleteComment()
	{
		static $allowed = null;

		if (is_null($allowed)) {
			$allowed = false;
			$config = KT::config();

			if ($this->allow('delete_own_comment')) {
				$allowed = true;
			}
		}

		return $allowed;
	}

	/**
	 * Determines if the user is allowed to share location
	 *
	 * @since	3.0
	 * @access	public
	 */
	public function canShareLocation()
	{
		static $allowed = null;

		if (is_null($allowed)) {
			$allowed = false;
			$location = KT::location();

			if ($location->isEnabled()) {
				$allowed = true;
			}
		}

		return $allowed;
	}

	public function getCommentCount()
	{
		$model = KT::model( 'comments' );

		$total = $model->getTotalComment( $this->id );

		return $total;
	}

	public function toModerate()
	{
		if (!$this->config->get('enable_moderation') && !$this->config->get('enable_auto_moderation')) {
			return false;
		}

		$moderationGroup = $this->config->get('requires_moderation', '');

		if (!is_array($moderationGroup)) {
			$moderationGroup = explode(',', $moderationGroup);
		}

		$usergid = $this->getUsergroups();
		$toModerate = false;

		// check if this user need to be moderated
		foreach($usergid as $gid) {
			if (in_array($gid, $moderationGroup)) {
				$toModerate = true;
				break;
			}
		}

		if (!$this->config->get('enable_auto_moderation')) {
			return $toModerate;
		}

		// we check if the user is in the threshold moderation 
		// and check if its exceed threshold setting
		$thresholdGroup = $this->config->get('requires_auto_moderation', '');

		if (!$thresholdGroup) {
			return $toModerate;
		}

		if (!is_array($thresholdGroup)) {
			$thresholdGroup = explode(',', $thresholdGroup);
		}

		$limit = $this->config->get('moderation_threshold');
		$model = KT::model('Users');

		foreach($usergid as $gid) {
			if (in_array($gid, $thresholdGroup)) {
				// if coming here, user is included in the group that need to be moderated by threshold
				// we assume the user has to be moderated unless proved otherwise
				$toModerate = true;

				// If exceeded, they shouldn't be moderated
				if ($model->exceededModerationThreshold($this->id, $limit)) {
					$toModerate = false;
				}
				
				break;
			}
		}

		return $toModerate;
	}
}
