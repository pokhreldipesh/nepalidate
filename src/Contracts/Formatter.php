<?php
namespace Dipesh\NepaliDate\Contracts;
interface Formatter
{
    public function setUp(Date $calender, Language $lang): static;
    public function format(string $format): string|Date;

}
