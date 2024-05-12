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

class KomentoDatabase
{
	public $helper = null;

	public function __construct()
	{
		$this->helper 	= new KomentoDatabaseJoomla();
	}

	public function __call( $method , $args )
	{
		$refArray	= array();

		if( $args )
		{
			foreach( $args as &$arg )
			{
				$refArray[]	=& $arg;
			}
		}

		return call_user_func_array( array( $this->helper , $method ) , $refArray );
	}

	public function getTables()
	{
		$db = KT::db();
		$db->setQuery('SHOW TABLES');
		$result = $db->loadResultArray();

		return $result;
	}

	public function getColumns($tableName)
	{
		$db = KT::db();
		$query = 'SHOW FIELDS FROM ' . $db->nameQuote($tableName);
		$db->setQuery($query);

		$result = $db->loadObjectList();

		$fields = [];

		foreach($result as $column) {
			$fields[] = $column->Field;
		}

		return $fields;
	}

	public function getIndexes( $tableName )
	{
		$db = KT::db();
		$query	= 'SHOW INDEX FROM ' . $db->nameQuote( $tableName );
		$db->setQuery( $query );

		$result	= $db->loadObjectList();

		$indexes = array();

		foreach ($result as $row) {
			$indexes[] = $row->Key_name;
		}

		return $indexes;
	}


	public function isTableExists( $tableName )
	{
		$db = KT::db();
		$query	= 'SHOW TABLES LIKE ' . $db->quote($tableName);
		$db->setQuery( $query );

		return (boolean) $db->loadResult();
	}

	public function isColumnExists( $tableName, $columnName )
	{
		$db = KT::db();
		$query	= 'SHOW FIELDS FROM ' . $db->nameQuote( $tableName );
		$db->setQuery( $query );

		$fields	= $db->loadObjectList();

		$result = array();

		foreach( $fields as $field )
		{
			$result[ $field->Field ]	= preg_replace( '/[(0-9)]/' , '' , $field->Type );
		}

		if( array_key_exists($columnName, $result) )
		{
			return true;
		}

		return false;
	}

	public function isIndexKeyExists( $tableName, $indexName )
	{
		$db = KT::db();
		$query	= 'SHOW INDEX FROM ' . $db->nameQuote( $tableName );
		$db->setQuery( $query );
		$indexes	= $db->loadObjectList();

		$result = array();

		foreach( $indexes as $index )
		{
			$result[ $index->Key_name ]	= preg_replace( '/[(0-9)]/' , '' , $index->Column_name );
		}

		if( array_key_exists($indexName, $result) )
		{
			return true;
		}

		return false;
	}

	/**
	 * Helper to load our own sql string helper.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function sql()
	{
		$sql = KT::sql();

		return $sql;
	}

	/**
	 * Override JDatabase setQuery behavior
	 *
	 * @since   4.0.0
	 * @access  public
	*/
	public function setQuery($query, $offset = 0, $limit = 0)
	{
		$db = JFactory::getDBO();

		if (is_array($query)) {
			$query = implode(' ', $query);
		}

		if ($query instanceof KomentoSql) {
			$query = $query->__toString();
		}

		return call_user_func_array([$db, __FUNCTION__] , [$query, $offset, $limit]);
	}

	/**
	 * Override JDatabase getErrorNum method
	 *
	 * @since   4.0.0
	 * @access  public
	*/
	public function getErrorNum()
	{
		$db = JFactory::getDBO();

		if (method_exists($db, 'getErrorNum')) {
			return $db->getErrorNum();
		}

		return $db->getConnection()->errno;
	}

	/**
	 * Override query method
	 *
	 * @since   4.0.0
	 * @access  public
	 */
	public function query()
	{
		$db = JFactory::getDBO();

		return $db->execute();
	}

	/**
	 * Determine if mysql can support utf8mb4 or not.
	 *
	 * @since   4.0.0
	 * @access public
	 */
	public function hasUTF8mb4Support()
	{
		static $_cache = null;

		if (is_null($_cache)) {
			$db = JFactory::getDBO();

			if (method_exists($db, 'hasUTF8mb4Support')) {
				$_cache = $db->hasUTF8mb4Support();
				return $_cache;
			}

			// we need to check server version 1st.
			$server_version = $db->getVersion();
			if (version_compare($server_version, '5.5.3', '<')) {
				 $_cache = false;
				 return $_cache;
			}

			// now we check for client version
			$client_version = '5.0.0';

			if (function_exists('mysqli_get_client_info')) {
				$client_version = mysqli_get_client_info();
			} else if (function_exists('mysql_get_client_info')) {
				$client_version = mysql_get_client_info();
			}

			if (strpos($client_version, 'mysqlnd') !== false) {
				$client_version = preg_replace('/^\D+([\d.]+).*/', '$1', $client_version);
				$_cache = version_compare($client_version, '5.0.9', '>=');
			} else {
				$_cache = version_compare($client_version, '5.5.3', '>=');
			}
		}

		return $_cache;
	}
}

class KomentoDatabaseJoomla extends KomentoDatabase
{
	public $db = null;

	public function __construct()
	{
		$this->db	= JFactory::getDBO();
	}

	public function loadColumn()
	{
		return $this->loadResultArray();
	}

	public function loadResultArray()
	{
		return $this->db->loadColumn();
	}

	public function nameQuote($str)
	{
		return $this->db->quoteName($str);
	}

	public function __call($method, $args)
	{
		$refArray	= [];

		if ($args) {
			foreach($args as &$arg) {
				$refArray[] =& $arg;
			}
		}

		return call_user_func_array([$this->db, $method], $refArray);
	}
}