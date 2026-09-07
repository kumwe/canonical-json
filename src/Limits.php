<?php

declare(strict_types=1);

namespace Kumwe\CanonicalJson;

use InvalidArgumentException;

/**
 * Immutable operation budgets accompanying a canonical semantic profile.
 *
 * Budgets are data, not a validator or encoder. Executors must enforce them before unbounded allocation.
 * A consumer may tighten defaults; raising a ceiling requires a separately versioned profile review.
 *
 * @since 0.1.0
 */
final readonly class Limits
{
    /**
     * Construct explicit bounded execution metadata, using the generic-v1 maxima by default.
     *
     * @param int $maxDepth Maximum root-relative node depth, from 0 through 64 inclusive.
     * @param int $maxNodes Maximum visited value nodes, from 1 through 100000 inclusive.
     * @param int $maxOutputBytes Maximum canonical output bytes, from 1 through 8388608 inclusive.
     *
     * @throws InvalidArgumentException When any budget is outside the generic-v1 permitted range.
     *
     * @since 0.1.0
     */
    public function __construct(
        public int $maxDepth = 64,
        public int $maxNodes = 100000,
        public int $maxOutputBytes = 8388608,
    ) {
        if ($maxDepth < 0 || $maxDepth > 64) {
            throw new InvalidArgumentException('Canonical depth must be between 0 and 64.');
        }
        if ($maxNodes < 1 || $maxNodes > 100000) {
            throw new InvalidArgumentException('Canonical node budget must be between 1 and 100000.');
        }
        if ($maxOutputBytes < 1 || $maxOutputBytes > 8388608) {
            throw new InvalidArgumentException('Canonical output budget must be between 1 and 8388608 bytes.');
        }
    }
}
