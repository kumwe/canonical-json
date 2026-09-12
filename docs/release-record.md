---
schema: "kumwe-package-release-record/v1"
artifact_kind: "framework_php"
migration_id: "KUMWE-MIG-2026-007"
change_set: "KUMWE-CS-2026-007"
source:
  app:
    repository: "https://github.com/kumwe/app"
    baseline_commit: "960ce8ec00cf724a7cae03e5ba09c4852c9ab54e"
    examined_paths:
      - "src/Shared/Domain/CanonicalJson.php"
      - "src/BusinessDefinition/Domain/CanonicalDefinitionJson.php"
      - "src/Extension/Runtime/RuntimeCanonicalJson.php"
      - "src/OpenApi/Infrastructure/CanonicalOpenApiJson.php"
      - "src"
      - "tests"
      - "composer.json"
      - "composer.lock"
      - "AGENTS.md"
      - "docs/architecture/governance/decisions.md"
      - "build/capability-index/v1.json"
    old_namespace_roots: []
    capability_index_sha256: "87ded886f35f74878ca9eb8db4c36e23d681c4a49891f76dfc3210f385a7ce39"
  semantic_inputs:
    -
      owner: "kumwe/app"
      version_or_commit: "960ce8ec00cf724a7cae03e5ba09c4852c9ab54e"
      manifest_or_corpus: "src/Shared/Domain/CanonicalJson.php"
      sha256: "df80b60dd4cf382a443894b69971fb92fe31cdc62318e5d1b185571534af41a1"
  examined_dependencies:
    - "Producer 0.2.0 canonical profile is distinct: UTF-16, safe integers, objects, SRI digest."
    - "Extension SDK 0.2.4 has a distinct definition/manifest canonicalizer that rejects floats."
    - "Conversion 0.1.2 owns exact decimal values; no runtime dependency selected."
    - "No Kumwe runtime dependencies; inspected installed sources establish exclusions only."
target:
  repository: "https://github.com/kumwe/canonical-json"
  artifact_identity: "kumwe/canonical-json"
  canonical_namespace_or_abi: "Kumwe\\CanonicalJson"
ownership:
  responsibility: "Generic canonical JSON semantic profile, budgets, findings and language-neutral corpus."
  non_responsibilities:
    - "Production encoding, hashing, native ABI implementation and binding."
    - "Definition, Runtime, Producer/Studio and OpenAPI canonical profiles."
    - "App authorization, persistence, transactions, crypto policy, delivery and readiness."
  allowed_dependency_ceiling: []
  implementation_owner: "kumwe/canonical-json"
  next_consumer: "kumwe/app"
  public_manifests:
    -
      path: "resources/public-api/v1.json"
      sha256: "43b126b3215555482b3d439cc2521808f16d252a99e0036138d6607a3c3a3465"
    -
      path: "resources/capabilities/v1.json"
      sha256: "fba51564639daa35180b354084649bacff466728ba531c2104b0a93d5136143b"
    -
      path: "resources/service-map/v1.json"
      sha256: "a9426334ed36972ab82781a64ace58bbac058aed884d5aa091354bbeb82e4108"
    -
      path: "resources/semantics/v1.json"
      sha256: "621b7dfae136ce7635a234f047e7b744a06e2b7ad57c28ae09aeaa4a4308979c"
    -
      path: "resources/ownership/v1.json"
      sha256: "a2f66ab45521f54f1fc8145f6805ee8827cf098e44f33d8bae8c0f4f9fb34934"
    -
      path: "resources/corpus/v1.json"
      sha256: "84d21b12e7a2bfd752356d9a6e664bcb332e209d19017e7634e7485a4fa4e250"
  intentionally_excluded:
    - "src/Shared/Domain/CanonicalJson.php remains the App production executor."
    - "Definition, Runtime, OpenAPI and installed Producer/SDK canonicalizers retain separate ownership."
    - "tests/Oracle is test-only and excluded from every runtime archive."
    - "Native concrete signatures require a separate joint ownership agreement before native implementation."
