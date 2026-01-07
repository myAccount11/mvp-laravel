<?php

namespace App\Services\V5;

use Carbon\Carbon;

class DateUtilsService
{
    public function getDateOfISOWeek(int $week, int $year): string
    {
        $simple = Carbon::create($year, 1, 1)->addWeeks($week - 1);
        $dayOfWeek = $simple->dayOfWeek;
        
        $ISOweekStart = $simple->copy();
        
        if ($dayOfWeek <= 4) {
            $ISOweekStart->subDays($dayOfWeek - 1);
        } else {
            $ISOweekStart->addDays(8 - $dayOfWeek);
        }
        
        return $ISOweekStart->format('Y-m-d');
    }

    public function addDays($date, int $days): string
    {
        $result = Carbon::parse($date)->addDays($days);
        return $result->format('Y-m-d');
    }
}

