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

HTMLHelper::_('bootstrap.modal');
HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');

if (!defined('nrJ4'))
{
    JHtml::_('formbehavior.chosen', 'select');

    JFactory::getDocument()->addScriptDeclaration('
        document.addEventListener("DOMContentLoaded", function() {
            Joomla.submitbutton = function(task)
            {
                if (task == "campaign.cancel" || document.formvalidator.isValid(document.getElementById("adminForm")))
                {
                    Joomla.submitform(task, document.getElementById("adminForm"));
                }
            }
        });
    ');
}

JFactory::getDocument()->addScriptDeclaration('
    jQuery(function($) {
        $("#jform_service").on("change", function() {
            $("#confirm-delete").modal("show");
        })

        $("#confirm-delete .btn-success").click(function() {
            Joomla.submitform("campaign.apply", document.getElementById("adminForm"));
        })
    })
');

?>

<div class="modal fade modal-nr modal-sm" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3><?php echo JText::_("COM_CONVERTFORMS_CAMPAIGN_CHANGED"); ?></h3>
            </div>
            <div class="modal-body">
                <p><?php echo JText::_("COM_CONVERTFORMS_CAMPAIGN_CONFIRM_DESC"); ?></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-bs-dismiss="modal" data-dismiss="modal"><?php echo JText::_("JCANCEL") ?></button>
                <a class="btn btn-success"><?php echo JText::_("JAPPLY") ?></a>
            </div>
        </div>
    </div>
</div>

<form action="<?php echo JRoute::_('index.php?option=com_convertforms&view=campaign&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">
    <div class="form-horizontal">
        <div class="row-fluid">
            <div class="span12">
                <div class="card p-3 mb-3 well nr-well">
                    <h4>Campaign Settings</h4>
                    <?php echo $this->form->renderField("name"); ?>
                    <?php echo $this->form->renderField("state"); ?>
                </div>
                <div class="card p-3 well nr-well">
                    <h4><?php echo JText::_("COM_CONVERTFORMS_CAMPAIGN_SYNC") ?></h4>
                    <div class="well-desc" style="margin-bottom:20px">
                        <?php echo JText::_("COM_CONVERTFORMS_CAMPAIGN_SYNC_DESC"); ?>
                    </div>

                    <div class="control-group">
                        <div class="control-label">
                            <?php echo $this->form->getLabel("service"); ?>
                        </div>
                        <div class="controls">
                            <?php echo $this->form->getInput("service"); ?>
                            <a href="<?php echo JURI::base() ?>index.php?option=com_convertforms&view=addons" class="btn btn-info btn-small">
                                <span class="icon-cogs" style="margin-right:5px;"></span>
                                <?php echo JText::_("COM_CONVERTFORMS_INSTALL_ADDONS"); ?>
                            </a>
                        </div>
                    </div>
                    <?php echo $this->form->renderField("service_pro"); ?>
                </div>
                <?php if ($this->item->service) { ?>
                    <div class="card p-3 mt-3 well nr-well cf-service-fields">
                        <h4><?php echo JText::_("PLG_CONVERTFORMS_" . $this->item->service  . "_ALIAS"); ?></h4>
                        <div class="well-desc" style="margin-bottom:20px;"><?php echo JText::_("PLG_CONVERTFORMS_" . $this->item->service  . "_DESC"); ?></div>
                        <?php echo $this->form->renderFieldset("service"); ?>
                    </div>
                <?php } ?>
                <?php echo JHtml::_('form.token'); ?>
                <input type="hidden" name="task" value="" />
            </div>
        </div>
    </div>
</form>