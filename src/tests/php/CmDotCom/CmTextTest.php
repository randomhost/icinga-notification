<?php

namespace randomhost\Icinga\Notification\Tests\CmDotCom;

use CMText\Exceptions\MessagesLimitException;
use CMText\Exceptions\RecipientLimitException;
use CMText\TextClient;
use CMText\TextClientResult;
use CMText\TextClientStatusCodes;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use randomhost\Icinga\Notification\CmDotCom\CmText;
use randomhost\Icinga\Notification\CmDotCom\Factory;
use randomhost\Icinga\Plugin;

/**
 * Unit test for {@see CmText}.
 *
 * @author    Ch'Ih-Yu <chi-yu@web.de>
 * @copyright 2025 Random-Host.tv
 * @license   https://opensource.org/licenses/BSD-3-Clause BSD License (3 Clause)
 *
 * @see https://github.random-host.tv
 */
class CmTextTest extends TestCase
{
    use ProphecyTrait;

    /**
     * Tests {@see CmText::run()} with all required options.
     *
     * @throws MessagesLimitException
     * @throws RecipientLimitException
     *
     * @dataProvider providerRunWithOptions
     */
    public function testRunWithOptions(
        array $options,
        int $responseCode,
        string $responseMessage,
        int $expectedCode,
        string $expectedMessage
    ) {
        // setup TextClientResult mock
        $textClientResult = $this->prophesize(TextClientResult::class);
        $textClientResult->statusCode = $responseCode;
        $textClientResult->statusMessage = $responseMessage;

        // setup TextClient mock
        $textClient = $this->prophesize(TextClient::class);
        $textClient
            ->SendMessage(
                Argument::any(),
                Argument::any(),
                Argument::any()
            )
            ->shouldBeCalled()
            ->willReturn($textClientResult->reveal())
        ;

        // setup Factory mock
        $factory = $this->prophesize(Factory::class);
        $factory
            ->getTextClient($options['apikey'])
            ->shouldBeCalledOnce()
            ->willReturn($textClient->reveal())
        ;

        $cmText = new CmText($factory->reveal());
        $cmText
            ->setOptions($options)
            ->run()
        ;

        $this->assertSame($expectedMessage, $cmText->getMessage());
        $this->assertSame($expectedCode, $cmText->getCode());
    }

    /**
     * Provides test data for {@see CmTextTest::testRunWithOptions()}.
     */
    public function providerRunWithOptions(): \Generator
    {
        yield [
            'options' => [
                'type' => 'Problem',
                'service' => 'Test',
                'host' => 'localhost',
                'address' => '::1',
                'state' => 'Warning',
                'time' => '2022-12-20 19:43 +200',
                'output' => 'Something is burning',
                'phone' => '',
                'apikey' => '',
            ],
            'responseCode' => TextClientStatusCodes::OK,
            'responseMessage' => '1 message(s) sent',
            'expectedMode' => Plugin::STATE_OK,
            'expectedMessage' => '1 message(s) sent',
        ];

        yield [
            'options' => [
                'type' => 'Problem',
                'service' => 'Test',
                'host' => 'localhost',
                'address' => '::1',
                'state' => 'Warning',
                'time' => '2022-12-20 19:43 +200',
                'output' => 'Something is burning',
                'phone' => '',
                'apikey' => '',
            ],
            'responseCode' => TextClientStatusCodes::APIKEY_INCORRECT,
            'responseMessage' => 'Invalid product token.',
            'expectedMode' => Plugin::STATE_WARNING,
            'expectedMessage' => 'Invalid product token.',
        ];

        yield [
            'options' => [
                'type' => 'Problem',
                'service' => 'Test',
                'host' => 'localhost',
                'address' => '::1',
                'state' => 'Warning',
                'time' => '2022-12-20 19:43 +200',
                'output' => 'Something is burning',
                'phone' => '',
                'apikey' => '',
            ],
            'responseCode' => TextClientStatusCodes::UNKNOWN,
            'responseMessage' => 'Something unexpected happened.',
            'expectedMode' => Plugin::STATE_CRITICAL,
            'expectedMessage' => 'Something unexpected happened.',
        ];
    }

    /**
     * Tests {@see CmText::run()} with missing options.
     *
     * @dataProvider providerRunWithMissingOptions
     */
    public function testRunWithMissingOptions(
        array $options,
        int $expectedCode,
        string $expectedMessage
    ) {
        // setup Factory mock
        $factory = $this->prophesize(Factory::class);
        $factory
            ->getTextClient(Argument::any())
            ->shouldNotBeCalled()
        ;

        $cmText = new CmText($factory->reveal());
        $cmText
            ->setOptions($options)
            ->run()
        ;

        $this->assertSame($expectedMessage, $cmText->getMessage());
        $this->assertSame($expectedCode, $cmText->getCode());
    }

    /**
     * Provides test data for {@see CmTextTest::testRunWithMissingOptions()}.
     */
    public function providerRunWithMissingOptions(): \Generator
    {
        yield [
            'options' => [
                'type' => 'Problem',
                'service' => 'Test',
                'host' => 'localhost',
                'address' => '::1',
                'state' => 'Warning',
                'time' => '2022-12-20 19:43 +200',
                'output' => 'Something is burning',
                'phone' => '',
            ],
            'expectedMode' => Plugin::STATE_UNKNOWN,
            'expectedMessage' => 'Missing required parameters: apikey',
        ];

        yield [
            'options' => [
                'type' => 'Problem',
                'service' => 'Test',
                'host' => 'localhost',
                'address' => '::1',
                'state' => 'Warning',
                'time' => '2022-12-20 19:43 +200',
                'output' => 'Something is burning',
                'apikey' => '',
            ],
            'expectedMode' => Plugin::STATE_UNKNOWN,
            'expectedMessage' => 'Missing required parameters: phone',
        ];

        yield [
            'options' => [],
            'expectedMode' => Plugin::STATE_UNKNOWN,
            'expectedMessage' => 'Missing required parameters: type, service, '
                .'host, address, state, time, output, phone, apikey',
        ];
    }

    /**
     * Tests {@see CmText::run()} with too many messages.
     *
     * @throws MessagesLimitException
     * @throws RecipientLimitException
     */
    public function testRunWithApiValidationException()
    {
        $expectedMessage = 'Maximum amount of Message objects exceeded.';

        $options = [
            'type' => 'Problem',
            'service' => 'Test',
            'host' => 'localhost',
            'address' => '::1',
            'state' => 'Warning',
            'time' => '2022-12-20 19:43 +200',
            'output' => '',
            'phone' => '',
            'apikey' => '',
        ];

        // setup TextClient mock
        $textClient = $this->prophesize(TextClient::class);
        $textClient
            ->SendMessage(
                Argument::any(),
                Argument::any(),
                Argument::any()
            )
            ->shouldBeCalled()
            ->willThrow(new MessagesLimitException($expectedMessage))
        ;

        // setup Factory mock
        $factory = $this->prophesize(Factory::class);
        $factory
            ->getTextClient($options['apikey'])
            ->shouldBeCalledOnce()
            ->willReturn($textClient->reveal())
        ;

        $cmText = new CmText($factory->reveal());
        $cmText
            ->setOptions($options)
            ->run()
        ;

        $this->assertSame($expectedMessage, $cmText->getMessage());
        $this->assertSame(Plugin::STATE_CRITICAL, $cmText->getCode());
    }
}
