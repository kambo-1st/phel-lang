<?php

declare(strict_types=1);

namespace Phel\Lang;

/**
 * @template TFirst
 * @template TRest of SeqInterface
 *
 * @extends FirstInterface<TFirst>
 * @extends CdrInterface<TRest>
 * @extends RestInterface<TRest>
 * @extends ConcatInterface<TRest>
 */
interface SeqInterface extends FirstInterface, CdrInterface, RestInterface
{
    public function toArray(): array;
}
