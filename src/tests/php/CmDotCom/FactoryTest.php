<?php

namespace randomhost\Icinga\Notification\Tests\CmDotCom;

use CMText\TextClient;
use PHPUnit\Framework\TestCase;
use randomhost\Icinga\Notification\CmDotCom\CmText;
use randomhost\Icinga\Notification\CmDotCom\Factory;

/**
 * Unit test for {@see Factory}.
 *
 * @author    Ch'Ih-Yu <chi-yu@web.de>
 * @copyright 2025 Random-Host.tv
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
