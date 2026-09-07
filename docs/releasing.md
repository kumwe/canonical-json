# Release protocol

CHANGELOG's newest second-level semantic-version heading is authoritative; Unreleased is skipped and malformed
headings fail closed. 0.1.0 is a proposed initial record, not proof of publication.

CI and release-on-record both run composer check on PHP 8.5. Publication runs only for main after a push or manual
dispatch and after the full gate,
serializes without cancellation, and grants write permission only to its release job. Actions use reviewed SHA pins.
The API creates a missing tag at the exact tested main SHA only after a confirmed 404. Existing tags must belong to
main history and match their release record; an unpublished tag must match the exact tested commit.
Agents never push tags, enable auto-merge, change repository settings, merge or publish.

Before the first merge that publishes, a maintainer must protect main and enable GitHub immutable releases.
The workflow reads the current main branch through GitHub's API before building and again immediately before
release mutations. It rejects unprotected main, malformed responses and API errors, and checks the published
release's immutable flag before declaring success. These are separate from green package CI. GitHub immutability applies
only to future releases, so enable it before publication; the workflow cannot repair a mutable prior release.
GitHub's "Prevent release changes" documentation describes this platform policy.
No administrative token is introduced into workflows.

## Repository setup and recovery

The failed release run [34128868173](https://github.com/kumwe/canonical-json/actions/runs/34128868173)
stopped because main had no active protection. Its package gate had passed; publication had not started.
Packagist registration does not enable GitHub branch protection or release immutability. Retrying the build
without correcting repository settings cannot resolve this prerequisite failure.

A repository maintainer must complete these settings before retrying publication:

1. Open [Settings > Rules > Rulesets](https://github.com/kumwe/canonical-json/settings/rules), create or edit a
   branch ruleset targeting `main`, and set enforcement to **Active**. Require pull requests and block force
   pushes and deletions. An active classic branch protection rule targeting `main` also satisfies the guard.
   A disabled/evaluate-only ruleset or one targeting a different branch does not.
2. Open [Settings > General](https://github.com/kumwe/canonical-json/settings), scroll to **Releases**, and select
   **Enable release immutability** (or confirm the organization enforces it for this repository). GitHub documents
   this policy in [Immutable releases][immutable-releases-docs].
   Set this before the first publication: existing mutable releases are not made immutable by enabling the setting.

Using GitHub CLI authenticated as a maintainer with repository Administration read access, run:

```bash
bash tools/check-release-settings.sh
```

This read-only check reports both settings independently and never changes them. The
[immutable-releases endpoint][immutable-api]
requires Administration read permission. A failed or unavailable lookup is not proof that the setting is enabled;
verify it in Settings. The normal Actions token cannot perform this administrative check, so it is deliberately
not used in CI. The workflow still verifies the actual release's immutable flag after publication.

Once settings are confirmed and this workflow is on main, select **Release on record > Run workflow > main** in
[Actions](https://github.com/kumwe/canonical-json/actions/workflows/release-on-record.yml), or run:

```bash
gh workflow run release-on-record.yml --repo kumwe/canonical-json --ref main
```

Manual dispatch reruns the complete package gate before publishing the recorded version. Other branches are
skipped. Live branch metadata allows a new run to observe corrected settings; rerunning an older workflow
revision still executes that older revision. Keep the recorded version unchanged when no tag or release was
created. Existing tags and releases remain subject to all integrity checks below; never move a published tag.

[immutable-releases-docs]: https://docs.github.com/en/code-security/concepts/supply-chain-security/immutable-releases
[immutable-api]: https://docs.github.com/rest/repos/repos#check-if-immutable-releases-are-enabled-for-a-repository

## Artifact and consumer verification

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
