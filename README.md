# Kumwe Canonical JSON

Normative generic canonical JSON semantics and conformance fixtures for the native streaming engine
and deterministic digests. Canonical namespace: `Kumwe\CanonicalJson`.

This package supplies **semantic metadata, not an encoder**. There is no `encode()` or `digest()`
implementation, PHP fallback, native class, provider or runtime selector in this archive.

## Installation and direct use

PHP 8.5 on a 64-bit platform is supported. After a maintainer publishes and independently verifies
the recorded release, install its exact pre-1.0 version with Composer. Do not consume a branch.

```php
use Kumwe\CanonicalJson\Limits;
use Kumwe\CanonicalJson\Profile;

$profile = Profile::GenericV1;
$budgets = new Limits(maxDepth: 32, maxNodes: 10000, maxOutputBytes: 1048576);
```

These immutable values are safe to share across operations. They hold no actor, tenant, environment,
transaction or mutable runtime state. There are no configuration keys, services, factories or aliases.
Computation owns future interface binding to a compatible native extension and readiness enforcement.

## Contract and examples

- [Normative semantics and exact bounds](docs/semantics.md)
- [Complete public API](docs/public-api.md)
- [Corpus representation and replay](docs/corpus.md)
- [Native and App ownership](docs/ownership.md)
- [Integration and test migration](docs/integration.md)
- [Run the standalone metadata example](examples/typed-consumer.php)

The profile preserves list order, sorts non-list array keys by UTF-8 byte order, preserves finite
binary64 zero fractions, and emits unescaped Unicode/slashes with defined control escaping.
It is not RFC 8785 JCS. Exact decimal strings belong to Conversion; strings are never coerced to numbers.

The PHP profile enum, finding enum and budgets are directly constructible metadata. Extending semantics
requires a reviewed profile/corpus successor, not subclassing or a replacement local canonicalizer.

## Validation and adoption

Run `composer install`, then `composer check`. This checks all package behavior and corpus fixtures,
strict analysis, documentation, architecture, manifests and a real ZIP installed as a no-dev dependency
in a fresh Composer consumer. The frozen test-only oracle is excluded from every consumer archive.

Phase 2 may adopt semantics and metadata after immutable release verification. It keeps the current
App production executor and its implementation tests. Only Computation's later native cutover can
remove that executor and those tests. App continues testing composition, persistence, authorization,
audit/idempotency integration, lifecycle, delivery and recovery.

See [MIGRATION-HANDOFF.md](MIGRATION-HANDOFF.md) for exact paths and gates,
[releasing](docs/releasing.md) for release-on-record and compatibility,
and [security](docs/security.md) for resource refusals and reporting. Licensed Apache-2.0.
