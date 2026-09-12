# Changelog

## Unreleased

### Changed

- Standardize the README with linked package, CI, PHP and license badges and a usable installation example.
- Consolidate current Core responsibilities and integration guidance; correct the documented
  encoder interface ownership.
- Replace the obsolete handover narrative with a compatible machine-readable release record
  and update archive/digest verification.

## 0.1.1

### Added

- Explicit CanonicalEncoder execution port for portable idempotency, audit and integration consumers.
  The contract preserves the generic profile and ordered bounds without shipping a runtime executor.
- Port conformance, digest/output-budget parity and failure-recovery tests; retain all 79 corpus vectors.
- NRM-2026-041: unblock neutral extraction through explicit injection, with native binding still owned
  by Computation and App execution retained until the verified native cutover.

## 0.1.0

- Unify PR and post-rebase release gates, dynamic release identity, tested publication retries,
  and administrator setup across the package family. Preserve immutable release and dependency evidence requirements.

### Added

- Generic canonical JSON semantic profile, portable finding vocabulary and bounded operation metadata.
- Language-neutral conformance corpus and explicit native/PHP FQCN ownership contract.
- Package-owned semantic, boundary, corpus, architecture, public API and clean archive consumer gates.
- Phase 1 handoff for KUMWE-MIG-2026-007 / KUMWE-CS-2026-007, NRM-2026-009.

### Compatibility

- No executor is shipped. App production behavior and its executor tests remain unchanged.
- Explicit depth, node and output bounds tighten previously unbounded traversal. Future cutover must
  prove consumer compatibility before adopting those refusals. Definition/runtime/OpenAPI profiles
  are deliberately not unified with the generic profile.
