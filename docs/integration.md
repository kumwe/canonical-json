# Integration and test ownership

There is no provider or executor to register. `Profile`, `FindingCode` and `Limits` are immutable
semantic values supplied explicitly to the future owning execution contract. Direct Composer metadata
adoption does not require a native extension and must not change a production selector.

## App semantic/API adoption

After human merge, immutable publication and a fresh external release attestation, exact-pin the
verified package in App and regenerate its lock, capability index and migration evidence. The examined
baseline contains no duplicate profile/limits/finding DTO classes to delete, so this handoff has no
historical-to-new runtime FQCN replacement. Do not replace `CanonicalJson::encode()` with a metadata type.

The 13 production and two test files in `docs/consumer-inventory.json` identify existing direct generic
encoder consumers. At semantic adoption, retain those imports and calls. New metadata/corpus readiness
integration may refer to the package release without switching execution or requiring an extension.

`tests/Unit/Application/Automation/CanonicalJsonTest.php` remains because it tests the still-running
App executor. The corpus preserves its ordering/INF assertions as package semantic requirements, but
its executor test removal belongs exclusively to Computation's later runtime cutover. Keep
`tests/Unit/Identity/Application/Administration/AccessControlServiceTest.php` and all audit, job,
idempotency, integration, projection, authorization, database, deployment and delivery responsibility
suites. Keep Definition/Runtime/Producer/Studio/OpenAPI tests with their separate owners.

## Final native cutover

Computation's separate successor must consume the verified extension and corpus tuple. Extension
provisioning/readiness merges first. Computation alone then changes execution and removes
`src/Shared/Domain/CanonicalJson.php` plus its executor-only tests after parity and persisted digest
compatibility are proven. App tests should assert composed outcomes and readiness/corpus compatibility,
not instantiate a vendor encoder to repeat its unit suite. No global deletion based on test directory
names is permitted: split mixed tests and retain host authority/integration assertions.

The native Engine owns algorithms, streaming/batching, bounds, ABI, fuzz/sanitizer and performance tests.
The extension owns PHP marshalling/handle/lifecycle/PHPT tests. This package owns semantic identity,
limits, finding vocabulary, corpus and public API tests. This split removes repeated framework testing
from App at the correct implementation cutover, without discarding proof for a still-active executor.
