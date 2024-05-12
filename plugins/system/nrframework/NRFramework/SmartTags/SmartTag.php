<?php

/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace NRFramework\SmartTags;

defined('_JEXEC') or die('Restricted access');

use Joomla\Registry\Registry;

abstract class SmartTag
{
	/**
	 * Factory Class
	 *
	 * @var object
	 */
    protected $factory;

    /**
	 * Joomla Application object
	 *
	 * @var object
     */
    protected $app;

    /**
	 * Joomla Document
	 *
	 * @var object
     */
    protected $doc;

    /**
     * Useful data used by a Smart Tag
     * 
     * @var  array
     */
    protected $data;

    /**
     * Smart Tags Configuration Options
     * 
     * @var  array
     */
    protected $options;

    public function __construct($factory = null, $options = null)
    {
        if (!$factory)
        {
            $factory = new \NRFramework\Factory();
        }
        $this->factory = $factory;
        
		$this->app = $this->factory->getApplication();
        $this->doc = $this->factory->getDocument();

        $this->parsedOptions = isset($options['options']) ? $options['options'] : new Registry();

        $this->options = $options;
    }

    /**
     * Set the data
     * 
     * @param   array  $data
     * 
     * @return  void
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * This method runs before replacements and determines whether the class can be executed and do replacements or not.
     * 
     * THE PROBLEM: 
     * 
     * Let's say we have a bunch of Smart Tags in a namespaced folder and we register them using the register() method. 
     * The Smart Tags include, Foo and Bar. Let's say our replacement subject is: 'lorem {foo.x} ipsum {foo.y} lorem ipsum {bar.x}' 
     * and we'd like to replace {foo.x} and {foo.y} and leave {bar.x} untouched. Right now this is not possible. 
     * All 3 Smart Tags will be replaced in the subject because all classes are already registered.
     * 
     * This problem occurs also in Convert Forms during form rendering. When a form is using Calculations, it's very likely 
     * a calculation formula in the form {field.XXX} + {field.YYY} is included in the form's HTML layout. 
     * In Convert Forms, Smart Tag replacements run during page load. Since we have a Smart Tag for Fields {field.XXX} already registered, 
     * the Smart Tags found in the Calculations formula will be replaced by empty space (there's no submitted data yet) breaking Calculation. 
     * 
     * We need a way to determine during runtime whether a Smart Tag can run or not.
     * 
     * We could write a new method so 3rd party extension can register individual classes conditionally but this would add more work on the extension's side.
     * 
     * @return boolean 
     */
    public function canRun()
    {
        return true;
    }
}