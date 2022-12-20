<?php
/**
 * Sends Icinga SMS notifications via CM.com.
 *
 * @author    Ch'Ih-Yu <chi-yu@web.de>
 * @copyright 2022 Random-Host.tv
 * @license   https://opensource.org/licenses/BSD-3-Clause BSD License (3 Clause)
 *
 * @see https://github.random-host.tv
 */

use randomhost\Icinga\Notification\CmDotCom\Factory;

// require autoload.php
$paths = [
    __DIR__.'/../../../../autoload.php',
    __DIR__.'/../../../vendor/autoload.php',
    __DIR__.'/../../vendor/autoload.php',
];
foreach ($paths as $autoload) {
    if (file_exists($autoload)) {
        require_once $autoload;

        break;
    }
}
unset($paths, $autoload);

$notification = (new Factory())->getNotification();
$notification
    ->setOptions(
        getopt(
            $notification->getShortOptions(),
            $notification->getLongOptions()
        )
    )
    ->run()
;

echo $notification->getMessage();

exit($notification->getCode());
