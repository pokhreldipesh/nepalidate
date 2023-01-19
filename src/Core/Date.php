<?php

namespace Dipesh\NepaliDate\Core;

interface Date
{
    public function toNepali($date = null);

    public function toEnglish($date = null, $format = 'Y/m/d');

    public function weekDay($format = 'w', $lang = 'np');

    public function day($format = 'd');

    public function month($format = 'm', $lang = 'np');

    public function year($format = 'Y');

    public function addDays($days, $date = null);

    public function subDays($days);

    public function subMonths($months);

    public function addMonths($months);

    public function subYears($years);

    public function diff($date);

    public function format($format = 'Y/m/d');

    public function lang($lang);

    public function __toString();
}
