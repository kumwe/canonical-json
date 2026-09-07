# Release protocol

CHANGELOG's newest second-level semantic-version heading is authoritative; Unreleased is skipped and malformed
headings fail closed. 0.1.0 is a proposed initial record, not proof of publication.

CI and release-on-record both run composer check on PHP 8.5. Publication runs only on main push after the full gate,
serializes without cancellation, and grants write permission only to its release job. Actions use reviewed SHA pins.
The API creates a missing tag at the exact tested main SHA only after a confirmed 404. Existing tags must belong to
main history and match their release record; an unpublished tag must match the exact tested commit.
Agents never push tags, enable auto-merge, change repository settings, merge or publish.

Before the first merge that publishes, a maintainer must protect main and enable GitHub immutable releases.
The release job refuses mutations unless github.ref_protected is true and checks the published release's
immutable flag before declaring success. These are separate from green package CI. GitHub immutability applies
only to future releases, so enable it before publication; the workflow cannot repair a mutable prior release.
See https://docs.github.com/en/code-security/how-tos/secure-your-supply-chain/establish-provenance-and-integrity/
prevent-release-changes for the platform policy. No administrative token is introduced into workflows.

The built ZIP installs as a dependency in a fresh no-dev/no-scripts/no-plugins/classmap-authoritative Composer
project. Its package repository points to that ZIP, never a path checkout. Packagist is disabled for the PHP-only
isolated consumer. Manifested symbols and shipped example run via that consumer's autoloader. Archive verification
enforces the reviewed allowlist and absence of tests/tools/vendor/development state.

After initial Packagist submission, its GitHub integration follows tags without workflow credentials. Independent
verification records tag/source, archive digest, manifests, registry coordinate, license/security evidence and clean
consumer in external RELEASE-ATTESTATION.yaml. Never embed the artifact's final digest or invented release claims in
its own handoff.

Exact pre-1.0 pins are required. Release defects need new versions/advisories, not tag movement.
Semantic-only App adoption, Engine/extension implementation and Computation cutover are separately gated tasks.
