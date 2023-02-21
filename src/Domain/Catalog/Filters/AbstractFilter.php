<?php

namespace Domain\Catalog\Filters;

use Illuminate\Contracts\Database\Eloquent\Builder;

abstract class AbstractFilter
{
    abstract public function title(): string;

    abstract public function key(): string;

    abstract public function apply(Builder $query): string;

    abstract public function value(): array;

    abstract public function view(): string;
}