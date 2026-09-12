# Integration

The [Core contract](core-contract.md) defines the package, execution and host responsibilities.
The [public API](public-api.md) documents every exported type and refusal.

## Install and compose

Install an exact published pre-1.0 version through Composer and verify the source/tag, registry
coordinate, manifests, corpus digest and clean consumer according to [releasing](releasing.md).
Update the host lock and capability index from the installed package.

Use `Profile`, `FindingCode` and `Limits` directly. Supply `CanonicalEncoder` explicitly to a
consuming constructor or factory. There is no provider, alias or executor to register, and metadata
adoption alone neither requires a native extension nor changes a production selector.

## Replace an existing executor

The [consumer inventory](consumer-inventory.json) records 13 production and two test consumers
at an exact historical Core source commit. Compare that baseline with the current source before
changing imports or calls; no old-to-new executable FQCN replacement is implied by metadata adoption.

Computation owns native adaptation. Verify extension provisioning and readiness before switching
execution. Establish corpus parity, configured bounds, refusal recovery and persisted digest
compatibility before removing an active executor or its implementation-only tests.

Retain all host authority and integration assertions and split mixed tests by responsibility.
Do not replace `CanonicalJson::encode()` with a metadata type, select through a static global,
or fall back silently when the module or its corpus identity is incompatible. Definition, Runtime,
SDK, Producer/Studio and OpenAPI canonicalizers remain under their separate owners.

See [test ownership](test-ownership.md) for package mappings and host retention rules.
