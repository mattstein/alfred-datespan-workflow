<?php

include 'vendor/autoload.php';

use Alfred\Workflows\Workflow;

$workflow = new Workflow();
$timezoneAbbreviation = isset($argv[2]) ? $argv[2] : 'PDT';

date_default_timezone_set(timezone_name_from_abbr($timezoneAbbreviation));

$dates = explode(" to ", trim(@$argv[1])); // split arguments by " to "

array_map(static function($dateString) {
    return str_replace('-', '/', $dateString);
}, $dates);

/*
 * use the current date and time in lieu of a second argument
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
$total_days     = (int) $diff->format('%a');
$total_hours    = $total_days * 24;
$total_minutes  = $total_hours * 60;
$weeks          = (int) $diff->format('%a') / 7;
$business_weeks = (int) $diff->format('%a') / 5;
$months         = (int) $diff->format('%m');
$years          = (int) $diff->format('%y');
$sign           = $diff->format('%R');

if ($total_days > 1) {
	$days++;
}


/*
 * a single, complete string; may include years, months, days, hours, minutes, 
 * and "ago" if there's only one parameter and it's in the past
 */

$complete = [];

if ($years) { $complete[] = pluralize('year', $years); }
if ($months) { $complete[] = pluralize('month', $months); }
if ($days) { $complete[] = pluralize('day', $days); }
if ($hours) { $complete[] = pluralize('hour', $hours); }
if ($minutes) { $complete[] = pluralize('minute', $minutes); }

if (count($complete) > 1) {
	$complete_string = implode(', ', array_slice($complete, 0, -1));
	$complete_string .= " and ".$complete[count($complete)-1];
} else {
	$complete_string = implode(', ', $complete);
}

if ( ! isset($dates[1]) && $sign === "+") {
	$complete_string .= " ago";
}

$workflow->item()
    ->title($complete_string)
    ->arg($complete_string)
    ->subtitle('Copy to clipboard');


/*
 * include business weeks if we have them
 */

if ($business_weeks > 0) {
    $workflow->item()
        ->title(pluralize('business week', $business_weeks))
        ->arg(pluralize('business week', $business_weeks))
        ->subtitle('Copy to clipboard');
}


/*
 * include weeks if we have them
 */

if ($weeks > 0) {
    $workflow->item()
        ->title(pluralize('week', $weeks))
        ->arg(pluralize('week', $weeks))
        ->subtitle('Copy to clipboard');
}


/*
 * if the *total* number of days is different from days factoring into the interval,
 * we should include it because it's probably interesting
 */

if ($total_days > $days) {
    $workflow->item()
        ->title(pluralize('day', $total_days))
        ->arg(pluralize('day', $total_days))
        ->subtitle('Copy to clipboard');
}


/*
 * include a total count of hours if we've got them
 */

if ($total_hours > 0) {
    $workflow->item()
        ->title(pluralize('hour', $total_hours))
        ->arg(pluralize('hour', $total_hours))
        ->subtitle('Copy to clipboard');
}


/*
 * include a total count of minutes if we've got them
 */

if ($total_minutes > 0) {
    $workflow->item()
        ->title(pluralize('minute', $total_minutes))
        ->arg(pluralize('minute', $total_minutes))
        ->subtitle('Copy to clipboard');
}

$workflow->output();


/*
 * make pretty numbers, and add "s"'s if needed
 */

function pluralize($label, $value): string
{
	return number_format($value) . " ". $label . ($value !== 1 ? "s" : "");
}
