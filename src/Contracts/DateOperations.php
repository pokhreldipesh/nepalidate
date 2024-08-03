<?php
namespace Dipesh\NepaliDate\Contracts;

interface DateOperations
{
   public function total(int $year, int $month, int $day):int;
   public function getTotalDaysFromBaseDate(Date|string $date):int;
   public function addDays(int $day): string;
   public function weekDay(): int;
}
