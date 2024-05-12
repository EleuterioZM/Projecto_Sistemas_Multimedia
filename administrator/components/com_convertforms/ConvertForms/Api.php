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

/**
 *  This is the main Convert Forms API helper class meant to be used ONLY by 3rd party developers and advanced users. 
 *  Do not ever use this class to implement and rely any core feture.
 */
class Api
{
    /**
     * Delete a submission from the database
     *
     * @param  integer $id  The submission's primary key
     *
     * @return bool True on success
     */
    public static function removeSubmission($submission_id)
    {
        if (!$submission_id)
        {
            return;
        }

		\JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_convertforms/tables');
        $table = \JTable::getInstance('Conversion', 'ConvertFormsTable');
        return $table->delete($submission_id);
    }

    /**
     * Delete all form submissions from the database
     *
     * @param  integer $form_id     The form's primary key
     *
     * @return bool
     */
    public static function removeFormSubmissions($form_id)
    {
        if (!$form_id)
        {
            return;
        }

        $db = \JFactory::getDbo();

        $query = $db->getQuery(true)
            ->delete($db->quoteName('#__convertforms_conversions'))
            ->where($db->quoteName('form_id') . ' = ' . $form_id);
        
        $db->setQuery($query);
        
        return $db->execute();
    }

    /**
     * Return all form submissions
     *
     * @param   integer $form_id    The form's ID
     *
     * @return  Object
     */
    public static function getFormSubmissions($form_id)
    {
        if (!$form_id)
        {
            return;
        }

        $db = \JFactory::getDbo();

        $query = $db->getQuery(true)
            ->select('*')
            ->from($db->quoteName('#__convertforms_conversions'))
            ->where($db->quoteName('form_id') . ' = ' . $form_id);
    
        $db->setQuery($query);
        
        $rows = $db->loadObjectList();

        foreach ($rows as $key => $row)
        {
            $row->params = json_decode($row->params);
        }

        return $rows;
    }

    /**
     * Return the total number of form total submissions
     *
     * @param   integer $form_id    The form's ID
     *
     * @return  integer
     */
    public static function getFormSubmissionsTotal($form_id)
    {
        return number_format(Form::getSubmissionsTotal($form_id));
    }

    /**
     * Get the visitor's device type: desktop, tablet, mobile
     *
     * @return string
     */
    public static function getDeviceType()
    {
        return \NRFramework\WebClient::getDeviceType();
    }

    /**
     * Indicate if the visitor is browsing the site via a mobile
     *
     * @return bool
     */
    public static function isMobile()
    {
        return self::getDeviceType() == 'mobile';
    }
}