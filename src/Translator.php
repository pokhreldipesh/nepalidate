<?php

namespace Dipesh\NepaliDate;

class Translator
{
    /**
     * Get digits by language.
     *
     * @param mixed $lang
     *
     * @return mixed
     */
    public function digits($lang)
    {
        return include __DIR__."/lang/{$lang}/digits.php";
    }

    /**
     * Get months by language.
     *
     * @param mixed $lang
     *
     * @return mixed
     */
    public function months($lang)
    {
        return include __DIR__."/lang/{$lang}/months.php";
    }

    /**
     * Get weeks by language.
     *
     * @param mixed $lang
     *
     * @return mixed
     */
    public function weeks($lang)
    {
        return include __DIR__."/lang/{$lang}/weeks.php";
    }

    /**
     * Get gate by language.
     *
     * @param mixed $lang
     *
     * @return mixed
     */
    public function gate($lang)
    {
        return include __DIR__."/lang/{$lang}/gate.php";
    }

    /**
     * Get all translator data.
     *
     * @param mixed $lang
     *
     * @return array
     */
    public function __invoke($lang)
    {
        return [
            'digits' => $this->digits($lang),
            'months' => $this->months($lang),
            'weeks' => $this->weeks($lang),
            'gate' => $this->gate($lang),
        ];
    }
}
