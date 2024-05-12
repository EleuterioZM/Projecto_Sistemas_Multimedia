<?php
/*
 * @package Joomla
 * @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 *
 * @component Phoca Gallery
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die('Restricted access');
$d      = $displayData;
$t      = $d['t'];

// we need to change the title dynamically when iframe loads new image - with help of function pgFrameOnLoad() (in main.js)
?><div class="modal fade" id="pgCategoryModal" tabindex="-1" aria-labelledby="pgCategoryModal" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="pgCategoryModalLabel"><?php echo Text::_('COM_PHOCAGALLERY_TITLE') ?></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?php echo Text::_('COM_PHOCAGALLERY_CLOSE') ?>"></button>
      </div>
      <div class="modal-body">
        <iframe id="pgCategoryModalIframe" height="100%" frameborder="0" onload="pgFrameOnLoad()"></iframe>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo Text::_('COM_PHOCAGALLERY_CLOSE') ?></button>
      </div>
    </div>
  </div>
</div>
