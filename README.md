[![Build Status][1]][2]

# randomhost/icinga-notification

<!-- TOC -->
* [1. Purpose](#1-purpose)
* [2. Usage](#2-usage)
  * [2.1. CM.com](#21-cmcom)
    * [2.1.1. Usage Example](#211-usage-example)
    * [2.1.2. Command Line Parameters](#212-command-line-parameters)
* [3. License](#3-license)
<!-- TOC -->

## 1. Purpose

This package provides a set of common notification commands to accompany the
[`randomhost/icinga`][3] package.

## 2. Usage

`CmDotCom` is currently the only available notification plugin.

### 2.1. CM.com

Sends notifications using [CM.com][4].

#### 2.1.1. Usage Example

```php
<?php

use randomhost\Icinga\Notification\CmDotCom\Factory;

include $_composer_autoload_path ?? __DIR__.'/../../vendor/autoload.php';

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
```

This will instantiate the CM.com notification plugin and send a text message to
the phone number provided on the command line.

#### 2.1.2. Command Line Parameters

| Parameter   | Description                                              |
|-------------|----------------------------------------------------------|
| `--type`    | Notification type                                        |
| `--service` | Service name                                             |
| `--host`    | Host name                                                |
| `--address` | Host address                                             |
| `--state`   | Service state                                            |
| `--time`    | Notification time                                        |
| `--output`  | Check plugin output                                      |
| `--phone`   | Phone number in international format (e.g. +12065550199) |
| `--apikey`  | CM.com API key                                           |

## 3. License

See LICENSE.txt for full license details.


[1]: https://github.com/randomhost/icinga-notification/actions/workflows/php.yml/badge.svg
[2]: https://github.com/randomhost/icinga-notification/actions/workflows/php.yml
[3]: https://github.com/randomhost/icinga-notification
[4]: https://cm.com
