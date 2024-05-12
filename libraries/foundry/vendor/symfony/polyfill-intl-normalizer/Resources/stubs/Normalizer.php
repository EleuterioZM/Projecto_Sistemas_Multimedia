<?php
/**
* @package      Foundry
* @copyright    Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* Foundry is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class Normalizer extends Symfony\Polyfill\Intl\Normalizer\Normalizer
{
    /**
     * @deprecated since ICU 56 and removed in PHP 8
     */
    public const NONE = 2;
    public const FORM_D = 4;
    public const FORM_KD = 8;
    public const FORM_C = 16;
    public const FORM_KC = 32;
    public const NFD = 4;
    public const NFKD = 8;
    public const NFC = 16;
    public const NFKC = 32;
}
