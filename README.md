[![Build Status][0]][1]

randomhost/icinga-notification
==============================

This package provides a set of common notification commands to a accompany the
`randomhost/icinga` package.

Usage
-----

A basic approach at using the notification plugins contained within this package
could look like this:

```php
<?php
namespace randomhost\Icinga\Notification;

require_once '/path/to/vendor/autoload.php';

use randomhost\NotifyMyAndroid\Client as NmaClient;

$nmaClient = new NmaClient();

$notification = new NotifyMyAndroid($nmaClient);
$notification->run();

echo $notification->getMessage();
exit($notification->getCode());
```

This will instantiate the NotifyMyAndroid notification plugin and send a push
notification to the NMA API key provided on the command line.

License
-------

See LICENSE.txt for full license details.


[0]: https://travis-ci.org/randomhost/icinga-notification.svg?branch=master
[1]: https://travis-ci.org/randomhost/icinga-notification
