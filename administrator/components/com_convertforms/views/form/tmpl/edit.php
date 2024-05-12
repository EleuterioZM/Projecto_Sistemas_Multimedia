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

use Joomla\CMS\HTML\HTMLHelper;
use NRFramework\HTML;

HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('bootstrap.dropdown');

JHtml::script('com_convertforms/admin.js', ['relative' => true, 'version' => 'auto']);
JHtml::stylesheet('com_convertforms/editor.css', ['relative' => true, 'version' => 'auto']);

if (defined('nrJ4'))
{
    JFactory::getDocument()->addScript(JURI::root(true) . '/media/vendor/tinymce/tinymce.js');
    HTML::fixFieldTooltips();

} else 
{
    JFactory::getDocument()->addScript(JURI::root(true) . '/media/editors/tinymce/tinymce.min.js');
    JHtml::script('com_convertforms/cookie.js', ['relative' => true, 'version' => 'auto']);
}

$fonts = new NRFonts();
JFactory::getDocument()->addScriptDeclaration('var ConvertFormsGoogleFonts = '. json_encode($fonts->getFontGroup('google')));

$tabState      = JFactory::getApplication()->input->cookie->get("ConvertFormsState" . $this->item->id, 'fields');
$tabStateParts = explode("-", $tabState);
$tabActive     = $tabStateParts[0];

// Smart Tags Box
echo NRFramework\HTML::smartTagsBox();


NRFramework\HTML::renderProOnlyModal();


