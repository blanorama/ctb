<?php
Artisan::add(new AddProjectTimeCommand());
Artisan::add(new BookDateTimeCommand());
Artisan::add(new BookDateTimeStartCommand());
Artisan::add(new BookTimeCommand());
Artisan::add(new ListDateCommand());
Artisan::add(new ListOverviewCommand());
Artisan::add(new ListTodayCommand());
Artisan::add(new ProjectsCommand());
Artisan::add(new StartWorkingtimeCommand());
Artisan::add(new StopWorkingtimeCommand());

/**
 * @param $callingObject
 * @param $dateString
 * @return DateTime|false
 * @throws Exception
 */
function handleDateArgument ($callingObject, $dateString) {
    $date = $dateString == null ? new DateTime() : DateTime::createFromFormat('Y-m-d', $dateString);

    if (!$date) {
        $callingObject->error('[Response] Wrong format... Please use 1970-01-01 as example.');
        exit();
    }

    return $date;
}
