<?php


namespace Nextend\SmartSlider3\Application\Admin\Update;


use Joomla\CMS\Router\Route;
use Nextend\SmartSlider3\Application\Admin\AbstractControllerAdmin;

class ControllerUpdate extends AbstractControllerAdmin {

    public function actionUpdate() {
        if ($this->validateToken()) {
            header('LOCATION: ' . Route::_('index.php?option=com_installer&view=update', false));
            exit;
        
        }

        $this->redirectToSliders();
    }
}