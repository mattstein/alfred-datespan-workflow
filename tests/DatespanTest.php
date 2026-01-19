<?php

describe('pluralize', function () {
    it('returns singular form for value of 1', function () {
        expect(pluralize('day', 1))->toBe('1 day');
        expect(pluralize('hour', 1))->toBe('1 hour');
        expect(pluralize('minute', 1))->toBe('1 minute');
    });

    it('returns plural form for values other than 1', function () {
        expect(pluralize('day', 0))->toBe('0 days');
        expect(pluralize('day', 2))->toBe('2 days');
        expect(pluralize('hour', 5))->toBe('5 hours');
        expect(pluralize('week', 10))->toBe('10 weeks');
    });

    it('formats large numbers with commas', function () {
        expect(pluralize('minute', 1000))->toBe('1,000 minutes');
        expect(pluralize('hour', 10000))->toBe('10,000 hours');
    });

    it('handles float values', function () {
        expect(pluralize('week', 2.5))->toBe('3 weeks'); // number_format rounds
        expect(pluralize('business week', 4.8))->toBe('5 business weeks');
    });
});

describe('calculateDateSpan', function () {
    it('returns null for invalid dates', function () {
        expect(calculateDateSpan('not a date'))->toBeNull();
        expect(calculateDateSpan('2024-01-01', 'invalid'))->toBeNull();
    });

    it('calculates difference between two dates', function () {
        $result = calculateDateSpan('2024-01-01', '2024-01-08');

        expect($result)->not->toBeNull();
        expect($result['totalDays'])->toBe(7);
        expect($result['weeks'])->toEqual(1);
    });

    it('calculates years and months correctly', function () {
        $result = calculateDateSpan('2023-01-15', '2024-03-15');

        expect($result)->not->toBeNull();
        expect($result['years'])->toBe(1);
        expect($result['months'])->toBe(2);
    });

    it('calculates hours and minutes', function () {
        $result = calculateDateSpan('2024-01-01 10:00:00', '2024-01-01 12:30:00');

        expect($result)->not->toBeNull();
        expect($result['hours'])->toBe(2);
        expect($result['minutes'])->toBe(30);
    });

    it('calculates total hours and minutes', function () {
        $result = calculateDateSpan('2024-01-01', '2024-01-03');

        expect($result)->not->toBeNull();
        expect($result['totalDays'])->toBe(2);
        expect($result['totalHours'])->toBe(48);
        expect($result['totalMinutes'])->toBe(2880);
    });

    it('calculates business weeks', function () {
        $result = calculateDateSpan('2024-01-01', '2024-01-11');

        expect($result)->not->toBeNull();
        expect($result['totalDays'])->toBe(10);
        expect($result['businessWeeks'])->toEqual(2);
    });

    it('returns positive sign for future dates', function () {
        $result = calculateDateSpan('2024-01-01', '2024-01-02');

        expect($result)->not->toBeNull();
        expect($result['sign'])->toBe('+');
    });

    it('returns negative sign for past dates', function () {
        $result = calculateDateSpan('2024-01-02', '2024-01-01');

        expect($result)->not->toBeNull();
        expect($result['sign'])->toBe('-');
    });

    it('builds complete string with multiple components', function () {
        $result = calculateDateSpan('2023-01-01', '2024-03-05');

        expect($result)->not->toBeNull();
        expect($result['complete'])->toContain('year');
        expect($result['complete'])->toContain('month');
        expect($result['complete'])->toContain('day');
        expect($result['complete'])->toContain(' and ');
    });

    it('builds complete string for single component', function () {
        $result = calculateDateSpan('2024-01-01 00:00:00', '2024-01-01 00:05:00');

        expect($result)->not->toBeNull();
        expect($result['complete'])->toBe('5 minutes');
    });
});
