<?php
namespace Dipesh\NepaliDate\Contracts;

interface Date
{
    public function setUp(string $date): void;
    public function day(): int|string;
    public function month(): int|string;
    public function year(): int|string;
}
