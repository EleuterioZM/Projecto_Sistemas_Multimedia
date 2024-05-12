<?php
/**
 * @package   Phoca Gallery
 * @author    Jan Pavelka - https://www.phoca.cz
 * @copyright Copyright (C) Jan Pavelka https://www.phoca.cz
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 and later
 * @cms       Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\HTML\HTMLHelper;

class PhocaGalleryRenderTabs
{

    protected $id           = '';
    protected $activeTab    = '';
    protected $countTab     = 0;

    public function __construct() {

        $this->id = uniqid();
        HTMLHelper::_('jquery.framework', false);
        HTMLHelper::_('script', 'media/com_phocagallery/js/tabs/tabs.js', array('version' => 'auto'));
        HTMLHelper::_('stylesheet', 'media/com_phocagallery/js/tabs/tabs.css', array('version' => 'auto'));
    }

    public function setActiveTab($item) {
        if ($item != '') {
            $this->activeTab = $item;
        }
    }

    public function startTabs() {
        return '<div class="phTabs" id="phTabsId' . $this->id . '">';
    }


    public function endTabs() {
        return '</div>';
    }

    public function renderTabsHeader($items) {

        $o   = array();
        $o[] = '<ul class="phTabsUl">';
        if (!empty($items)) {
            $i = 0;
            foreach ($items as $k => $v) {

                $activeO = '';
                if ($this->activeTab == '' && $i == 0) {
                    $activeO = ' active';
                } else if ($this->activeTab == $v['id']) {
                    $activeO = ' active';

                }
                $o[] = '<li class="phTabsLi"><a class="phTabsA phTabsHeader' . $activeO . '" id="phTabId' . $this->id . 'Item' . $v['id'] . '">'
                        //. PhocaGalleryRenderFront::renderIcon($v['icon'], 'media/com_phocagallery/images/icon-' . $v['image'] . '.png', '')
                        . '<svg class="ph-si ph-si-tab ph-si-'.$v['icon'].'"><use xlink:href="#ph-si-'.$v['icon'].'"></use></svg>'
                        . '&nbsp;' . $v['title'] . '</a></li>';
                $i++;
            }
        }

        $o[] = '</ul>';
        return implode("\n", $o);

    }

    public function startTab($name) {

        $activeO = '';
        if ($this->activeTab == '' && $this->countTab == 0) {
            $activeO = ' active';
        } else if ($this->activeTab == $name) {
            $activeO = ' active';
        }
        $this->countTab++;
        return '<div class="phTabsContainer' . $activeO . '" id="phTabId' . $this->id . 'Item' . $name . 'Container">';
    }

    public function endTab() {
        return '</div>';
    }
}

