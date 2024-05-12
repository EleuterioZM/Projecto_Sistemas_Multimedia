<?php

/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

namespace NRFramework;

use NRFramework\Factory;
use NRFramework\Conditions\ConditionsHelper;

defined('_JEXEC') or die;

// @deprecated  - Use \NRFramework\Condnitions\ConditionsHelper;
class Assignments
{
    /**
	 *  Assignment Type Aliases
	 *
	 *  @var  array
     * 
     *  @deprecated To be removed on Jan 1st 2023.
	 */
	public $typeAliases = array(
		'device|devices'                     => 'Device',
		'urls|url'                           => 'URL',
		'os'			                     => 'OS',
		'browsers|browser'		             => 'Browser',
		'referrer'                           => 'Referrer',
		'php'                                => 'PHP',
		'timeonsite'                         => 'TimeOnSite',
		'pageviews|user_pageviews'           => 'Pageviews',
		'lang|language|languages'            => 'Joomla\Language',
		'usergroups|usergroup|user_groups'   => 'Joomla\UserGroup',
		'user_id|userid'		             => 'Joomla\UserID',
		'menu'                               => 'Joomla\Menu',
        'components|component'	             => 'Joomla\Component',
        'datetime|daterange|date'            => 'Date\Date',
        'weekday|days|day'                   => 'Date\Day',
        'months|month'                       => 'Date\Month',
		'timerange|time'                     => 'Date\Time',
        'acymailing'                         => 'AcyMailing',
        'akeebasubs'                         => 'AkeebaSubs',
        'engagebox|onotherbox'               => 'EngageBox',
        'convertforms'	                     => 'ConvertForms',
        'geo_country|country|countries'	     => 'Geo\Country',
        'geo_continent|continent|continents' => 'Geo\Continent',
        'geo_city|city|cities'               => 'Geo\City',
        'geo_region|region|regions'          => 'Geo\Region',
        'cookiename|cookie'                  => 'Cookie',
        'ip_addresses|iprange|ip'            => 'IP',
        'k2_items|k2item'                    => 'Component\K2Item',
        'k2_cats|k2category'                 => 'Component\K2Category',
        'k2_tags|k2tag'                      => 'Component\K2Tag',
        'k2_pagetypes|k2pagetype'            => 'Component\K2Pagetype',
        'contentcats|category'               => 'Component\ContentCategory',
        'contentarticles|article'            => 'Component\ContentArticle',
        'contentview'                        => 'Component\ContentView',
        'eventbookingsingle'                 => 'Component\EventBookingSingle',
        'eventbookingcategory'               => 'Component\EventBookingCategory',
        'j2storesingle'                      => 'Component\J2StoreSingle',
        'j2storecategory'                    => 'Component\J2StoreCategory',
        'hikashopsingle'                     => 'Component\HikashopSingle',
        'hikashopcategory'                   => 'Component\HikashopCategory',
        'sppagebuildersingle'                => 'Component\SPPageBuilderSingle',
        'sppagebuildercategory'              => 'Component\SPPageBuilderCategory',
        'virtuemartcategory'                 => 'Component\VirtueMartCategory',
        'virtuemartsingle'                   => 'Component\VirtueMartSingle',
        'jshoppingsingle'                    => 'Component\JShoppingSingle',
        'jshoppingcategory'                  => 'Component\JShoppingCategory',
        'rsblogsingle'                       => 'Component\RSBlogSingle',
        'rsblogcategory'                     => 'Component\RSBlogCategory',
        'easyblogcategory'                   => 'Component\EasyBlogCategory',
        'easyblogsingle'                     => 'Component\EasyBlogSingle',
        'zoosingle'                          => 'Component\ZooSingle',
        'zoocategory'                        => 'Component\ZooCategory',
        'eshopcategory'                      => 'Component\EshopCategory',
        'eshopsingle'                        => 'Component\EshopSingle',
        'djcatalog2category'                 => 'Component\DJCatalog2Category',
        'djcatalog2single'                   => 'Component\DJCatalog2Single',
        'quixsingle'                         => 'Component\QuixSingle',
        'djclassifiedssingle'                => 'Component\DJClassifiedsSingle',
        'djclassifiedscategory'              => 'Component\DJClassifiedsCategory',
        'sobiprocategory'                    => 'Component\SobiProCategory',
        'sobiprosingle'                      => 'Component\SobiProSingle',
        'gridboxcategory'                    => 'Component\GridboxCategory',
        'gridboxsingle'                      => 'Component\GridboxSingle',
        'djeventscategory'                   => 'Component\DJEventsCategory',
        'djeventssingle'                     => 'Component\DJEventsSingle',
        'jcalprocategory'                    => 'Component\JCalProCategory',
        'jcalprosingle'                      => 'Component\JCalProSingle',
        'jbusinessdirectorybusinesscategory' => 'Component\JBusinessDirectoryBusinessCategory',
        'jbusinessdirectorybusinesssingle'   => 'Component\JBusinessDirectoryBusinessSingle',
        'jbusinessdirectoryeventcategory'    => 'Component\JBusinessDirectoryEventCategory',
        'jbusinessdirectoryeventsingle'      => 'Component\JBusinessDirectoryEventSingle',
        'jbusinessdirectoryoffercategory'    => 'Component\JBusinessDirectoryOfferCategory',
        'jbusinessdirectoryoffersingle'      => 'Component\JBusinessDirectoryOfferSingle'
    );

