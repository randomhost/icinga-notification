<?php

namespace randomhost\Icinga\Notification\CmDotCom;

use CMText\TextClientStatusCodes;
use randomhost\Icinga\Notification\Base;
use randomhost\Icinga\Notification\Notification;

/**
 * Sends Icinga SMS notifications via CM.com.
 *
 * @author    Ch'Ih-Yu <chi-yu@web.de>
 * @copyright 2025 Random-Host.tv
 * @license   https://opensource.org/licenses/BSD-3-Clause BSD License (3 Clause)
 *
 * @see https://github.random-host.tv
 */
class CmText extends Base implements Notification
{
    /**
     * Notification sender.
     */
    private const SENDER = 'Icinga';

    /**
     * Maximum message length.
     */
    private const MAX_MESSAGE_LENGTH = 260;

    /**
     * Command line options specific to the CM.com API.
     */
    private const API_OPTIONS = [
        'phone',
        'apikey',
    ];

    /**
     * Text message template.
     */
    private const MESSAGE_TEMPLATE
        = '-{type}- '
        .'Service: {service}, '
        .'Host: {host}, '
        .'State: {state}, '
        .'Message: {output}';

    /**
     * Factory for CM.com dependencies.
     *
     * @var Factory
     */
    private $factory;

    /**
     * Constructor for this class.
     */
    public function __construct(Factory $factory)
    {
        $this->factory = $factory;

        // build required options for this specific implementation
        $requiredOptions = array_merge(self::MESSAGE_OPTIONS, self::API_OPTIONS);
        $this->setRequiredOptions($requiredOptions);

        // build command line options for this specific implementation
        $longOptions = array_map(
            function (string $value) {
                return "{$value}:";
            },
            $requiredOptions
        );
        $this->setLongOptions($longOptions);

        $this->setHelp(
            <<<'EOT'
                Icinga plugin for sending SMS notifications via CM.com.

                --type    Notification type
                --service Service name
                --host    Host name
                --address Host address
                --state   Service state
                --time    Notification time
                --output  Check plugin output
                --phone   Phone number
                --apikey  CM.com API key
                EOT
        );
    }

    /**
     * Sends the notification to the given Android device.
     */
    protected function send(): self
    {
        try {
            $options = $this->getOptions();

            // build message
            $message = $this->buildMessage();

            // shorten message to maximum supported length for a single SMS
            $message = substr($message, 0, self::MAX_MESSAGE_LENGTH);

            // send SMS
            $result = $this->factory
                ->getTextClient($options['apikey'])
                ->SendMessage(
                    $message,
                    self::SENDER,
                    [$options['phone']],
                )
            ;

            $this->setMessage($result->statusMessage);

            // evaluate return code
            switch ($result->statusCode) {
                case TextClientStatusCodes::OK:
                    $this->setCode(self::STATE_OK);

                    break;

                case TextClientStatusCodes::UNKNOWN:
                    $this->setCode(self::STATE_CRITICAL);

                    break;

                default:
                    $this->setCode(self::STATE_WARNING);
            }
        } catch (\Exception $e) {
            $this->setMessage($e->getMessage());
            $this->setCode(self::STATE_CRITICAL);
        }

        return $this;
    }

    /**
     * Returns the message to send.
     */
    private function buildMessage(): string
    {
        $placeholders = array_map(
            function (string $option) {
                return "{{$option}}";
            },
            self::MESSAGE_OPTIONS
        );

        $replacements = array_map(
            function (string $option): string {
                return $this->options[$option];
            },
            self::MESSAGE_OPTIONS
        );

        return str_replace($placeholders, $replacements, self::MESSAGE_TEMPLATE);
    }
}
