<?php

namespace Bl\Tools;

use Bl\BaLiu;
use DateTime;
use DateTimeZone;

/**
 *
 * 时间工具
 * DateTool
 * Bl\Tools
 */
class DateTool extends BaLiu
{
    const YEAR = 31536000;

    const MONTH = 2592000;

    const WEEK = 604800;

    const DAY = 86400;

    const HOUR = 3600;

    const MINUTE = 60;


    /**
     * 计算两个时区间相差的时长,单位为秒
     *
     * seconds = self::offset("America/Chicago", "GMT");
     *
     * [!!] A list of time zones that PHP supports can be found at
     * <http://php.net/timezones>.
     *
     * @param string $remote timezone that to find the offset of
     * @param string $local timezone used as the baseline
     * @param mixed $now UNIX timestamp or date string
     * @return  integer
     */
    public static function offset($remote, $local = null, $now = null)
    {
    }
}
