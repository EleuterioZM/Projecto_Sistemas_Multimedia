<?php
/**
 *  @author          Tassos.gr <info@tassos.gr>
 *  @link            http://www.tassos.gr
 *  @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 *  @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

namespace NRFramework;

defined('_JEXEC') or die('Restricted access');

/**
 *  Helper class to work with continent names/codes
 */
class Continents
{
    /**
     *  Return a continent code from it's name
     *
     *  @param  string $cont
     *  @return string|void
     */
    public static function getCode($cont)
    {
        $cont = \ucwords(strtolower($cont));
        foreach (self::getContinentsList() as $key => $value)
        {
            if (strpos($value, $cont) !== false)
            {
                return $key;
            }
        }
        return null;
    }

    /**
     * Returns a list of continents
     * 
     * @return  array
     */
    public static function getContinentsList()
    {
        return [
            'AF' => \JText::_('NR_CONTINENT_AF'),
            'AS' => \JText::_('NR_CONTINENT_AS'),
            'EU' => \JText::_('NR_CONTINENT_EU'),
            'NA' => \JText::_('NR_CONTINENT_NA'),
            'SA' => \JText::_('NR_CONTINENT_SA'),
            'OC' => \JText::_('NR_CONTINENT_OC'),
            'AN' => \JText::_('NR_CONTINENT_AN'),
        ];
    }
}