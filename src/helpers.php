<?php

/**
 * Format numbers and pluralize as needed.
 */
function pluralize(string $label, int|float $value): string
{
    return number_format($value) . " " . $label . ($value !== 1 ? "s" : "");
}

/**
 * Calculate date span information between two dates.
 *
 * @param string $date1String First date string
 * @param string|null $date2String Second date string (defaults to now)
 * @return array{
 *     diff: DateInterval,
 *     minutes: int,
 *     hours: int,
 *     days: int,
 *     totalDays: int,
 *     totalHours: int,
 *     totalMinutes: int,
 *     weeks: float,
 *     businessWeeks: float,
 *     months: int,
 *     years: int,
 *     sign: string,
 *     complete: string
 * }|null Returns null if dates are invalid
 */
function calculateDateSpan(string $date1String, ?string $date2String = null): ?array
{
    try {
        $date2 = $date2String !== null ? new DateTime($date2String) : new DateTime();
        $date1 = new DateTime($date1String);
    } catch (Exception $e) {
        return null;
    }

    $diff = $date1->diff($date2);

    $minutes       = (int) $diff->format('%i');
    $hours         = (int) $diff->format('%h');
    $days          = (int) $diff->format('%d');
    $totalDays     = (int) $diff->format('%a');
    $totalHours    = $totalDays * 24;
    $totalMinutes  = $totalHours * 60;
    $weeks         = (int) $diff->format('%a') / 7;
    $businessWeeks = (int) $diff->format('%a') / 5;
    $months        = (int) $diff->format('%m');
    $years         = (int) $diff->format('%y');
    $sign          = $diff->format('%R');

    if ($totalDays > 1) {
        $days++;
    }

    // Build complete string
    $complete = [];

    if ($years) { $complete[] = pluralize('year', $years); }
    if ($months) { $complete[] = pluralize('month', $months); }
    if ($days) { $complete[] = pluralize('day', $days); }
    if ($hours) { $complete[] = pluralize('hour', $hours); }
    if ($minutes) { $complete[] = pluralize('minute', $minutes); }

    if (count($complete) > 1) {
        $completeString = implode(', ', array_slice($complete, 0, -1));
        $completeString .= " and " . $complete[count($complete) - 1];
    } else {
        $completeString = implode(', ', $complete);
    }

    return [
        'diff' => $diff,
        'minutes' => $minutes,
        'hours' => $hours,
        'days' => $days,
        'totalDays' => $totalDays,
        'totalHours' => $totalHours,
        'totalMinutes' => $totalMinutes,
        'weeks' => $weeks,
        'businessWeeks' => $businessWeeks,
        'months' => $months,
        'years' => $years,
        'sign' => $sign,
        'complete' => $completeString,
    ];
}
