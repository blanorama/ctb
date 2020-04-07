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
 * @param $now DateTime
 * @return string
 */
function getRoundedTimestamp($now) {
    $minutes = intval($now->format("i"));
    $seconds = intval($now->format("s"));
    $rawValue = ($minutes + $seconds / 60) / 15;
    $rounded = round($rawValue) * 15;
    return $now->format("H").sprintf('%02d', $rounded);
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