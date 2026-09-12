# Architecture

`src/` contains four public types: `CanonicalEncoder`, `Profile`, `FindingCode` and `Limits`.
The interface defines execution requirements; the enum and readonly values supply semantic identity,
stable refusal vocabulary and operation budgets. The package contains no encoding or hashing algorithm.

The architecture gate fixes this allowlist and one PSR-4 owner. It rejects Core imports,
runtime selection, native classes, providers, aliases, environment I/O and runtime dependencies
besides PHP. Computation owns the execution adapter and Core supplies it explicitly.

`resources/corpus/` is language-neutral release data. Its replay adapter and frozen oracle live
under `tests/Oracle/` and never ship. Native Engine conformance runs through its C ABI and
extension conformance through PHPT must agree with the same corpus digest. Package replay establishes
semantic evidence, not native execution readiness.

Behavior and refusal tests are supplemented by structural gates that block accidental executors
or development fixtures from consumer archives. The clean-consumer gate installs the built ZIP as a
dependency without dev dependencies, plugins, scripts, an external registry or a path repository,
then loads every documented type and the shipped example.

See [profile ownership](ownership.md), the [Core contract](core-contract.md) and
[release evidence](release-record.md).
