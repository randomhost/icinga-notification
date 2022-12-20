<?php

namespace randomhost\Icinga\Notification\CmDotCom;

use CMText\TextClient;
use PHPUnit\Framework\TestCase;

/**
 * Unit test for {@see \randomhost\Icinga\Notification\CmDotCom\Factory}.
 *
 * @author    Ch'Ih-Yu <chi-yu@web.de>
 * @copyright 2022 Random-Host.tv
 * @license   https://opensource.org/licenses/BSD-3-Clause BSD License (3 Clause)
 *
 * @see https://github.random-host.tv
 */
class FactoryTest extends TestCase
{
    /**
     * Tests {@see Factory::getTextClient()}.
     */
    public function testGetTextClient()
    {
        $factory = new Factory();

        $this->assertInstanceOf(
            TextClient::class,
            $factory->getTextClient('dummy-api-key')
        );
    }

    /**
     * Tests {@see Factory::getNotification()}.
     */
    public function testGetNotification()
    {
        $factory = new Factory();

        $this->assertInstanceOf(
            CmText::class,
            $factory->getNotification()
        );
    }
}