framework_php:
  composer_package: "kumwe/canonical-json"
  canonical_namespace: "Kumwe\\CanonicalJson"
  public_api_manifest: "resources/public-api/v1.json"
  capability_manifest: "resources/capabilities/v1.json"
  service_map: "resources/service-map/v1.json"
  extracted_symbols: []
  consumers:
    app_code:
      - "src/Administrator/Http/Handler/AdministratorAccessControlHandler.php"
      - "src/Application/Automation/ChangePlan.php"
      - "src/Application/Automation/IdempotencyRecord.php"
      - "src/Application/Automation/IdempotencyResult.php"
      - "src/Application/Automation/JobEnvelope.php"
      - "src/Application/Automation/ScheduleOccurrenceKey.php"
      - "src/Audit/Domain/AuditAnchorDigest.php"
      - "src/Audit/Domain/AuditEventDigest.php"
      - "src/BusinessIntegration/Application/EventContractRegistry.php"
      - "src/BusinessIntegration/Domain/RecordedEventEnvelope.php"
      - "src/BusinessIntegration/Infrastructure/DoctrineOutboxStore.php"
      - "src/BusinessReporting/Infrastructure/DoctrineProjectionStore.php"
      - "src/Identity/Application/Administration/AccessControlService.php"
    configuration_and_di: []
    reflection_and_string_references: []
    fixtures_and_examples:
      - "tests/Unit/Application/Automation/CanonicalJsonTest.php"
      - "tests/Unit/Identity/Application/Administration/AccessControlServiceTest.php"
    external:
      - "kumwe/engine C ABI corpus replay"
      - "kumwe/kumwe-engine PHPT corpus replay"
      - "kumwe/computation semantic adapter/readiness agreement"
  dependency_injection:
    mode: "direct"
    provider: null
    factories: []
    aliases: []
    service_lifetimes:
      - "Immutable semantic values are supplied explicitly per operation."
    configuration_keys: []
    provider_absence_reason: "Explicit CanonicalEncoder port; Computation owns native binding."
native_cpp: null
php_extension: null
tests:
  moved_or_added:
    - "tests/Case/CorpusTest.php"
    - "tests/Case/MetadataTest.php"
    - "tests/Case/CanonicalEncoderTest.php"
    - "tests/Case/ArchitectureTest.php"
    - "tests/Oracle/CanonicalJson.php frozen test-only semantic oracle"
    - "tests/Oracle/Replay.php test-only bounded replay"
  remain_in_app_or_consumer:
    - "tests/Unit/Application/Automation/CanonicalJsonTest.php"
    - "tests/Unit/Identity/Application/Administration/AccessControlServiceTest.php"
    - "App audit, job, idempotency, integration, projection, security, database, lifecycle and delivery tests."
    - "Definition/Runtime/OpenAPI/Producer/Studio executor tests remain with those distinct owners."
  split_tests:
    - "Corpus preserves generic source ordering/INF semantics; App still tests its active executor."
  prohibited_duplicates:
    - "Do not copy this package unit/corpus suite into App against vendor types."
    - "Do not remove active PHP executor tests during semantic-only adoption."
  corpora:
    - "resources/corpus/v1.json; digest in resources/corpus/v1.sha256; 79 exact vectors."
documentation:
  charter: "CHARTER.md"
  readme: "README.md"
  public_api: "docs/public-api.md"
  architecture: "docs/architecture.md"
  integration_or_consumer: "docs/integration.md"
  examples:
    - "examples/typed-consumer.php"
  changelog_record: "CHANGELOG.md / 0.1.1"
release_expectations:
  version_policy: "SemVer; pre-1.0 exact pins; profile changes need reviewed minor successor."
  expected_artifact_types:
    - "Composer source ZIP"
    - "GitHub immutable release and version tag"
  required_checks:
    - "composer check"
    - "Complete reusable Package gate and release automation regression tests."
    - "Independent source/tag/archive/API/corpus/registry verification after human merge."
  required_registry_or_installer: "Packagist / Composer"
  required_external_attestation: true
