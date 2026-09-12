# Canonical JSON ownership

`kumwe/canonical-json` owns the GenericV1 canonical JSON profile, the `CanonicalEncoder` execution
interface, finite value-space rules, finding vocabulary, immutable budgets and language-neutral corpus.

It defines the semantic contract for conforming executors and deterministic digests. It does not
implement encoding, hashing, streaming, native binding, readiness selection, business definition
semantics, extension trust, cryptography, audit policy or Core orchestration. It has no runtime service,
configuration provider, factory, alias or runtime dependency beyond PHP.

The execution interface is supplied explicitly. Computation owns native adaptation; Engine owns
algorithms and ABI; the PHP extension owns native class registration and binding. Core owns authority,
storage, composition and operational readiness. Distinct Definition, Runtime, Studio, SDK and OpenAPI
canonical profiles keep their owners. See [profile ownership](docs/ownership.md).

Package semantic, API, bounds, manifest and corpus tests belong here. Native execution tests belong
in Engine, PHP binding tests in the extension and host responsibility tests in Core. An executor and
its implementation-only tests remain together until replacement is proven compatible.

See the [Core contract](docs/core-contract.md) for composition and compatibility requirements.
