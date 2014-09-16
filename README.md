Console Time Booker (ctb)
===

This is a PHProjekt CLI Timebooker for guys who work on terminal all day and also want to book on it.

NOTE: This may not work with your PHProjekt version.

# Installation

1. Clone this git repository

2. Add .env.php file to root directory with the following content.

```php
<?php

return [
	'PHPROJEKT_URL' => 'https://phprojekt.yourcompany.de',
	'PHPROJEKT_USERNAME' => 'mmustermann',
	'PHPROJEKT_PASSWORD' => 'passwort'
];
```

3. Start using ctb by using the listed features.

# Features available

## List today time bookings
```php
php artisan t:list
```

## List todays favorite project bookings
```php
php artisan p:list
```

## Book todays working time
```php
php artisan t:t <start 0800> <end 1600>
```

## Book todays project working time
```php
php artisan p:p <projectID> <time 0100> <description Meeting>
```

*NOTE:* The projectID can be seen in project list (php artisan p:list)

## Start working timer
```php
php artisan t:start
```

## Stop working timer
```php
php artisan t:stop
```

#Features coming someday

* List time/rpoject bookings of this month
* List time/project bookings of another day
* Add time/project bookings to another day
* Remove time/project bookings
* Reminder with notification (Did you ctb'ed already?)

