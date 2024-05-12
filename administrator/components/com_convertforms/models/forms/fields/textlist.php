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

JFormHelper::loadFieldClass('text');

class JFormFieldTextList extends JFormFieldText
{
    /**
     * Method to get a list of options for a list input.
     *
     * @return      array           An array of JHtml options.
     */
    protected function getInput()
    {
        if (!empty($this->value) && !is_array($this->value))
        {
            $this->value = (array) $this->value;
        }

        // Add brackets to field name to accept multiple values
        $this->name .= '[]';

        $html = '<div class="input_item_list"><div class="input_items">';

        if (is_array($this->value) && !empty($this->value))
        {
            foreach ($this->value as $key => $item)
            {
                $this->value = $item;
                $html .= '<div>' . parent::getInput() . '<button class="btn btn-mini remove_item"><span class="icon-minus"></button></div>';
            }
        }

        $html .= '</div>';

        // Add an extra input for template needs
        $this->value = '';
        $this->disabled = true;
        $html .= '<div class="input_item_list_tmpl"> ' . parent::getInput() . '<button class="btn btn-mini remove_item"><span class="icon-minus"></button></div>';

        $html .= '<button class="btn btn-mini btn-success add_item"><span class="icon-plus"></span></button></div>';

        static $run;

        if (!$run)
        {
            $this->addMedia();
            $run = true;
        }

        return $html;
    }

    private function addMedia()
    {
        // Add CSS
        JFactory::getDocument()->addStyleDeclaration('
            .input_item_list .input_item_list_tmpl {
                display:none;
            }
            .input_item_list div div {
                margin-bottom:5px;
                display:flex;
                display:-webkit-flex;
                align-items:center;
                -webkit-align-items:center;
            }
            .input_item_list input {
                margin-right:5px;
            }
            .input_item_list *[class^="icon"] {
                margin:0;
                pointer-events: none;
            }
        ');

        // Add Script
        JFactory::getDocument()->addScriptDeclaration('
            document.addEventListener("DOMContentLoaded", function(e) {
                var els = document.querySelectorAll(".input_item_list");

                els.forEach(function(el) {
                    el.addEventListener("click", function(e) {
                        e.preventDefault();
                        
                        // Remove item action
                        if (e.target.classList.contains("remove_item")) {
                            var button = e.target;
                            var container = button.closest(".input_items");

                            container.removeChild(button.parentNode);
                        }

                        // Add new item action
                        if (e.target.classList.contains("add_item")) {
                            
                            var el_tmpl = el.querySelector(".input_item_list_tmpl");
                            var cln = el_tmpl.cloneNode(true);

                            cln.querySelector("input").disabled = false;
                            cln.classList.remove("input_item_list_tmpl");
                            
                            el.querySelector(".input_items").appendChild(cln);
                        }
                    });
                });
            });
        ');
    }
}