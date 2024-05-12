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

jimport('joomla.database.table');

class ConvertFormsTableForm extends JTable
{
    /**
     * Constructor
     *
     * @param object Database connector object
     */
    function __construct(&$db) 
    {
    	$this->setColumnAlias('published', 'state');
    	$this->created = JFactory::getDate()->toSql();

        parent::__construct('#__convertforms', 'id', $db);
    }
}