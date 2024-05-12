<?php

/**
 * @package         Convert Forms
 * @version         3.2.8 Free
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2021 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('_JEXEC') or die('Restricted access');

JFormHelper::loadFieldClass('media');

class JFormFieldSignature extends JFormFieldMedia
{
    /**
     * Allow editing the signature field on the backend
     *
     * @return  string
     */
    protected function getInput()
    {
        JFactory::getDocument()->addStyleDeclaration('
            .previewSignature {
                max-width:600px;
                position:relative;
            }
            .previewSignature .btn-download {
                position:absolute;
                right:10px;
                top:10px;
                display:none;
            }
            .previewSignature:hover .btn-download {
                display:block;
            }
        ');

        $this->class = '';

        $parent = parent::getInput();

        if (!defined('nrJ4'))
        {
            JFactory::getDocument()->addStyleDeclaration('
                .previewSignature {
                    border:solid 1px #ccc;
                    border-radius:3px;
                    box-sizing: border-box;
                }
                .previewSignature * {
                    box-sizing: inherit;
                }
                .previewSignature .pop-helper, .previewSignature .tooltip {
                    display:none !important;
                }
                .previewSignature .input-prepend {
                    width:100%;
                    display:flex;
                    height:34px;
                }
                .previewSignature .input-prepend > * {
                    flex:0;
                    height:100%;
                }
                .previewSignature .input-prepend input {
                    flex:1;
                    border-radius: 0 0 0 3px;
                    padding-left: 10px;
                }
                .previewSignature .field-media-wrapper {
                    margin-bottom: -1px;
                    margin-left: -1px;
                }
                .previewSignature .img-prv {
                    padding:10px;
                    background-color:#f2f2f2;
                    text-align:center;
                }  
            ');

            $parent = '<div class="img-prv"><img src="' . JURI::root() . '/' . $this->value . '"/></div>' . $parent;
        }

        return '
            <div class="previewSignature">' . 
                $parent . '
                <a href="' . JURI::root() . '/' . $this->value . '" title="' . \JText::_('COM_CONVERTFORMS_SIGNATURE_DOWNLOAD') . '" class="btn btn-small btn-primary btn-sm btn-download" download>
                    <span class="icon-download"></span>
                </a>
            </div>
        ';
    }
}