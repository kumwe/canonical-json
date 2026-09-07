<?php

declare(strict_types=1);

namespace Kumwe\CanonicalJson;

/**
 * Ordered, language-neutral refusal identities; messages are diagnostic and never compatibility keys.
 *
 * Native bindings transport these values; they do not register this Composer-owned enum.
 *
 * @since 0.1.0
 */
enum FindingCode: string
{
    /**
     * Object (including closure), resource or another unsupported source value.
     *
     * @since 0.1.0
     */
    case UnsupportedType = 'canonical.unsupported-type';

    /**
     * IEEE-754 NaN or either infinity.
     *
     * @since 0.1.0
     */
    case NonFiniteNumber = 'canonical.non-finite-number';

    /**
     * Malformed UTF-8 in a string value or a string key.
     *
     * @since 0.1.0
     */
    case InvalidUtf8 = 'canonical.invalid-utf8';

    /**
     * Traversal would exceed the declared root-relative depth bound.
     *
     * @since 0.1.0
     */
    case DepthLimit = 'canonical.depth-limit';

    /**
     * Traversal would visit more value nodes than allowed.
     *
     * @since 0.1.0
     */
    case NodeLimit = 'canonical.node-limit';

    /**
     * Canonical output would exceed the complete-result byte budget.
     *
     * @since 0.1.0
     */
    case OutputLimit = 'canonical.output-limit';

    /**
     * Raw value/key data exceeds the input admission budget before UTF-8 inspection or sorting.
     *
     * @since 0.1.0
     */
    case InputLimit = 'canonical.input-limit';
}
