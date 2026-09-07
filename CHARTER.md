# Canonical JSON ownership

`kumwe/canonical-json` owns the normative generic canonical JSON profile, finite value-space rules,
stable finding vocabulary, immutable operation budgets, and language-neutral conformance corpus.
It is the semantic owner consumed by future C ABI and PHP extension conformance gates.

This package does not implement encoding, hashing, streaming, native binding, readiness selection,
business definition semantics, extension trust, cryptography, audit policy, or App orchestration.
It has no runtime service, ConfigProvider, factory, alias, or runtime dependency beyond PHP 8.5.

The App generic executor is an examined semantic input, not a moved PHP implementation.
It remains in App until the separately gated Computation runtime cutover. Definition, runtime,
Studio and OpenAPI canonicalizers remain distinct owners. See `docs/ownership.md`.

Only package contracts, fixtures, semantics, limits and manifest tests belong here. Engine execution
tests belong in Engine; PHP binding tests in the extension; host responsibility tests remain in App.

## Explicit generic execution port

`Kumwe\CanonicalJson\CanonicalEncoder` declares `encode(mixed): string` and `digest(mixed): string`.
Portable packages receive this contract explicitly. No executor, container provider or runtime selector
is shipped here. The implementation must preserve GenericV1 bytes, ordered refusals and operation limits;
digest enforces the same limits as encoding. Computation owns the native adapter after verified native
releases. App may adapt its existing executor during the staged adoption, retaining its tests until the
Computation cutover. Distinct Definition, SDK, Runtime and Studio profiles keep their current owners.
