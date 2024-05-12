<?php

namespace Nextend\SmartSlider3\Generator\Joomla\JoomlaContent\Elements;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Nextend\Framework\Form\Element\Select;


class JoomlaContentCategories extends Select {

    public function __construct($insertAt, $name = '', $label = '', $default = '', $parameters = array()) {
        parent::__construct($insertAt, $name, $label, $default, $parameters);

        $db = Factory::getDBO();

        $query = 'SELECT
                    id, 
                    title AS name, 
                    title, 
                    parent_id AS parent, 
                    parent_id
                FROM #__categories
                WHERE published = 1 AND extension = "com_content"
                ORDER BY lft';


        $db->setQuery($query);
        $menuItems = $db->loadObjectList();
        $children  = array();
        if ($menuItems) {
            foreach ($menuItems as $v) {
                $pt   = $v->parent_id;
                $list = isset($children[$pt]) ? $children[$pt] : array();
                array_push($list, $v);
                $children[$pt] = $list;
            }
        }

        $this->options[0] = n2_('All');

        jimport('joomla.html.html.menu');
        $options = HTMLHelper::_('menu.treerecurse', 1, '', array(), $children, 9999, 0, 0);
        if (count($options)) {
            foreach ($options as $option) {
                $this->options[$option->id] = $option->treename;
            }
        }
        if ($this->getValue() == '') {
            reset($this->options);
            $this->setValue(key($this->options));
        }

    }

}
