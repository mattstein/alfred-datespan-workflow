<?php

include 'vendor/autoload.php';

use Alfred\Workflows\Workflow;

$workflow = new Workflow();
$timezoneAbbreviation = isset($argv[2]) ? $argv[2] : 'PDT';

date_default_timezone_set(timezone_name_from_abbr($timezoneAbbreviation));

$dates = explode(" to ", trim(@$argv[1])); // Split arguments by " to "

array_map(static function($dateString) {
    return str_replace('-', '/', $dateString);
}, $dates);

/**
 * Use the current date and time in lieu of a second argument
 */

try {
    $date2 = isset($dates[1]) ? new DateTime($dates[1]) : new DateTime();
    $date1 = new DateTime($dates[0]);
} catch (Exception $e) {
    return;
}

$diff = $date1->diff($date2);

// http://www.php.net/manual/en/dateinterval.format.php

$minutes        = (int) $diff->format('%i');
$hours          = (int) $diff->format('%h');
$days           = (int) $diff->format('%d');
$totalDays     = (int) $diff->format('%a');
$totalHours    = $totalDays * 24;
$totalMinutes  = $totalHours * 60;
$weeks          = (int) $diff->format('%a') / 7;
$businessWeeks = (int) $diff->format('%a') / 5;
$months         = (int) $diff->format('%m');
$years          = (int) $diff->format('%y');
$sign           = $diff->format('%R');

if ($totalDays > 1) {
	$days++;
}


/*
 * A single, complete string; may include years, months, days, hours, minutes,
 * and “ago” if there’s only one parameter and it’s in the past
 */

$complete = [];

if ($years) { $complete[] = pluralize('year', $years); }
if ($months) { $complete[] = pluralize('month', $months); }
if ($days) { $complete[] = pluralize('day', $days); }
if ($hours) { $complete[] = pluralize('hour', $hours); }
if ($minutes) { $complete[] = pluralize('minute', $minutes); }

if (count($complete) > 1) {
	$completeString = implode(', ', array_slice($complete, 0, -1));
	$completeString .= " and ".$complete[count($complete)-1];
} else {
	$completeString = implode(', ', $complete);
}

if ( ! isset($dates[1]) && $sign === "+") {
	$completeString .= " ago";
}

$workflow->item()
    ->title($completeString)
    ->arg($completeString)
    ->subtitle('Copy to clipboard');

if ($businessWeeks > 0) {
    $workflow->item()
        ->title(pluralize('business week', $businessWeeks))
        ->arg(pluralize('business week', $businessWeeks))
        ->subtitle('Copy to clipboard');
}

if ($weeks > 0) {
    $workflow->item()
        ->title(pluralize('week', $weeks))
        ->arg(pluralize('week', $weeks))
        ->subtitle('Copy to clipboard');
}


/**
 * If the *total* number of days is different from days factoring into the interval,
 * we should include it because it’s probably interesting.
 */
if ($totalDays > $days) {
    $workflow->item()
        ->title(pluralize('day', $totalDays))
        ->arg(pluralize('day', $totalDays))
        ->subtitle('Copy to clipboard');
}

if ($totalHours > 0) {
    $workflow->item()
        ->title(pluralize('hour', $totalHours))
        ->arg(pluralize('hour', $totalHours))
        ->subtitle('Copy to clipboard');
}

if ($totalMinutes > 0) {
    $workflow->item()
        ->title(pluralize('minute', $totalMinutes))
        ->arg(pluralize('minute', $totalMinutes))
        ->subtitle('Copy to clipboard');
}

$workflow->output();


/**
 * Format numbers and pluralize as needed.
 */
function pluralize($label, $value): string
{
	return number_format($value) . " " . $label . ($value !== 1 ? "s" : "");
}
