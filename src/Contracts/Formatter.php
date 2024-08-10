<?php
namespace Dipesh\NepaliDate\Contracts;
interface Formatter
{
    public function __invoke(string $format, Date $date, Language $lang): string;
    public function formatNumber(int $number, Language $language): string;

}
