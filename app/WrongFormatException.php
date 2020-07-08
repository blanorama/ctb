<?php


class WrongFormatException extends Exception {
    function __construct($time) {
        parent::__construct(sprintf('[ERROR] Wrong time format "%s". ',$time).
        'Please use decimal format for duration in hours or time in format [H]H[MM].');
    }
}