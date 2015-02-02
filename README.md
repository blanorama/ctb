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
php artisan time:list
```

```php
+-------+---------+---------+
| Start | End     | Sum     |
+-------+---------+---------+
| 0900  | 1300    | 4 h 0 m |
| 1400  | 1800    | 4 h 0 m |
+-------+---------+---------+
|       | Overall | 8 h 0 m |
+-------+---------+---------+
```

## List todays favorite project bookings
```php
php artisan project:list
```

```php
+---------------------+----------------------------+
| Project             | Bookings                   |
+---------------------+----------------------------+
| BizDev (1)          |                            |
+---------------------+----------------------------+
| Mayflower (2)       |                            |
+---------------------+----------------------------+
| Mobile-Shop-Dev (3) |                            |
+---------------------+----------------------------+
|                     | Noch zu vergeben: 9 h 0 m  |
| Overall             | 0 h 0 m                    |
+---------------------+----------------------------+
```

## Book todays working time
```php
php artisan time:book <start 0800> <end 1600>
```

## Book todays project working time
```php
php artisan project:book <projectID> <time 0100> <description Meeting>
```

*NOTE:* The projectID can be seen in project list (php artisan p:list)

## Start working timer
```php
php artisan time:start
```

## Stop working timer
```php
php artisan time:stop
```

## Vacation and overtime overview
```php
php artisan time:overview
```

```php
+--------------------+-----------+
| Overtime           | 08 h 00 m |
| Vacation days left | 20.00     |
+--------------------+-----------+
```

#Features coming someday

* List time/project bookings of this month
* List time/project bookings of another day
* Add time/project bookings to another day
* Remove time/project bookings
* Reminder with notification (Did you ctb'ed already?)