    /**
     *  Factory object 
     * 
     *  @var \NRFramework\Factory
     */
    protected $factory;

    /**
     *  Class constructor
     */
    public function __construct($factory = null)
    {
        $this->factory = is_null($factory) ? new Factory() : $factory;
    }

    /**
	 *  Legacy method to check a set of rules. 
     * 
     *  At the moment of writing this and the moment we're going to release a new version for EngageBox 
     *  which is going to introduce the new ConditionBuilder field, ACF and GSD will still be using the passAll() method. 
     * 
     *  This forces us to keep this method for backwards compatibiliy reasons.
     *  Additionally, it helps us to catch a special case where both ACF and GSD expect to pass all rules even if the array passed is null.
	 *
	 *  @param   array|object   $assignments_info   Array/Object containing assignment info
	 *  @param   string         $match_method       The matching method (and|or) - Deprecated
	 *  @param   bool           $debug              Set to true to request additional debug information about assignments
     *
     *  @deprecated Use passSets() instead. To be removed on Jan 1st 2023
	 */
	public function passAll($assignments_info, $match_method = 'and')
	{
        $assignments = $this->prepareAssignments($assignments_info, $match_method);

        $ch = new ConditionsHelper($this->factory);
        $pass = $ch->passSets($assignments);

        // If the checks return null, consider this as Success. This is required for both ACF and GSD.
        return is_null($pass) ? true : $pass;
    }

    /**
     *  Returns the classname for a given assignment alias
     *
     *  @param  string       $alias
     *  @return string|void
     * 
     *  @deprecated To be removed on Jan 1st 2023
     */
    public function aliasToClassname($alias)
    {
        $alias = strtolower($alias);
        foreach ($this->typeAliases as $aliases => $type)
        {
            if (strtolower($type) == $alias)
            {
                return $type;
            }

            $aliases = explode('|', strtolower($aliases));
            if (in_array($alias, $aliases))
            {
                return $type;                
            }   
        }

        return null;
    }

    /**
     *  Checks and prepares the given array of assignment information
     * 
     *  @param   array $assignments_info
     *  @return  array
     * 
     *  @deprecated To be removed on Jan 1st 2023
     */
    protected function prepareAssignments($data, $matching_method = 'all')
    {
        if (is_object($data))
        {
            return $this->prepareAssignmentsFromObject($data, $matching_method);
        }

        if (!is_array($data) OR empty($data)) 
        {
            return;
        }

        $rules = array_pop($data);

        if (!is_array($rules) OR empty($rules)) 
        {
            return;
        }

        foreach ($rules as &$rule)
        {
            if (is_array($rule))
            {
                foreach ($rule as &$_rule)
                {
                    $_rule = $this->prepareAssignmentRule($_rule);
                }
            }
            else
            {
                $rule = $this->prepareAssignmentRule($rule);
            }
        }

        $data = [
            [
                'matching_method' => $matching_method == 'and' ? 'all' : 'any',
                'rules' => $rules
            ]
        ];

        return $data;
    }

    /**
     * Prepares the assignment rule.
     * 
     * @param   object  $rule
     * 
     * @return  object
     */
    private function prepareAssignmentRule($rule)
    {
        return [
            'name'     => $this->aliasToClassname($rule->alias),
            'operator' => (int) $rule->assignment_state == 1 ? 'includes' : 'not_includes',
            'value'    => isset($rule->value) ? $rule->value : null,
            'params'   => isset($rule->params) ? $rule->params : null,
        ];
    }

    /**
     *  Converts an object of assignment information to an array of groups
     *  Used by existing extensions
     * 
     *  @param  object $assignments_info
     *  @param  string $matching_method
     * 
     *  @deprecated To be removed on Jan 1st 2023
     */
    public function prepareAssignmentsFromObject($assignments_info, $matching_method)
    {
        if (!isset($assignments_info->params))
        {
            return [];
        }

        $params = json_decode($assignments_info->params);

        if (!is_object($params))
        {
            return [];
        }

        $assignments_info = [];
        
        foreach ($this->typeAliases as $aliases => $type)
        {
            $aliases = explode('|', $aliases);

            foreach ($aliases as $alias)
            {
                if (!isset($params->{'assign_' . $alias}) || !$params->{'assign_' . $alias})
                {
                    continue;
                }

                // Discover assignment params
                $assignment_params = new \stdClass();
                foreach ($params as $key => $value)
                {
                    if (strpos($key, "assign_" . $alias . "_param") !== false)
                    {
                        $key = str_replace("assign_" . $alias . "_param_", "", $key);
                        $assignment_params->$key = $value;
                    }
                }

                $assignments_info[] = [
                    'name'      => $this->aliasToClassname($alias),
                    'operator'  => (int) $params->{'assign_' . $alias} == 1 ? 'includes' : 'not_includes',
                    'value'     => isset($params->{'assign_' . $alias . '_list'}) ? $params->{'assign_' . $alias . '_list'} : [],
                    'params'    => $assignment_params
                ];
            }
        }

        $data = [
            [
                'matching_method' => $matching_method == 'and' ? 'all' : 'any',
                'rules' => $assignments_info
            ]
        ];

        return $data;
    }
}