<?php
namespace Dipesh\NepaliDate\Contracts;
interface Formatter
{
    public function setUp(Date $calendar, Language $lang): static;
    public function format(string $format): string|Date;

}
