# Core contract

The consuming Kumwe Core application is maintained in [kumwe/app](https://github.com/kumwe/app).
This document defines the package boundary independently of any integration branch or rollout status.

## Package responsibilities

`kumwe/canonical-json` owns the GenericV1 semantic profile, the `CanonicalEncoder` execution
interface, `Profile`, `FindingCode`, `Limits`, and the 79-case language-neutral corpus.
Its [public API](public-api.md), [semantics](semantics.md) and
[ownership manifest](../resources/ownership/v1.json) define the supported surface.

The package has no runtime dependency besides PHP. Metadata is immutable and safe to share.
It does not provide an executor, configuration provider, factory, alias, service locator,
extension selector, native class, I/O, authorization, storage or transaction management.

## Composition and execution

Core passes the semantic values and execution port explicitly through constructors or factories.
Computation owns native adaptation and readiness enforcement. Engine owns the C++ implementation
and C ABI; `kumwe/kumwe-engine` owns PHP marshalling and native class registration.

A conforming executor returns complete GenericV1 bytes or a refusal without partial output, payload
callbacks or I/O. Digesting applies every encoding bound, including output bytes. Operation state
must be isolated so a refused value cannot poison the next call. See
[CanonicalEncoder](public-api.md#kumwecanonicaljsoncanonicalencoder) for the full method contract.

Core owns actor and tenant authority, composition, persistence, transaction boundaries, audit and
idempotency integration, lifecycle, delivery and recovery. Canonicalization supplies deterministic
bytes; it does not decide what Core is authorized to encode, hash, store or publish.

## Compatibility and consumer verification

Use exact pre-1.0 Composer pins. Verify the installed package identity, public manifests and corpus
digest and record the capability `canonical-json.semantics` in the host's capability index.
Publication and independent downstream verification are distinct evidence states; follow
[releasing](releasing.md) for their requirements.

Installing semantic metadata does not change an active executor. Before replacing one, prove
consumer envelope limits, ordered refusals, float precision settings and compatibility with persisted
digests. Provision and verify the native extension before selecting native execution. Never infer
execution readiness from a Composer install or a green semantic-package build.

The [consumer inventory](consumer-inventory.json) preserves exact source and oracle provenance at
its stated commit. Its paths are baseline evidence, not a claim about the current Core tree.
Review drift before applying a replacement. Definition, Runtime, OpenAPI, SDK and Producer/Studio
profiles retain their distinct [ownership and semantics](ownership.md).

## Test ownership

The package owns semantic values, bounds/refusals, public API, corpus, architecture and archive tests.
Engine owns algorithm, ABI, streaming, allocation, fuzzing and performance tests; the extension owns
binding, handle, lifecycle and PHPT tests. Computation owns adapter and readiness tests.

Core retains composed outcomes, persisted digests, audit/idempotency, authorization, database,
deployment, job/integration, lifecycle, recovery and delivery assertions. Keep implementation tests
while their executor remains active. Remove a duplicate implementation test only with removal of
that implementation after verified replacement; split mixed tests by responsibility.

Core does not copy this package's unit/corpus suite or run test files from `vendor`.
The exact package mappings and baseline host responsibilities remain in
[tests/ownership.json](../tests/ownership.json) and [test ownership guidance](test-ownership.md).
