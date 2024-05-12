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

extract($displayData);

if (defined('nrJ4'))
{
    JFactory::getDocument()->addScriptDeclaration('
        document.addEventListener("DOMContentLoaded", function() {
            let proOnlyEl = document.getElementById("proOnlyModal");
            proOnlyEl.classList.add("tf-pro-only-modal");
            
            document.body.appendChild(proOnlyEl);
            
            const opts = {
                backdrop: "static",
                keyboard: false
            };
            let proOnlyModal = new bootstrap.Modal(proOnlyEl, opts);

            document.addEventListener("click", function(e) {
                let elem = e.target.closest("[data-pro-only]");
                if (!elem) {
                    return;
                }

                let proFeature = elem.dataset.proOnly;

                if (proFeature === undefined) {
                    return;
                }

                event.preventDefault();

                if (proFeature) {
                    proOnlyEl.querySelectorAll("em").forEach(function(el) {
                        el.innerHTML = proFeature; 
                    });
                    proOnlyEl.querySelector(".po-upgrade").style.display = "none";
                    proOnlyEl.querySelector(".po-feature").style.display = "block";
                } else {
                    proOnlyEl.querySelector(".po-upgrade").style.display = "block";
                    proOnlyEl.querySelector(".po-feature").style.display = "none";
                }

                proOnlyModal.show();
            });
        });
    ');
} else 
{
    JFactory::getDocument()->addScriptDeclaration('
        jQuery(function($) {
            var $proOnlyModal = $("#proOnlyModal");
    
            // Move to body so it can be accessible by all buttons
            $proOnlyModal.addClass("tf-pro-only-modal");
            $proOnlyModal.appendTo("body");
    
            $(document).on("click", "*[data-pro-only]", function() {
                event.preventDefault();

                var $el = $(this)
                    feature_name = $el.data("pro-only");
    
                if (feature_name) {
                    $proOnlyModal.find("em").html(feature_name);
                    $proOnlyModal.find(".po-upgrade").hide().end().find(".po-feature").show();
                } else {
                    $proOnlyModal.find(".po-feature").hide().end().find(".po-upgrade").show();
                }
    
                $proOnlyModal.modal("show");
            });
        });
    ');
}

JHtml::stylesheet('plg_system_nrframework/proonlymodal.css', ['relative' => true, 'version' => 'auto']);

?>

<div class="pro-only-body text-center">
    <span class="icon-lock"></span>

    <!-- This is shown when we click on a Pro only feature button -->
    <div class="po-feature">
        <h2><?php echo \JText::sprintf('NR_PROFEATURE_HEADER', '') ?></h2>
        <p><?php echo JText::sprintf('NR_PROFEATURE_DESC', '') ?></p>
    </div>

    <!-- This is shown when click on Upgrade to Pro button -->
    <div class="po-upgrade">
        <h2><?php echo \JText::_($extension_name) ?> Pro</h2>
        <p><?php echo JText::sprintf('NR_UPGRADE_TO_PRO_VERSION', \JText::_($extension_name)); ?></p>
    </div>

    <p><a class="btn btn-danger btn-large" href="<?php echo $upgrade_url ?>" target="_blank">
        <?php echo JText::_('NR_UPGRADE_TO_PRO') ?>
    </a></p>
    <div class="pro-only-bonus"><?php echo JText::sprintf('NR_PROFEATURE_DISCOUNT', $extension_name) ?></div>

    <div class="pro-only-footer">
        <div>Pre-Sales questions? <a target="_blank" href="http://www.tassos.gr/contact?topic=Pre-sale Question&extension=<?php echo $extension_name ?>">Ask here</a></div>
        <div>Already purchased Pro? Learn how to <a target="_blank" href="https://www.tassos.gr/kb/general/how-to-upgrade-an-extension-from-free-to-pro">Unlock Pro Features</a></div>
    </div>
</div>