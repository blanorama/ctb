<?php
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
 * @param $time
 * @return mixed
 */
function handleTimeArgument($time) {
    return str_pad($time, 4, '0', STR_PAD_LEFT);
}

/**
 * @param $callingObject
 * @param $dateString
 * @return DateTime|false
 * @throws Exception
 */
function handleDateArgument($callingObject, $dateString) {
    $date = $dateString == null ?
        new DateTime('now', new DateTimeZone('CET')) :
        DateTime::createFromFormat('Y-m-d', $dateString);

    if (!$date) {
        $callingObject->error('[Response] Wrong date format... Please use 1970-01-01 as example.');
        exit();
    }

    return $date;
}
