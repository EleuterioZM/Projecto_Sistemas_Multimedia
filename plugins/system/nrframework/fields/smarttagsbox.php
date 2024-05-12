<?php

/**
 * @package         Convert Forms
 * @version         3.2.8 Free
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2022 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('_JEXEC') or die('Restricted access');

use NRFramework\SmartTags;

class JFormFieldSmartTagsBox extends JFormField
{
    /**
     * Undocumented variable
     *
     * @var string
     */
    public $input_selector = '.show-smart-tags';

    /**
     *  Disable field label
     *
     *  @return  boolean
     */
    protected function getLabel()
    {
        return false;
    }

    /**
     * Method to get a list of options for a list input.
     *
     * @return  array   An array of JHtml options.
     */
    protected function getInput()
    {
        JHtml::_('script', 'plg_system_nrframework/smarttagsbox.js', ['version' => 'auto', 'relative' => true]);
        JHtml::_('stylesheet', 'plg_system_nrframework/smarttagsbox.css', ['version' => 'auto', 'relative' => true]);

        JText::script('NR_SMARTTAGS_NOTFOUND');
        JText::script('NR_SMARTTAGS_SHOW');

        JFactory::getDocument()->addScriptOptions('SmartTagsBox', [
            'selector' => $this->input_selector,
            'tags'     => [
                'Joomla' => [
                    '{page.title}'     => 'Page Title',
                    '{url}'            => 'Page URL',
                    '{url.path}'       => 'Page Path',
                    '{page.lang}'      => 'Page Language Code',
                    '{page.langurl}'   => 'Page Language URL',
                    '{page.desc}'      => 'Page Meta Description',
                    '{site.name}'      => 'Site Name',
                    '{site.url}'       => 'Site URL',
                    '{site.email}'     => 'Site Email',
                    '{user.id}'        => 'User ID',
                    '{user.username}'  => 'User Username',
                    '{user.email}'     => 'User Email',
                    '{user.name}'      => 'User Full name',
                    '{user.firstname}' => 'User First name',
                    '{user.lastname}'  => 'User Last name',
                    '{user.groups}'    => 'User Group IDs',
                    '{user.registerdate}' => 'User Registration Date',
                ],
                'Visitor' => [
                    '{client.device}'    => 'Visitor Device Type',  
                    '{ip}'               => 'Visitor IP Address',
                    '{client.browser}'   => 'Visitor Browser',
                    '{client.os}'        => 'Visitor Operating System',
                    '{client.useragent}' => 'Visitor User Agent String',
                    '{client.id}'        => JText::_('NR_TAG_CLIENTID'),
                    '{geo.country}'      => JText::_('NR_TAG_GEOCOUNTRY'),
                    '{geo.countrycode}'  => JText::_('NR_TAG_GEOCOUNTRYCODE'),
                    '{geo.city}'         => JText::_('NR_TAG_GEOCITY'),
                    '{geo.location}'     => JText::_('NR_TAG_GEOLOCATION'),
                ],
                'Other' => [
                    '{date}'  => 'Date',
                    '{time}'  => 'Time',
                    '{day}'   => 'Day',
                    '{month}' => 'Month',
                    '{year}'  => 'Year',
                    '{referrer}' => 'Referrer URL',
                    '{randomid}' => 'Random ID',
                    '{querystring.YOUR_KEY}' => 'Query String',
                    '{language.YOUR_KEY}' => 'Language String'
                ]
            ]
        ]);

        // Render box layout
        $layout = new JLayoutFile('smarttagsbox', JPATH_PLUGINS . '/system/nrframework/layouts');
        return $layout->render();
    }
}