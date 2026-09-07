#!/usr/bin/env bash
# Fail closed on observed release-integrity metadata; no network or mutation occurs here.
set -euo pipefail

refuse_unprotected_main() {
  echo 'Release refused: main must be protected by an active branch rule or ruleset.' >&2
  echo 'A maintainer must activate protection for main in Settings > Rules > Rulesets or Settings > Branches.' >&2
  echo 'Packagist registration does not configure this GitHub release prerequisite.' >&2
  echo 'See docs/releasing.md for repository setup and retry instructions.' >&2
  exit 1
}

case "${1:-}" in
  protected)
    if [[ "$#" -ne 2 || "$2" != true ]]; then
      refuse_unprotected_main
    fi
    ;;
  branch)
    # Use the current branch response, including when a failed release is retried
    # after a maintainer changes protection. Never trust an event-time snapshot.
    if [[ "$#" -ne 1 ]] || ! jq -es '
      length == 1 and (.[0] | type == "object" and .name == "main" and .protected == true)
    ' >/dev/null; then
      refuse_unprotected_main
    fi
    ;;
  published)
    if [[ "$#" -ne 2 || ! "$2" =~ ^(0|[1-9][0-9]*)\.(0|[1-9][0-9]*)\.(0|[1-9][0-9]*)$ ]]; then
      echo 'Release verification requires one exact stable SemVer version.' >&2
      exit 1
    fi
    if ! jq -es --arg tag "v$2" '
      length == 1 and (.[0] | type == "object" and .tag_name == $tag and .draft == false and .prerelease == false
      and .immutable == true and (.published_at | type == "string" and length > 0))
    ' >/dev/null; then
      echo 'Release refused: exact version must be published, stable and immutable.' >&2
      exit 1
    fi
    ;;
  *)
    echo 'Usage: check-release-integrity.sh protected true | branch < branch.json' >&2
    echo '       check-release-integrity.sh published VERSION < release.json' >&2
    exit 2
    ;;
esac
