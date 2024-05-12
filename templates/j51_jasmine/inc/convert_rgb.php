<?php
defined( '_JEXEC' ) or die( 'Restricted index access' );
/**
 * Convert a hexa decimal color code to its RGB equivalent
 *
 * Code base: http://www.php.net/manual/en/function.hexdec.php#99478
 *
 * @param string $hexStr (hexadecimal color value)
 * @param boolean $returnAsString (if set true, returns the value separated by the separator character. Otherwise returns associative array)
 * @param string $seperator (to separate RGB values. Applicable only if second parameter is true.)
 * @return array or string (depending on second parameter. Returns False if invalid hex color value)
 *
 */                                                                                               
function hex2RGB($hexStr, $returnAsString = false, $seperator = ',') {
    $hexStr = preg_replace('/[^0-9a-f]/i', '', $hexStr); // Gets a proper hex string
    //if shorthand notation, need some string manipulations
    if(strlen($hexStr) == 3)
        $hexStr = preg_replace('/([0-9A-F]{1})/i','$1$1',$hexStr);
    if ( strlen($hexStr) != 6) {//Invalid hex color code
        return false;
    }
    $rgbArray = array();
    $colorVal = hexdec($hexStr);
    $rgbArray['red'] = 0xFF & ($colorVal >> 0x10);
    $rgbArray['green'] = 0xFF & ($colorVal >> 0x8);
    $rgbArray['blue'] = 0xFF & $colorVal;
    // returns the rgb string or the associative array
    return $returnAsString ? implode($seperator, $rgbArray) : $rgbArray;
}

?>