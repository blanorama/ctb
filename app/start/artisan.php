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

define("TIME_FORMAT_INPUT", "Hi");

/**
 * @param string $option
 * @param string $time
 * @return string
 * @throws Exception
 */
function handleTimeArgument($option, $time): string {
    $now = getNowDateTime();

    if ($option === 'rounded') $now = getRoundedTimestamp($now);
    elseif ($option !== 'precise') throw new Exception(sprintf(
        '[ERROR] Unknown option in first arg "%s"; possible values: "rounded", "precise"', $option));

    if ($time !== null) {
        $duration = checkDuration($time);
        if ($duration != null) $now = $now->sub($duration);
        else return makeUpTimeString($time);
    }

    return $now->format(TIME_FORMAT_INPUT);
}

/**
 * @param string $string
 * @return DateInterval|null
 * @throws WrongFormatException
 */
function checkDuration($string) {
    $duration = (new NumberFormatter("de-De", NumberFormatter::DECIMAL))->parse($string);
    if ($duration != 0 && !$duration) throw new WrongFormatException($string);
    if (strstr($string, ",") || ($duration < 7 && $duration != 0))
        return new DateInterval(sprintf("PT%dM", $duration * 60));
    else
        return null;
}

/**
 * @param string $time
 * @return string
 * @throws WrongFormatException
 */
function makeUpTimeString(string $time): string
{
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
            throw new WrongFormatException($time);
    }
}

/**
 * @param string $firstString
 * @param string $secondString
 * @return array
 * @throws WrongFormatException
 */
function handleTimeArguments($firstString, $secondString): array {
    $firstDuration = checkDuration($firstString);
    $secondDuration = checkDuration($secondString);

    if ($firstDuration === null && $secondDuration === null)
        return [makeUpTimeString($firstString), makeUpTimeString($secondString)];
    elseif ($firstDuration === null) {
        $first = DateTime::createFromFormat(TIME_FORMAT_INPUT, makeUpTimeString($firstString));
        return [$first->format(TIME_FORMAT_INPUT), $first->add($secondDuration)->format(TIME_FORMAT_INPUT)];
    } elseif ($secondDuration === null) {
        $second = DateTime::createFromFormat(TIME_FORMAT_INPUT, makeUpTimeString($secondString));
        $secondString = $second->format(TIME_FORMAT_INPUT);
        return [$second->sub($firstDuration)->format(TIME_FORMAT_INPUT), $secondString];
    } else
        throw new Exception(sprintf('[ERROR] Cannot handle two durations "%s" & "%s".',
            $firstString, $secondString));
}

/**
 * @param DateTime $time
 * @return DateTime
 */
function getRoundedTimestamp($time): DateTime {
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
function getInfoDate($date): string {
    return strftime('%A, %x', $date->getTimestamp());
}

/**
 * @return DateTime
 * @throws Exception
 */
function getNowDateTime(): DateTime {
    return new DateTime();
}

/**
 * @param string $dateString
 * @return DateTime|false
 * @throws Exception
 */
function handleDateArgument($dateString) {
    $date = $dateString === null ? getNowDateTime() : DateTime::createFromFormat('Y-m-d', $dateString);
    if (!$date) throw new Exception(sprintf('[ERROR] Wrong date format "%s". Please use format YYYY-MM-DD.', $dateString));
    return $date;
}