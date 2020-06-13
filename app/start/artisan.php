<?php

use Illuminate\Support\Facades\Artisan;

Artisan::add(new AddProjectTimeCommand());
Artisan::add(new BookMissedPauseCommand());
Artisan::add(new BookMissedTimeCommand());
Artisan::add(new BookPauseCommand());
Artisan::add(new BookTaskSwitchCommand());
Artisan::add(new BookTimeCommand());
Artisan::add(new BookTimeEndCommand());
Artisan::add(new BookTimeStartCommand());
Artisan::add(new ListOverviewCommand());
Artisan::add(new ListTimeCommand());
Artisan::add(new ProjectsCommand());
Artisan::add(new StartWorkingtimeCommand());
Artisan::add(new StopWorkingtimeCommand());

/**
 * @param string $option
 * @param string $time
 * @return string
 * @throws Exception
 */
function handleTimeArgument($option, $time) {
    $wrongFormat = '[ERROR] Wrong time format... Please use decimal format for duration in hours or time in format [H]H[MM].';

    $now = getNowDateTime();
    if ($option === 'rounded') $now = getRoundedTimestamp($now);
    elseif ($option !== 'precise') throw new Exception(sprintf(
        '[ERROR] Unknown option in first arg "%s"; possible values: "rounded", "precise"', $option));

    if ($time !== null) {
        $duration = (new NumberFormatter("de-De", NumberFormatter::DECIMAL))->parse($time);
        if (!$duration) throw new Exception($wrongFormat);
        if (!strstr($time, ",") && $duration >= 7) {
            switch (strlen($time)) {
                case 1:
                    return '0' . $time . '00';
                case 2:
                    return $time . '00';
                case 3:
                    return '0' . $time;
                case 4:
                    return $time;
                default:
                    throw new Exception($wrongFormat);
            }
        } else {
            $now = $now->sub(new DateInterval(sprintf("PT%dM", $duration * 60)));
        }
    }
    return $now->format('H') . $now->format('i');;
}

/**
 * @param DateTime $time
 * @return DateTime
 */
function getRoundedTimestamp($time) {
    $interval = 15;
    $minutes = intval($time->format('i'));
    $seconds = intval($time->format('s'));

    $rawValue = ($minutes + $seconds / 60) / $interval;
    $rounded = round($rawValue) * $interval;

    return $time->setTime($time->format('H'), $rounded);
}

/**
 * @param DateTime $date
 * @return string
 */
function getInfoDate($date) {
    return strftime('%a, %x', $date->getTimestamp());
}

/**
 * @return DateTime
 * @throws Exception
 */
function getNowDateTime() {
    return new DateTime();
}

/**
 * @param string $dateString
 * @return DateTime|false
 * @throws Exception
 */
function handleDateArgument($dateString) {
    $date = $dateString === null ? getNowDateTime() : DateTime::createFromFormat('Y-m-d', $dateString);
    if (!$date) throw new Exception('[ERROR] Wrong date format... Please use 1970-01-01 as example.');
    return $date;
}