<?php

namespace Nextend\Framework\Image\Joomla;

use Joomla\CMS\HTML\HTMLHelper;
use Nextend\Framework\Image\AbstractPlatformImage;

class JoomlaImage extends AbstractPlatformImage {

    public function initLightbox() {
        if (version_compare(JVERSION, '4', '<')) {
            HTMLHelper::_('behavior.modal');
        }
    }
}