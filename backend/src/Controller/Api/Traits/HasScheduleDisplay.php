<?php

declare(strict_types=1);

namespace App\Controller\Api\Traits;

use App\Entity\Station;
use App\Entity\StationSchedule;
use App\Radio\AutoDJ\Scheduler;
use App\Utilities\DateRange;

trait HasScheduleDisplay
{
    use AcceptsDateRange;

    protected function getEvents(
        Station $station,
        DateRange $dateRange,
        Scheduler $scheduler,
        array $scheduleItems,
        callable $rowRender
    ): array {
        $tz = $station->getTimezoneObject();

        $events = [];

        $loopStartDate = $dateRange->start->subDay()->startOf('day');
        $loopEndDate = $dateRange->end->endOf('day');

        foreach ($scheduleItems as $scheduleItem) {
            /** @var StationSchedule $scheduleItem */
            $i = $loopStartDate;

            while ($i <= $loopEndDate) {
                $dayOfWeek = $i->dayOfWeekIso;

                if (
                    $scheduler->shouldSchedulePlayOnCurrentDate($scheduleItem, $tz, $i)
                    && $scheduler->isScheduleScheduledToPlayToday($scheduleItem, $dayOfWeek)
                ) {
                    if($scheduleItem->start_date === $i->format('Y-m-d'))
                    {
                        $rowStart = StationSchedule::getDateTime($scheduleItem->start_time, $tz, $i);
                    }
                    else
                    {
                        $rowStart = StationSchedule::getDateTime("0000", $tz, $i);
                    }
                        
                    if($scheduleItem->end_date === $i->format('Y-m-d'))
                    {
                        $rowEnd = StationSchedule::getDateTime($scheduleItem->end_time, $tz, $i);
                    }
                    else
                    {
                        $rowEnd = StationSchedule::getDateTime("2359", $tz, $i);
                    }

                    // Handle overnight schedule items
                    if ($rowEnd < $rowStart) {
                        $rowEnd = $rowEnd->addDay();
                    }

                    $itemDateRange = new DateRange($rowStart, $rowEnd);
                    if ($itemDateRange->isWithin($dateRange)) {
                        $events[] = $rowRender($station, $scheduleItem, $itemDateRange);
                    }
                }

                $i = $i->addDay();
            }
        }

        return $events;
    }
}