if (!$this->isnew) { 
    // Render Embed popup
    echo \JHtml::_('bootstrap.renderModal', 'embedForm', [
        'title'  => 'Embed Form',
        'footer' => '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal" data-dismiss="modal" aria-hidden="true">'. JText::_('JLIB_HTML_BEHAVIOR_CLOSE') . '</button>',
    ], '
    <p>You are almost done! To embed this form on your site, please paste the following shortcode inside an article or a module.</p>
    <input class="shortcode" readonly value="{convertforms ' . $this->item->id . '}"/>
    <p>or you can follow the instructions from this <a target="_blank" href="https://www.tassos.gr/joomla-extensions/convert-forms/docs/how-to-display-a-form-on-the-frontend">page</a>.</p>
    ');
}


function tabSetStart($active)
{
    echo defined('nrJ4') ? HTMLHelper::_('uitab.startTabSet', 'sections', ['active' => $active, 'orientation' => 'vertical']) : JHtml::_('bootstrap.startTabSet', 'sections', ['active' => $active]);;
}

function tabSetEnd()
{
    echo defined('nrJ4') ? HTMLHelper::_('uitab.endTabSet') : JHtml::_('bootstrap.endTabSet');;
}

function tabStart($name, $title)
{
    echo defined('nrJ4') ? HTMLHelper::_('uitab.addTab', 'sections', $name, JText::_($title)) : JHtml::_('bootstrap.addTab', 'sections', $name, JText::_($title));
}

function tabEnd()
{
    echo defined('nrJ4') ? HTMLHelper::_('uitab.endTab') : JHtml::_('bootstrap.endTab');
}

if (defined('nrJ4'))
{
    NRFramework\HTML::fixFieldTooltips();
}

?>

<div class="cfEditor">

    <?php
        JPluginHelper::importPlugin('convertformstools');
        JFactory::getApplication()->triggerEvent('onConvertFormsEditorView');
    ?>

    <div class="nrEditor" data-root="<?php echo JURI::root(); ?>">
        <?php if (defined('nrJ4')) { ?>
        <div class="cfe-top">
            <div>
                <img height="18px" src="<?php echo JURI::root() ?>media/templates/administrator/atum/images/logos/brand-small.svg"/>
            </div>
            <div>
                <span><?php echo JVERSION ?></span>
                <span class="pl-3"><?php echo JFactory::getApplication()->get('sitename') ?></span>
            </div>
        </div>
        <?php } ?>

        <div class="cfe-header">
            <div class="cfe-logo">
                <img width="150px" src="<?php echo JURI::root() ?>/media/com_convertforms/img/logo.svg"/>
            </div>
            <div class="cfe-title">
                <label for="formname"><?php echo $this->isnew ? JText::_('COM_CONVERTFORMS_NEW_FORM') : JText::_('COM_CONVERTFORMS_EDIT_FORM') ?>:</label>
                <input type="text" data-fallback="<?php echo JText::_("COM_CONVERTFORMS_UNTITLED_BOX") ?>" id="formname" value="<?php echo $this->name ?>"/>
            </div>
            <div class="cfe-toolbar">
                <ul class="cf-menu">
                    <li>
                        <a href="#" class="btn btn-success save cf-menu-item saveForm" title="Save form" data-cfaction="save">
                            <i class="cf-icon-ok up-state"><?php echo JText::_('JAPPLY') ?></i>
                            <i class="cf-icon-spin hover-state">Saving..</i>
                        </a>
                    </li>
                    <li class="cf-menu-parent">
                        <a href="#" class="cf-icon-dots cf-menu-item " role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" data-toggle="dropdown" title="View more"></a>
                        <ul class="dropdown <?php echo defined('nrJ4') ? 'dropdown-menu' : '' ?>">
                            <li>
                                <a class="<?php echo $this->isnew ? 'disabled' : '' ?>" data-bs-toggle="modal" data-bs-target="#embedForm" data-toggle="modal" data-target="#embedForm" href="#">
                                    <span class="cf-icon-link"></span>
                                    <?php echo JText::_('NR_EMBED') ?>
                                </a>
                            </li>
                            <li>
                                <a class="<?php echo $this->isnew ? 'disabled' : '' ?>" target="_blank" href="<?php echo JURI::base() ?>index.php?option=com_convertforms&view=conversions&filter.form_id=<?php echo $this->item->id ?>">
                                    <span class="cf-icon-users"></span>
                                    <?php echo JText::_('COM_CONVERTFORMS_SUBMISSIONS')?>
                                </a>
                            </li>
                            <li class="separator"></li>
                            
                            <li>
                                <a href="#" data-pro-only>
                                    <span class="cf-icon-heart"></span>
                                    <?php echo JText::_('NR_UPGRADE_TO_PRO') ?>
                                </a>
                            </li>
                            <li class="separator"></li>
                            
                            <li>
                                <a href="https://extensions.joomla.org/extension/convert-forms/" target="_blank">
                                    <span class="cf-icon-thumbs-up"></span>
                                    <?php echo JText::sprintf('NR_RATE', JText::_('CONVERTFORMS')) ?>
                                </a>
                            </li>
                            <li>
                                <a href="http://www.tassos.gr/contact?topic=Bug Report&extension=Convert Forms" target="_blank">
                                    <span class="cf-icon-attention"></span>
                                    <?php echo JText::_('NR_REPORT_ISSUE') ?>
                                </a>
                            </li>
                            <li>
                                <a href="http://www.tassos.gr/joomla-extensions/convert-forms/docs" target="_blank">
                                    <span class="cf-icon-help"></span>
                                    <?php echo JText::_('JHELP') ?>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="<?php echo JRoute::_('index.php?option=com_convertforms&view=forms') ?>" class="cf-icon-cancel cf-menu-item" title="Close and return to forms list"></a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="cfe-main">
            <div class="nrEditorOptions inputSettings">
                <form action="<?php echo JRoute::_('index.php?option=com_convertforms&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-vertical" pk="<?php echo (int) $this->item->id ?>">
                    <div class="tabs-left">
                        <?php 
                            tabSetStart($tabActive);

                            foreach ($this->tabs as $key => $tab)
                            {
                                $tabName  = $key;
                                $tabLabel = JText::_($tab["label"]);

                                tabStart($tabName, '<span data-label="' . $tabLabel . '" class="' . $tab["icon"] . '"></span>');

                                $panelActive = $tabActive == $key ? $tabState : "";

                                echo JHtml::_('bootstrap.startAccordion', $tabName, array('active' => $panelActive));
                                echo "<h2>" . $tabLabel . "</h2>";

                                $single = count($tab["fields"]) == 1 ? true : false;

                                foreach ($tab["fields"] as $key => $field)
                                {
                                    if ($single)
                                    {
                                        echo '<div class="accordion-inner"> ' . $this->form->renderFieldset($field["name"]) . '</div>';
                                        continue;
                                    }

                                    echo JHtml::_('bootstrap.addSlide', $tabName, JText::_($field["label"]), $tabName.'-' . $field["name"], $field["name"]);

                                    $fieldset = $this->form->renderFieldset($field["name"]);
                                    JFactory::getApplication()->triggerEvent('onConvertFormsBackendFormPrepareFieldset', [$field["name"], &$fieldset]);
                                    echo $fieldset;

                                    echo JHtml::_('bootstrap.endSlide');
                                }

                                echo JHtml::_('bootstrap.endAccordion');

                                tabEnd();
                            }

                            tabSetEnd();
                        ?>
                        <input type="hidden" name="task" value="form.edit" />
                        <?php echo JHtml::_('form.token'); ?>
                    </div>
                </form>
            </div>
            <div class="nrEditorPreview hidden-phone">
                <div class="nrEditorTools inputSettings">
                    <div class="l nrEditorTabs">
                        <ul class="nrNav">
                            <li class="nrCheckbox">
                                <input value="1" type="checkbox" id="preview-successmsg">
                                <label for="preview-successmsg"><?php echo JText::_("COM_CONVERTFORMS_PREVIEW_SUCCESS") ?></label>
                            </li>
                        </ul>
                    </div>
                    <div class="r">

                    </div>
                </div>
                <div class="nrEditorPreviewContainer"></div>
                <div class="loader"></div>
            </div>
        </div>
    </div>


</div>