<?php

/**
 * @package         Convert Forms
 * @version         3.2.8 Free
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2019 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace ConvertForms;

defined('_JEXEC') or die('Restricted access');

class SubmissionMeta
{
    /**
     *  Logs table
     *
     *  @var  string
     */
    private static $table = '#__convertforms_submission_meta';

    /**
     * Adds a Submission Meta.
     * 
     * @param   int     $submission_id
     * @param   string  $type
     * @param   string  $key
     * @param   string  $value
     * @param   array   $params
     * 
     * @return  void
     */
    public static function add($submission_id, $type, $key = '', $value, $params = [])
    {
        if (!$submission_id || !$type || !$value)
        {
            return;
        }

        // Data to save
        $data = (object) [
            'submission_id' => $submission_id,
            'meta_type'     => $type,
            'meta_key'      => $key,
            'meta_value'    => $value,
            'params'        => json_encode($params),
            'date_created'  => \JFactory::getDate()->toSql()
        ];

        // Insert the data
        try
        {
            \JFactory::getDbo()->insertObject(self::$table, $data);
        } 
        catch (Exception $e)
        {
        }
    }

    /**
     * Retrieves the meta row.
     * 
     * @param   int     $submission_id
     * @param   string  $type
     * @param   string  $key
     * 
     * @return  mixed
     */
    public static function getMeta($submission_id, $type, $key = '')
    {
        if (!$submission_id || !$type)
        {
            return;
        }

        $db = \JFactory::getDbo();
        $query = $db->getQuery(true);
        $query
            ->select('*')
            ->from($db->quoteName(self::$table))
            ->where($db->quoteName('submission_id') . ' = ' . $db->quote($submission_id))
            ->where($db->quoteName('meta_type') . ' = ' . $db->quote($type));

        if (!empty($key))
        {
            $query->where($db->quoteName('meta_key') . ' = ' . $db->quote($key));
        }
         
        $db->setQuery($query);

        return $db->loadAssoc();
    }

    /**
     * Retrieves meta value.
     * 
     * @param   int     $submission_id
     * @param   string  $type
     * @param   string  $key
     * 
     * @return  string
     */
    public static function getValue($submission_id, $type, $key = '')
    {
        if (!$data = self::getMeta($submission_id, $type, $key))
        {
            return;
        }

        return $data['meta_value'];
    }

    /**
     * Deletes a submission meta
     * 
     * @param   int     $submission_id
     * @param   string  $type
     * @param   string  $key
     * 
     * @return  void
     */
    public static function delete($submission_id, $type, $key = '')
    {
        if (!$submission_id || !$type)
        {
            return;
        }

        $db = \JFactory::getDbo();

        $query = $db->getQuery(true)
            ->delete($db->quoteName(self::$table))
            ->where($db->quoteName('submission_id') . ' = ' . $db->quote($submission_id))
            ->where($db->quoteName('meta_type') . ' = ' . $db->quote($type));

        if (!empty($key))
        {
            $query->where($db->quoteName('meta_key') . ' = ' . $db->quote($key));
        }
        
        $db->setQuery($query);
        
        return $db->execute();
    }

    /**
     * Deletes a list of submission meta by ID
     * 
     * @param   array   $ids
     * 
     * @return  void
     */
    public static function deleteAll($ids)
    {
        if (!is_array($ids))
        {
            return;
        }

        $db = \JFactory::getDbo();

        $query = $db->getQuery(true)
            ->delete($db->quoteName(self::$table))
            ->where($db->quoteName('id') . ' IN (' . implode(', ', (array) $ids) . ')');
        
        $db->setQuery($query);
        
        return $db->execute();
    }
}