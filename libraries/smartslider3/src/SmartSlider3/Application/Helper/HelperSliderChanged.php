<?php


namespace Nextend\SmartSlider3\Application\Helper;


use Nextend\Framework\Data\Data;
use Nextend\Framework\Model\ApplicationSection;
use Nextend\Framework\Model\StorageSectionManager;
use Nextend\Framework\Pattern\MVCHelperTrait;
use Nextend\SmartSlider3\Application\Model\ModelSliders;
use Nextend\SmartSlider3\Application\Model\ModelSlidersXRef;
use WP_Post;

class HelperSliderChanged {

    use MVCHelperTrait;

    /** @var ApplicationSection */
    protected $storage;

    /**
     * HelperSliderChanged constructor.
     *
     * @param MVCHelperTrait $MVCHelper
     */
    public function __construct($MVCHelper) {

        $this->setMVCHelper($MVCHelper);

        $this->storage = StorageSectionManager::getStorage('smartslider');
    }


    public function isSliderChanged($sliderId, $value = 1) {
        return intval($this->storage->get('sliderChanged', $sliderId, $value));
    }


    public function setGroupChanged($sliderId, $value = 1) {
        $xref     = new ModelSlidersXRef($this);
        $groupIDs = array();
        foreach ($xref->getGroups($sliderId) as $row) {
            if ($row['group_id'] > 0) {
                $this->storage->set('sliderChanged', $row['group_id'], $value);
            }
            $groupIDs[] = $row['group_id'];
        }

        return $groupIDs;

    }

    public function setSliderChanged($sliderId, $value = 1, &$changedSliders = array()) {
        $this->storage->set('sliderChanged', $sliderId, $value);
        $changedSliders[] = $sliderId;

        $xref        = new ModelSlidersXRef($this);
        $sliderModel = new ModelSliders($this);

        array_merge($changedSliders, $this->setGroupChanged($sliderId));

        foreach ($xref->getGroups($sliderId) as $group) {
            $changedSliders[] = $group['group_id'];
        }

        $fallbackSliders = $sliderModel->getFallbackUsage($changedSliders);

        if (!empty($fallbackSliders)) {
            foreach ($fallbackSliders as $slider) {
                if (!in_array($slider['id'], $changedSliders)) {
                    $this->setSliderChanged($slider['id'], 1, $changedSliders);
                }
            }
        }
    }
}