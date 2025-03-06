<?php

namespace Swark\DataModel\Domain\Model\InformationTechnology\Network;

use Swark\Kernel\Domain\Model\EnumToMap;

/**
 * @see https://www.ionos.com/digitalguide/hosting/technical-matters/dns-records/
 */
enum IpVersion: string
{
    use EnumToMap;

    case V4 = '4';
    case V6 = '6';
}
