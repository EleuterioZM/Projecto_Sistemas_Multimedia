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

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\Polyfill\Intl\Normalizer as p;

if (\PHP_VERSION_ID >= 80000) {
    return require __DIR__.'/bootstrap80.php';
}

if (!function_exists('normalizer_is_normalized')) {
    function normalizer_is_normalized($string, $form = p\Normalizer::FORM_C) { return p\Normalizer::isNormalized($string, $form); }
}
if (!function_exists('normalizer_normalize')) {
    function normalizer_normalize($string, $form = p\Normalizer::FORM_C) { return p\Normalizer::normalize($string, $form); }
}
