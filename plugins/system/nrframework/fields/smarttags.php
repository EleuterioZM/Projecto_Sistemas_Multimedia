<?php

/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

// No direct access to this file
defined('_JEXEC') or die;

require_once dirname(__DIR__) . "/helpers/smarttags.php";
require_once dirname(__DIR__) . '/helpers/field.php';

class JFormFieldNR_SmartTags extends NRFormField
{
    /**
     *  Method to render the input field
     *
     *  @return  string  
     */
    function getInput()
    {  
        $smartTags = new NRSmartTags();

        // Add extra tags by calling an external method
        if ($tagsMethod = $this->get("tagsMethod", null))
        {
            $method = explode("::", $tagsMethod);

            if (is_array($method))
            {
                $extraTags = call_user_func($method);
                $smartTags->add($extraTags);     
            }
        }

    	$tags = $smartTags->get();

        if (!$tags || !is_array($tags))
        {
            return;
        }

    	$html[] = '
    		<div class="nrSmartTags">
			    <a class="nrst-btn" 
                    href="#" 
                    data-show-label="' . JText::_("NR_SMARTTAGS_SHOW") . '" 
                    data-hide-label="' . JText::_("NR_SMARTTAGS_HIDE") . '">
                        <span class="icon icon-tag"></span>
                        <span class="l">' . $this->prepareText($this->get("linklabel", "NR_SMARTTAGS_SHOW")) . '</span>
                </a>
                <div class="nrst-wrap">
    		    <div class="nrst-list">';

    	foreach ($tags as $tag => $value)
    	{
    		$html[] = '<div><a href="#" data-clipboard="' . $tag . '">' . $tag . '</a></div>';
    	}

    	$html[] = '</div></div></div>';

        $this->addScript();

    	return implode(" ", $html);
    }

    /**
     *  Adds field's script and CSS into the document once
     */
    private function addScript()
    {
    	static $run;

    	if ($run)
    	{
    		return;
    	}

        // Add script
    	$this->doc->addScriptDeclaration('
			jQuery(function($) {
				$(".nrst-btn").click(function() {
                    list = $(this).next();
                    $(this).find(".l").html(list.is(":visible") ? $(this).data("show-label") : $(this).data("hide-label"));
                    list.slideToggle();
				})

                $(".nrst-list a").click(function() {
                    var tag = $(this);
                    copyTextToClipboard(tag.data("clipboard"), function(success) {
                        if (success) {
                            tag.addClass("copied");
                        }

                        setTimeout(function() {
                            tag.removeClass("copied");
                        }, 1000);
                    });

                    return false;
                });

                function copyTextToClipboard(text, callback) {
                    var textArea = document.createElement("textarea");
                    textArea.style.position = "fixed";
                    textArea.style.top = 0;
                    textArea.style.left = 0;
                    textArea.style.width = "2em";
                    textArea.style.height = "2em";
                    textArea.style.background = "transparent";
                    textArea.value = text;
                    document.body.appendChild(textArea);
                    textArea.select();

                    try {
                        var success = document.execCommand("copy");
                        callback(success);
                    } catch (err) {
                        callback(false);
                    }

                    document.body.removeChild(textArea);
                }
			})
    	');

        // Add height
        if ($height = $this->get("height", null))
        {
            $this->doc->addStyleDeclaration('
                .nrst-wrap {
                    height: ' . $height . ';
                    overflow-x: hidden;
                    padding-right: 10px;
                }'
            );
        }

        // Add CSS
    	$this->doc->addStyleDeclaration('
			.nrst-wrap {
                display:none;
            }
            .nrst-list {
				display:flex;
				flex-wrap: wrap;
				margin:10px -3px 0 -3px;
			}
			.nrst-list div {
				min-width:50%;
			}
			.nrst-list a {
                -webkit-transition: background 150ms ease;
                -moz-transition: background 150ms ease;
                transition: background 150ms ease;
                color: inherit;
                text-decoration: none;
				display: block;
				border: solid 1px #ddd;
				padding: 7px;
				line-height: 1;
				margin: 3px;
				font-size: 12px;
			}
            .nrst-list a:hover {
                background-color:#eee;
            }
            .nrst-list a:after {
                font-family: "IcoMoon";
                font-style: normal;
                speak: none;
                float: right;
                font-size: 10px;
                line-height: 1;
            }
            .nrst-list a:hover:after {
                content: "\e018";
            }
            .nrst-list a.copied {
                background:#dff0d8;
            }
            .nrst-list a.copied:after {
                content: "\47";
                color: green;
            }
    	');

    	$run = true;
    }
}