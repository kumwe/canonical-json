<?php

declare(strict_types=1);

namespace Kumwe\CanonicalJson;

/**
 * Versioned semantic identity carried across package, Engine, extension and App evidence.
 *
 * This enum selects no executor. Its backed value names the normative document and corpus.
 *
 * @since 0.1.0
 */
enum Profile: string
{
    /**
     * Generic PHP array/scalar semantics with explicit bounded execution and binary64 encoding.
     *
     * @since 0.1.0
     */
    case GenericV1 = 'kumwe-canonical-json/generic-v1';
}
