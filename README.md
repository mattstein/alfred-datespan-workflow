Use this workflow to get the difference between two dates in Alfred. This relies exclusively on PHP 5.3+'s [DateInterval](http://www.php.net/manual/en/dateinterval.format.php) method, which is happy to accept a variety of inputs and works pretty well! Examples:

- `datespan tomorrow`: countdown in hours and minutes
- `datespan 10/10/10`: difference in years, months, days, hours, and minutes—along with total business weeks, weeks, days, hours, and minutes
- `datespan 3/10 to 5/12`: all units above, but the span between zero-hour on each date

You can use any format that [`strtotime`](https://php.net/manual/en/function.strtotime.php) can parse, so I'm sure there are plenty of variations I've not thought to try yet.

Screenshots with a few more examples [on my humble blog](http://workingconcept.com/blog/date-span-alfred-workflow).

Comments, suggestions, criticisms, and pull requests all welcome!
