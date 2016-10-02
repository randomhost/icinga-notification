<?php
namespace randomhost\Icinga\Notification;

/**
 * Unit test for NotifyMyAndroid.
 *
 * @author    Ch'Ih-Yu <chi-yu@web.de>
 * @copyright 2016 random-host.com
 * @license   http://www.debian.org/misc/bsd.license BSD License (3 Clause)
 * @link      http://github.random-host.com/icinga-notification/
 */
class NotifyMyAndroidTest extends \PHPUnit_Framework_TestCase
{
    public function testRunSuccessful()
    {
        $options = array(
            'type' => 'Unit Test',
            'service' => __CLASS__,
            'host' => 'localhost',
            'address' => '127.0.0.1',
            'state' => 0,
            'time' => date('Y-m-d H:i:S'),
            'output' => 'Don\'t worry, he happy.',
            'apikey' => '0123456789abcdefghijklmnopqrstuvwxyz0123456789ab',
        );

        $message = sprintf(
            'Service: %1$s' . PHP_EOL .
            'Host: %2$s' . PHP_EOL .
            'State: %3$s' . PHP_EOL .
            'Message: %4$s',
            $options['service'],
            $options['host'],
            $options['state'],
            $options['output']
        );

        $client = $this->getNmaClientMock();

        $client
            ->expects($this->once())
            ->method('addApiKey')
            ->with($options['apikey'])
            ->willReturnSelf();

        $client
            ->expects($this->once())
            ->method('verify')
            ->willReturnSelf();

        $client
            ->expects($this->once())
            ->method('notify')
            ->with(
                NotifyMyAndroid::SENDER,
                $options['type'],
                $message,
                0,
                'anag://open?updateonreceive=true'
            )
            ->willReturnSelf();

        $nma = new NotifyMyAndroid($client);

        $nma->setOptions($options);

        $this->assertSame(
            $nma,
            $nma->run()
        );

        $this->assertEquals(
            NotifyMyAndroid::STATE_OK,
            $nma->getCode()
        );

        $this->assertEquals(
            'Message was sent',
            $nma->getMessage()
        );
    }

    public function testRunUnsuccessful()
    {
        $options = array(
            'type' => 'Unit Test',
            'service' => __CLASS__,
            'host' => 'localhost',
            'address' => '127.0.0.1',
            'state' => 0,
            'time' => date('Y-m-d H:i:S'),
            'output' => 'Don\'t worry, he happy.',
            'apikey' => '0123456789abcdefghijklmnopqrstuvwxyz0123456789ab',
        );

        $message = sprintf(
            'Service: %1$s' . PHP_EOL .
            'Host: %2$s' . PHP_EOL .
            'State: %3$s' . PHP_EOL .
            'Message: %4$s',
            $options['service'],
            $options['host'],
            $options['state'],
            $options['output']
        );

        $client = $this->getNmaClientMock();

        $client
            ->expects($this->once())
            ->method('addApiKey')
            ->with($options['apikey'])
            ->willReturnSelf();

        $client
            ->expects($this->once())
            ->method('verify')
            ->willReturnSelf();

        $client
            ->expects($this->once())
            ->method('notify')
            ->with(
                NotifyMyAndroid::SENDER,
                $options['type'],
                $message,
                0,
                'anag://open?updateonreceive=true'
            )
            ->willThrowException(
                new \RuntimeException(
                    'Test failed',
                    500
                )
            );

        $nma = new NotifyMyAndroid($client);

        $nma->setOptions($options);

        $this->assertSame(
            $nma,
            $nma->run()
        );

        $this->assertEquals(
            NotifyMyAndroid::STATE_CRITICAL,
            $nma->getCode()
        );

        $this->assertEquals(
            'Message could not be sent. Error from NMA client: Test failed',
            $nma->getMessage()
        );
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\randomhost\NotifyMyAndroid\Client
     */
    private function getNmaClientMock()
    {
        $client = $this
            ->getMockBuilder('randomhost\\NotifyMyAndroid\\Client')
            ->disableOriginalConstructor()
            ->setMethods(
                array(
                    'addApiKey',
                    'verify',
                    'notify'
                )
            )
            ->getMock();

        return $client;
    }
}
