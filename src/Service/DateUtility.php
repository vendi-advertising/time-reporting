<?php

namespace App\Service;

use DateTimeImmutable;

class DateUtility
{
    public function getCurrentDate(?int $dateStart = null): DateTimeImmutable
    {
        if ($dateStart) {
            $dateTimeObject = DateTimeImmutable::createFromFormat('Ymd', (string)$dateStart);
        } else {
            $dateTimeObject = new DateTimeImmutable;
        }

        return $dateTimeObject->modify('Monday this week');
    }
}