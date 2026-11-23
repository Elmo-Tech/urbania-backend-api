<?php

namespace App\Http\Requests\Concerns;

use App\Utils\TicketYears;

trait NormalizesTicketYears
{
    protected function prepareForValidation(): void
    {
        if ($this->exists('anno')) {
            $this->merge(['anno' => TicketYears::normalizeInput($this->input('anno'))]);
        }
    }
}
