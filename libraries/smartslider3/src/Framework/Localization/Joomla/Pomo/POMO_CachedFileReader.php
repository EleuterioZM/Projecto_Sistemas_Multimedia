<?php


namespace Nextend\Framework\Localization\Joomla\Pomo;

/**
 * Reads the contents of the file in the beginning.
 */
class POMO_CachedFileReader extends POMO_StringReader {

    function __construct($filename) {
        parent::__construct();
        $this->_str = file_get_contents($filename);
        if (false !== $this->_str) {
            $this->_pos = 0;
        }
    }
}