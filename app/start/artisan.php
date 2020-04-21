<?php

use Illuminate\Support\Facades\Artisan;

Artisan::add(new AddProjectTimeCommand());
Artisan::add(new BookEndTimeCommand());
Artisan::add(new BookPauseCommand());
Artisan::add(new BookStartTimeCommand());
Artisan::add(new BookTimeCommand());
Artisan::add(new ListOverviewCommand());
Artisan::add(new ListTimeCommand());
Artisan::add(new ProjectsCommand());
Artisan::add(new StartWorkingtimeCommand());
Artisan::add(new StopWorkingtimeCommand());

/**
 * @param object $callingObject
 * @param string $time
 * @return string
 */
function handleTimeArgument($callingObject, $time) {
    if (strlen($time) > 4) {
        $callingObject->error('[RESPONSE] Wrong format... Please use 0100 0200 [1970-01-01] as example.');
        exit();
    }

    return str_pad($time, 4, '0', STR_PAD_LEFT);
}

/**
 * @param DateTime $time
 * @return string
 */
function getRoundedTimestamp($time) {
    $interval = 15;
    $minutes = intval($time->format("i"));
    $seconds = intval($time->format("s"));

    $rawValue = ($minutes + $seconds / 60) / $interval;
    $rounded = round($rawValue) * $interval;
    
    $time->setTime($time->format('H'), $rounded);
    return $time->format('H').$time->format('i');
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
    return new DateTime('now');
}

/**
 * @param object $callingObject
 * @param string $dateString
 * @return DateTime|false
 * @throws Exception
 */
function handleDateArgument($callingObject, $dateString) {
    $date = $dateString == null ? getNowDateTime() : DateTime::createFromFormat('Y-m-d', $dateString);

    if (!$date) {
        $callingObject->error('[RESPONSE] Wrong date format... Please use 1970-01-01 as example.');
        exit();
    }

    return $date;
}