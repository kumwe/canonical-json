<?php

declare(strict_types=1);

namespace Kumwe\CanonicalJson;

use InvalidArgumentException;

/**
 * Explicit execution port for the generic canonical JSON semantic profile.
 *
 * Consumers receive this port through their constructors or factories. This package supplies no
 * implementation. Computation owns the native adapter; App retains its existing executor until the
 * separately verified native cutover. Definition, Studio and runtime profiles remain distinct.
 *
 * @since 0.1.1
 */
interface CanonicalEncoder
{
    /**
     * Encode a bounded value using the GenericV1 profile and the implementation's declared Limits.
     *
     * Lists retain order, maps sort keys by byte value, finite binary64 numbers retain zero fractions,
     * and UTF-8 strings use the exact escaping rules in the normative corpus. The operation performs
     * no I/O, invokes no payload callbacks, mutates no input, and returns no partial result on refusal.
     * Each call is independent: a refusal must not corrupt the next operation on the same service.
     *
     * @param mixed $value Null, boolean, integer, finite float, UTF-8 string or recursively bounded array.
     *
     * @return string Complete canonical UTF-8 JSON bytes, reproducible across conforming implementations.
     *
     * @throws InvalidArgumentException When a value or bound violates the profile; ordered FindingCode
     *         identity must be retained by the implementation's documented exception mapping.
     *
     * @since 0.1.1
     */
    public function encode(mixed $value): string;

    /**
     * Fingerprint exactly the bytes encode() would produce under the same profile and bounds.
     *
     * Digesting applies the same validation, ordering and failure semantics as encoding. It may stream
     * internally, but must not skip output-budget validation or expose an intermediate digest.
     *
     * @param mixed $value Value accepted by encode(), subject to identical operation limits.
     *
     * @return string Lowercase hexadecimal SHA-256, exactly 64 ASCII characters.
     *
     * @throws InvalidArgumentException When encoding the value would violate the profile or bounds.
     *
     * @since 0.1.1
     */
    public function digest(mixed $value): string;
}
