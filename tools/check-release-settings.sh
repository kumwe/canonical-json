#!/usr/bin/env bash
# Read-only maintainer preflight. The immutable-release API needs administrator read access.
set -euo pipefail

repository="${1-kumwe/canonical-json}"
if [[ "$#" -gt 1 || ! "$repository" =~ ^[A-Za-z0-9][A-Za-z0-9-]*/[A-Za-z0-9_.-]+$ \
  || "${repository#*/}" == . || "${repository#*/}" == .. ]]; then
  echo 'Usage: check-release-settings.sh [owner/repository]' >&2
  exit 2
fi
for dependency in gh jq; do
  if ! command -v "$dependency" >/dev/null 2>&1; then
    echo "Missing required command: $dependency" >&2
    exit 2
  fi
done
tool="$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")" && pwd)/check-release-integrity.sh"
settings="https://github.com/$repository/settings"
status=0

read_metadata() {
  gh api --method GET -H 'Accept: application/vnd.github+json' \
    -H 'X-GitHub-Api-Version: 2026-03-10' "repos/$repository/$1"
}

if branch="$(read_metadata branches/main)"; then
  if bash "$tool" branch <<< "$branch" >/dev/null 2>&1; then
    echo 'PASS: main has active protection.'
  else
    echo "FAIL: Protected main was not confirmed. Activate protection for main at $settings/rules." >&2
    status=1
  fi
else
  echo "FAIL: Could not read main protection. Check repository access and $settings/rules." >&2
  status=1
fi

# Check this independently so one missing setting does not hide the other.
if immutable="$(read_metadata immutable-releases)"; then
  if jq -es 'length == 1 and (.[0] | type == "object" and .enabled == true)' \
    <<< "$immutable" >/dev/null 2>&1; then
    echo 'PASS: Immutable releases are enabled.'
  else
    echo "FAIL: Immutable releases were not confirmed. Enable release immutability under Releases at $settings." >&2
    status=1
  fi
else
  echo 'FAIL: Could not read immutable-release settings; a 404 can mean disabled or inaccessible.' >&2
  echo "Use an existing gh login with Administration read access and check Releases at $settings." >&2
  status=1
fi

exit "$status"
