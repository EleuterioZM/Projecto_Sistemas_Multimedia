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

if (!function_exists('normalizer_is_normalized')) {
    function normalizer_is_normalized(?string $string, ?int $form = p\Normalizer::FORM_C): bool { return p\Normalizer::isNormalized((string) $string, (int) $form); }
}
if (!function_exists('normalizer_normalize')) {
    function normalizer_normalize(?string $string, ?int $form = p\Normalizer::FORM_C): string|false { return p\Normalizer::normalize((string) $string, (int) $form); }
}
