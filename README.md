[![Build Status][0]][1]

randomhost/icinga-notification
==============================

This package provides a set of common notification commands to a accompany the
`randomhost/icinga` package.

Usage
-----

`NotifyMyAndroid` is currently the only available notification plugin but more
will follow in the future.

### NotifyMyAndroid

Sends notifications using [NotifyMyAndroid][2].

#### Usage example

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

#### Command line parameters

| Parameter           | Description             |
| ------------------- | ----------------------- |
| --type              | Notification type       |
| --service           | Service name            |
| --host              | Host name               |
| --address           | Host address            |
| --state             | Service state           |
| --time              | Notification time       |
| --output            | Check plugin output     |
| --apikey            | NotifyMyAndroid API key |

License
-------

See LICENSE.txt for full license details.


[0]: https://travis-ci.org/randomhost/icinga-notification.svg?branch=master
[1]: https://travis-ci.org/randomhost/icinga-notification
[2]: http://notifymyandroid.com