consumer_contract:
  permitted_only_when:
    - "The selected published artifact is independently verified under docs/releasing.md."
    - "Fresh external RELEASE-ATTESTATION.yaml verifies the exact artifact, manifests, corpus and clean consumer."
    - "App source drift has been reviewed without deleting newer portable behavior."
  consumer_repository: "kumwe/app"
  dependency_or_native_change: "Exact-pin verified semantic package only; no extension requirement or runtime switch."
  namespace_or_api_replacements: []
  files_to_update:
    - "composer.json"
    - "composer.lock"
    - "build/capability-index/v1.json"
    - "build/capability-index/v1.sha256"
    - "docs/architecture/capability-index.md"
    - "CHANGELOG.md"
    - "docs/architecture/migrations/KUMWE-MIG-2026-007.yaml"
    - "docs/architecture/migrations/change-sets/KUMWE-CS-2026-007.yaml"
    - "docs/architecture/non-roadmap/NRM-2026-009.yaml"
  files_to_remove: []
  tests_to_remove: []
  tests_to_retain_or_add:
    - "Retain generic App executor tests and all host responsibility tests."
    - "Add exact installed semantic profile/corpus identity and capability-index integration proof."
  di_or_provisioning_changes:
    - "No provider is supplied; execution readiness belongs to Computation."
  capability_index_changes:
    - "Record canonical-json.semantics from the exact installed package manifests."
  changelog_and_evidence_changes:
    - "Record exact semantic package identity; package installation does not prove execution readiness."
    - "Commit the external release attestation unchanged with the installed dependency identity."
  verification_commands:
    - "composer install"
    - "composer check-platform-reqs"
    - "composer kumwe:capability-index"
    - "composer kumwe:capability-index-check"
    - "composer kumwe:core-growth-check"
    - "composer qa"
    - "Run the App database/browser/deployment and affected acceptance CI matrix."
governance:
  completion_claim: false
decisions:
  - "Three metadata types and the CanonicalEncoder port; production execution remains outside this package."
  - "Generic-v1 freezes finite binary64 rendering with serialize_precision=-1 behavior."
  - "Explicit input/depth/node/output limits are safety refinements requiring later App cutover proof."
  - "CanonicalEncoder is an injected port; no provider, concrete executor, native class or PHP fallback."
  - "Empty extracted_symbols is intentional: the new DTO/enums have no historical App FQCN."
blockers:
  - "Consumer adoption requires independent verification of the selected published artifact."
  - "Native selection requires compatible concrete FQCN/ABI, Engine and extension identities."
  - "Before initial publishing merge, maintainers must protect main and enable immutable releases."
---

# Release contract

## Package contract

This `kumwe-package-release-record/v1` record preserves consumer qualification requirements,
source provenance and exact manifest/corpus identities. Migration and change-set IDs are stable
cross-references for existing evidence, not workflow state. Current responsibilities are defined
in [the Core contract](core-contract.md).

## Public API and responsibility

The package owns the [generic execution interface and semantic values](public-api.md).
It supplies no encoder implementation or native binding. Core owns authority, storage and composition.

## Dependencies and semantic inputs

The source baseline and checksums above identify the frozen semantic oracle and inspected
profiles. They are provenance, not a current inventory of another repository. PHP is the only
runtime dependency. Distinct profiles retain their [owners](ownership.md).

## Consumer contract

The `consumer_contract` section records installation, verification, capability-index and
host-retention obligations. Its source paths identify the recorded baseline and must be checked
against current Core source. Follow [integration guidance](integration.md) when composing the package.

## Test ownership

The package owns semantic, API, bounds, corpus and archive proof. Core retains integration and
authority tests and tests for any still-active executor. The exact mappings remain in
[tests/ownership.json](https://github.com/kumwe/canonical-json/blob/main/tests/ownership.json)
and [test ownership guidance](test-ownership.md).

## Consumer verification

Follow [the release policy](releasing.md) to verify the selected published source/tag, archive,
registry coordinate, manifests and clean no-dev consumer. Independent verification is separate
from normal publication. Published versions are listed on
[GitHub Releases](https://github.com/kumwe/canonical-json/releases) and
[Packagist](https://packagist.org/packages/kumwe/canonical-json).

## Compatibility and drift

Compare consumers against the recorded source baseline before changing composition. An executor
replacement requires exact byte/digest compatibility, ordered bounds and refusal recovery proof.
The [normative corpus](corpus.md) remains checksum-bound and must not be regenerated from the
implementation under test.

## Validation

`composer check` validates the package, all six manifest/corpus digests and the clean archive
consumer. The record and Core contract are required archive files. Runtime API, corpus and
ownership manifests remain unchanged by documentation maintenance.
