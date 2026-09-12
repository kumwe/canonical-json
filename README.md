# Kumwe Canonical JSON

[![Packagist version](https://img.shields.io/packagist/v/kumwe/canonical-json)](https://packagist.org/packages/kumwe/canonical-json)
[![CI](https://github.com/kumwe/canonical-json/actions/workflows/ci.yml/badge.svg?branch=main)](https://github.com/kumwe/canonical-json/actions/workflows/ci.yml?query=branch%3Amain)
[![PHP requirement](https://img.shields.io/packagist/dependency-v/kumwe/canonical-json/php)](composer.json)
[![License](https://img.shields.io/packagist/l/kumwe/canonical-json)](LICENSE)

Canonical JSON contracts, bounded operation metadata and a language-neutral conformance corpus
for deterministic encoding and digests. The namespace is `Kumwe\CanonicalJson`.

The package provides the `CanonicalEncoder` interface, `Profile`, `FindingCode` and `Limits`.
It ships no encoder implementation, PHP fallback, native class, container provider or runtime selector.
Computation owns the execution adapter; Core owns its composition and application behavior.

## Installation

Requires PHP 8.5 on a 64-bit platform. Install the published release with an exact pre-1.0 pin:

```bash
composer require kumwe/canonical-json:0.1.1
```

See [published releases](https://github.com/kumwe/canonical-json/releases) and
[compatibility and release verification](docs/releasing.md) before upgrading.

## Usage

```php
<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use Kumwe\CanonicalJson\Limits;
use Kumwe\CanonicalJson\Profile;

$profile = Profile::GenericV1;
$limits = new Limits(maxDepth: 32, maxNodes: 10000, maxOutputBytes: 1048576);
```

These immutable values describe semantics and budgets; constructing them does not encode a payload.
Receive a conforming `CanonicalEncoder` through a constructor or factory when execution is needed.
Its `encode(mixed): string` returns canonical UTF-8 bytes, and `digest(mixed): string` returns
64 lowercase hexadecimal SHA-256 characters over exactly those bytes. Both operations enforce the
same bounds and ordered refusals. See the [complete public API](docs/public-api.md) and
[standalone metadata example](examples/typed-consumer.php).

The GenericV1 profile preserves list order, sorts map keys by UTF-8 byte order, preserves finite
binary64 zero fractions, and defines Unicode, slash and control escaping. It is not RFC 8785 JCS.
Exact decimal strings remain strings; Conversion owns decimal semantics. Definition, SDK, Runtime,
OpenAPI and Studio profiles have separate owners and must not be replaced without conformance proof.

## Core contract

Core installs and verifies the package version, public manifests and corpus identity, and supplies
the execution adapter explicitly. This package supplies no configuration keys, services, factories
or aliases and owns no actor, tenant, transaction or mutable execution state.

Core retains authorization, persistence, audit, idempotency, readiness, lifecycle and delivery tests.
Replacing an active executor requires byte/digest compatibility and bounded-failure proof before
removing its implementation tests. A published semantic package alone does not establish native
execution readiness. See the [Core contract](docs/core-contract.md) and
[integration guidance](docs/integration.md).

## Documentation

- [Normative semantics and exact bounds](docs/semantics.md)
- [Corpus representation and replay](docs/corpus.md)
- [Architecture](docs/architecture.md) and [profile ownership](docs/ownership.md)
- [Package and host test ownership](docs/test-ownership.md)
- [Security and compatibility](docs/security.md)
- [Release policy](docs/releasing.md) and [machine-readable release evidence](docs/release-record.md)

## Development

```bash
composer install
composer check
```

The complete gate runs behavior and corpus tests, static analysis, API and manifest checks,
architecture and documentation checks, release integrity tests, and a ZIP installed as a no-dev
dependency in a fresh Composer project. Test-only oracles are excluded from consumer archives.
CI also runs the release automation regression suite and checks the no-dev autoloader and example.

Licensed under [Apache-2.0](LICENSE).
