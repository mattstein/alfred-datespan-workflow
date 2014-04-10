<?php

$timezone_abbreviation = isset($argv[2]) ? $argv[2] : 'PDT';

date_default_timezone_set(timezone_name_from_abbr($timezone_abbreviation));

$icon = "icon.icns";

require( 'workflows.php' ); // by David Ferguson
$wf = new Workflows();

$dates = explode(" to ", trim(@$argv[1])); // split arguments by " to "

for ($i=0; $i < sizeof($dates); $i++)
{ 
	$dates[$i] = str_replace('-', '/', $dates[$i]);
}


/*
 * use the current date and time in lieu of a second argument
 */

$date2 = isset($dates[1]) ? new DateTime($dates[1]) : new DateTime();
$date1 = new DateTime($dates[0]);

$diff = $date1->diff($date2);

// http://www.php.net/manual/en/dateinterval.format.php

$minutes        = intval($diff->format('%i'));
$hours          = intval($diff->format('%h'));
$days           = intval($diff->format('%d'));
$total_days     = intval($diff->format('%a'));
$total_hours    = $total_days*24;
$total_minutes  = $total_hours*60;
$weeks          = intval(intval($diff->format('%a'))/7);
$business_weeks = intval(intval($diff->format('%a'))/5);
$months         = intval($diff->format('%m'));
$years          = intval($diff->format('%y'));
$sign           = $diff->format('%R');

if ($total_days > 1)
{
	$days++;
}


/*
 * a single, complete string; may include years, months, days, hours, minutes, 
 * and "ago" if there's only one paramater and it's in the past
 */

$complete = array();

if ($years)
{
	$complete[] = pluralize('year', $years);
}

if ($months)
{
	$complete[] = pluralize('month', $months);
}

if ($days)
{
	$complete[] = pluralize('day', $days);
}

if ($hours)
{
	$complete[] = pluralize('hour', $hours);
}

if ($minutes)
{
	$complete[] = pluralize('minute', $minutes);
}

if (sizeof($complete) > 1)
{
	$complete_string = implode(', ', array_slice($complete, 0, -1));
	$complete_string .= " and ".$complete[sizeof($complete)-1];
}
else
{
	$complete_string = implode(', ', $complete);
}

if ( ! isset($dates[1]) AND $sign === "+")
{
	$complete_string .= " ago";
}

$wf->result(
	"complete", 
	$complete_string, 
	$complete_string, 
	"copy to clipboard", 
	$icon
);


/*
 * include business weeks if we have them
 */

if ($business_weeks > 0)
{
	$wf->result(
		"business weeks", 
		pluralize('business week', $business_weeks), 
		pluralize('business week', $business_weeks), 
		"copy to clipboard", 
		$icon
	);
}


/*
 * include weeks if we have them
 */

if ($weeks > 0)
{
	$wf->result(
		"weeks", 
		pluralize('week', $weeks), 
		pluralize('week', $weeks), 
		"copy to clipboard", 
		$icon
	);
}


/*
 * if the *total* number of days is different from days factoring into the interval,
 * we should include it because it's probably interesting
 */

if ($total_days > $days) 
{
	$wf->result(
		"days", 
		pluralize('day', $total_days), 
		pluralize('day', $total_days), 
		"copy to clipboard", 
		$icon
	);
}


/*
 * include a total count of hours if we've got them
 */

if ($total_hours > 0) 
{
	$wf->result(
		"hours", 
		pluralize('hour', $total_hours), 
		pluralize('hour', $total_hours), 
		"copy to clipboard", 
		$icon
	);
}


/*
 * include a total count of minutes if we've got them
 */

if ($total_minutes > 0) 
{
	$wf->result(
		"minutes", 
		pluralize('minute', $total_minutes), 
		pluralize('minute', $total_minutes), 
		"copy to clipboard", 
		$icon
	);
}


echo $wf->toxml();


/*
 * make pretty numbers, and add "s"'s if needed
 */

function pluralize($label, $value)
{
	return number_format($value) . " ". $label . ($value != 1 ? "s" : "");
}

?>