<?php
namespace randomhost\Icinga\Notification;

use Exception;
use randomhost\NotifyMyAndroid\Client as NmaClient;

/**
 * Sends Icinga Android push notifications via Notify My Android.
 *
 * @author    Ch'Ih-Yu <chi-yu@web.de>
 * @copyright 2016 random-host.com
 * @license   http://www.debian.org/misc/bsd.license BSD License (3 Clause)
 * @link      http://github.random-host.com/icinga-notification/
 */
class NotifyMyAndroid extends Base implements Notification
{
    /**
     * Notification sender.
     *
     * @const bool
     */
    const SENDER = 'Icinga';

    /**
     * Instance of NotifyMyAndroid\Client.
     *
     * @var NmaClient
     */
    protected $nmaClient = null;

    /**
     * Maps host and service states to priority values.
     *
     * @var array
     */
    protected $stateToPriorityMap
        = array(
            self::STATE_UNKNOWN => 0,
            self::STATE_OK => 0,
            self::STATE_WARNING => 1,
            self::STATE_CRITICAL => 2,
        );

    /**
     * Constructor.
     *
     * @param NmaClient $nmaClient NmaClient instance.
     */
    public function __construct(NmaClient $nmaClient)
    {
        $this->nmaClient = $nmaClient;

        $this->setLongOptions(
            array(
                'type:',
                'service:',
                'host:',
                'address:',
                'state:',
                'time:',
                'output:',
                'apikey:',
            )
        );

        $this->setRequiredOptions(
            array(
                'type',
                'service',
                'host',
                'address',
                'state',
                'time',
                'output',
                'apikey',
            )
        );

        $this->setHelp(
            <<<EOT
Icinga plugin for sending Android push notifications via Notify My Android.

--type    Notification type
--service Service name
--host    Host name
--address Host address
--state   Service state
--time    Notification time
--output  Check plugin output
--apikey  NotifyMyAndroid API key
EOT
        );
    }

    /**
     * Reads command line options and performs pre-run tasks.
     *
     * @return $this
     */
    protected function preRun()
    {
        parent::preRun();

        $options = $this->getOptions();

        $this->nmaClient->addApiKey($options['apikey']);

        return $this;
    }

    /**
     * Sends the notification to the given Android device.
     *
     * @return $this
     */
    protected function send()
    {
        try {
            $options = $this->getOptions();

            $message = sprintf(
                'Service: %2$s' . PHP_EOL .
                'Host: %3$s' . PHP_EOL .
                'State: %5$s' . PHP_EOL .
                'Message: %7$s',
                $options['type'],
                $options['service'],
                $options['host'],
                $options['address'],
                $options['state'],
                $options['time'],
                $options['output']
            );

            $priority = $this->determinePriority($options['state']);

            // verify API key
            $this->nmaClient->verify();

            // send notification
            $this->nmaClient->notify(
                self::SENDER,
                $options['type'],
                $message,
                $priority,
                'anag://open?updateonreceive=true'
            );

            $this->setMessage('Message was sent');
            $this->setCode(self::STATE_OK);
        } catch (Exception $e) {
            $this->setMessage(
                'Message could not be sent. Error from NMA client: ' .
                $e->getMessage()
            );
            $this->setCode(self::STATE_CRITICAL);
        }

        return $this;
    }

    /**
     * Returns the priority for the given host or service state.
     *
     * @param string $state Host or service state.
     *
     * @return int
     */
    protected function determinePriority($state)
    {
        $priority = 0;

        if (array_key_exists($state, $this->stateToPriorityMap)) {
            $priority = $this->stateToPriorityMap[$state];
        }

        return $priority;
    }
}
