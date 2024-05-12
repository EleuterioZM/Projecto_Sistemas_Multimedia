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

namespace Intervention\Image\Commands;

defined('_JEXEC') or die('Unauthorized Access');

use GuzzleHttp\Psr7\Response;

class PsrResponseCommand extends AbstractCommand
{
    /**
     * Builds PSR7 compatible response. May replace "response" command in
     * some future.
     *
     * Method will generate binary stream and put it inside PSR-7
     * ResponseInterface. Following code can be optimized using native php
     * streams and more "clean" streaming, however drivers has to be updated
     * first.
     *
     * @param  \Intervention\Image\Image $image
     * @return boolean
     */
    public function execute($image)
    {
        $format = $this->argument(0)->value();
        $quality = $this->argument(1)->between(0, 100)->value();

        //Encoded property will be populated at this moment
        $stream = $image->stream($format, $quality);

        $mimetype = finfo_buffer(
            finfo_open(FILEINFO_MIME_TYPE),
            $image->getEncoded()
        );

        $this->setOutput(new Response(
            200,
            [
                'Content-Type'   => $mimetype,
                'Content-Length' => strlen($image->getEncoded())
            ],
            $stream
        ));

        return true;
    }
}