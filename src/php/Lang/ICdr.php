<?php

declare(strict_types=1);

namespace Phel\Lang;

interface ICdr
{
    /**
     * Return the sequence without the first element. If the sequence is empty returns null.
     */
    public function cdr(): ?ICdr;
}
