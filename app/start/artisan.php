<?php

use Illuminate\Support\Facades\Artisan;

Artisan::add(new AddProjectTimeCommand());
Artisan::add(new BookEndTimeCommand());
Artisan::add(new BookStartTimeCommand());
Artisan::add(new BookTimeCommand());
Artisan::add(new ListOverviewCommand());
Artisan::add(new ListTimeCommand());
Artisan::add(new ProjectsCommand());
Artisan::add(new StartWorkingtimeCommand());
Artisan::add(new StopWorkingtimeCommand());

/**
 * @param $time string
 * @return string
 */
function handleTimeArgument($time) {
    return str_pad($time, 4, '0', STR_PAD_LEFT);
}

/**
 * @param $time DateTime
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
 * @return DateTime
 * @throws Exception
 */
function getNowDateTime() {
    return new DateTime('now');
}

/**
 * @param $callingObject
 * @param $dateString string
 * @return DateTime|false
 * @throws Exception
 */
function handleDateArgument($callingObject, $dateString) {
    $date = $dateString == null ? getNowDateTime() : DateTime::createFromFormat('Y-m-d', $dateString);

    if (!$date) {
        $callingObject->error('[Response] Wrong date format... Please use 1970-01-01 as example.');
        exit();
    }

    return $date;
}