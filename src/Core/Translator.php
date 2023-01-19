<?php

namespace Dipesh\NepaliDate\Core;

class Translator
{
    public $lang = 'en';

    private function setLang($lang)
    {
        $this->lang = $lang;

        return $this;
    }

    /**
     * Get digits by language.
     *
     * @param mixed $lang
     *
     * @return mixed
     */
    private function digits()
    {
        return include __DIR__."/../lang/{$this->lang}/digits.php";
    }

    /**
     * Get months by language.
     *
     * @param mixed $lang
     *
     * @return mixed
     */
    private function months()
    {
        return include __DIR__."/../lang/{$this->lang}/months.php";
    }

    /**
     * Get weeks by language.
     *
     * @param mixed $lang
     *
     * @return mixed
     */
    private function weeks()
    {
        return include __DIR__."/../lang/{$this->lang}/weeks.php";
    }

    /**
     * Get gate by language.
     *
     * @param mixed $lang
     *
     * @return mixed
     */
    private function gate()
    {
        return include __DIR__."/../lang/{$this->lang}/gate.php";
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
        $this->setLang($lang);

        return [
            'digits' => $this->digits(),
            'months' => $this->months(),
            'weeks' => $this->weeks(),
            'gate' => $this->gate(),
        ];
    }
}
