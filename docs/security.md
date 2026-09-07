# Security and compatibility

Report a vulnerability privately through GitHub security reporting or to the Kumwe maintainers.
Do not include secrets, production payloads or exploitable customer data in a public issue.
Patch releases preserve profile bytes and code identity. Any profile byte/refusal or limit change
requires a reviewed successor profile/corpus and a pre-1.0 minor release; public API removal likewise.
The maintainer publishes advisories and coordinates affected package/native/App successor releases.

The semantic package executes no payload and never invokes JsonSerializable, callbacks, native code,
network, filesystem or process state from its public API. Native executors must enforce bounded
traversal, output, allocation and cancellation, then fail closed on incompatible corpus/API identity.
Strings and keys are not copied into diagnostics. There is no fallback on rejection or module absence.

The App source had unbounded normalization and ambient float precision. Those historical risks are
documented, not repaired by shipping another PHP executor. The Computation cutover must prove bounded
failure and persisted digest compatibility before removing App's current implementation and tests.
