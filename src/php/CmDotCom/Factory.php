<?php

namespace randomhost\Icinga\Notification\CmDotCom;

use CMText\TextClient;

/**
 * Factory for CM.com dependencies.
 *
 * @author    Ch'Ih-Yu <chi-yu@web.de>
 * @copyright 2025 Random-Host.tv
 * @license   https://opensource.org/licenses/BSD-3-Clause BSD License (3 Clause)
 *
 * @see https://github.random-host.tv
 */
class Factory
{
    /**
     * Returns a \CMText\TextClient instance.
     *
     * @param string $apiKey API key.
     */
    public function getTextClient(string $apiKey): TextClient
    {
        return new TextClient($apiKey);
    }

    /**
     * Returns a CmText instance.
     */
    public function getNotification(): CmText
    {
        return new CmText($this);
    }
}
