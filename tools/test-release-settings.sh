#!/usr/bin/env bash
# Exercise the maintainer preflight using isolated gh responses; never call GitHub.
set -euo pipefail
tool="$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")" && pwd)/check-release-settings.sh"
temporary="$(mktemp -d)"
trap 'rm -rf -- "$temporary"' EXIT
export GH_SETTINGS_CALL_LOG="$temporary/calls"
cat > "$temporary/gh" <<'STUB'
#!/usr/bin/env bash
set -euo pipefail
if [[ "$1" != api || "$2" != --method || "$3" != GET ]]; then
  echo 'Unexpected command: settings checks must only use GET.' >&2
  exit 98
fi
endpoint="${!#}"
printf '%s\n' "$endpoint" >> "$GH_SETTINGS_CALL_LOG"
case "$endpoint" in
  repos/*/branches/main)
    printf '%s\n' "$GH_SETTINGS_BRANCH_BODY"
    exit "$GH_SETTINGS_BRANCH_STATUS"
    ;;
  repos/*/immutable-releases)
    printf '%s\n' "$GH_SETTINGS_IMMUTABLE_BODY"
    exit "$GH_SETTINGS_IMMUTABLE_STATUS"
    ;;
  *) exit 99 ;;
esac
STUB
chmod +x "$temporary/gh"
export PATH="$temporary:$PATH"
checks=0
branch='{"name":"main","protected":true}'
immutable='{"enabled":true,"enforced_by_owner":false}'

check() {
  local expected="$1" name="$2" branch_body="$3" immutable_body="$4"
  local branch_status="$5" immutable_status="$6" actual=0 repository
  shift 6
  repository="${1-kumwe/canonical-json}"
  : > "$GH_SETTINGS_CALL_LOG"
  GH_SETTINGS_BRANCH_BODY="$branch_body" GH_SETTINGS_IMMUTABLE_BODY="$immutable_body" \
    GH_SETTINGS_BRANCH_STATUS="$branch_status" GH_SETTINGS_IMMUTABLE_STATUS="$immutable_status" \
    bash "$tool" "$@" > "$temporary/output" 2>&1 || actual=$?
  if [[ "$actual" -ne "$expected" ]]; then
    echo "Settings check failed: $name (expected $expected, got $actual)." >&2
    cat "$temporary/output" >&2
    exit 1
  fi
  local -a calls=()
  mapfile -t calls < "$GH_SETTINGS_CALL_LOG"
  if [[ "${#calls[@]}" -ne 2 || "${calls[0]}" != "repos/$repository/branches/main" \
    || "${calls[1]}" != "repos/$repository/immutable-releases" ]]; then
    echo "Settings check did not independently read both settings: $name." >&2
    exit 1
  fi
  checks=$((checks + 1))
}

check 0 ready "$branch" "$immutable" 0 0
check 0 'explicit repository' "$branch" "$immutable" 0 0 other-owner/.github
check 0 'organization enforced' "$branch" '{"enabled":true,"enforced_by_owner":true}' 0 0
check 1 'both settings disabled' '{"name":"main","protected":false}' '{"enabled":false}' 0 0
for body in '' '{}' '[]' 'null' 'true' 'not-json' '{"name":"main","protected":"true"}' \
  '{"name":"main","protected":1}' '{"name":"other","protected":true}' "$branch $branch"; do
  check 1 'invalid branch response' "$body" "$immutable" 0 0
done
for body in '' '{}' '[]' 'null' 'true' 'not-json' '{"enabled":"true"}' '{"enabled":1}' \
  '{"enabled":false}' "$immutable $immutable"; do
  check 1 'invalid immutable response' "$branch" "$body" 0 0
done
for api_status in 1 4 22; do
  check 1 'branch API error' "$branch" "$immutable" "$api_status" 0
  check 1 'immutable API error' "$branch" "$immutable" 0 "$api_status"
  check 1 'both API errors' "$branch" "$immutable" "$api_status" "$api_status"
done
for argument in '' 'owner' 'owner/repo/extra' '../repo' 'owner/..' 'owner/.' '-owner/repo' 'owner/repo?x=1'; do
  : > "$GH_SETTINGS_CALL_LOG"
  actual=0
  bash "$tool" "$argument" > "$temporary/output" 2>&1 || actual=$?
  if [[ "$actual" -ne 2 || -s "$GH_SETTINGS_CALL_LOG" ]]; then
    echo 'Invalid repository argument was not refused before API access.' >&2
    exit 1
  fi
  checks=$((checks + 1))
done
actual=0
bash "$tool" kumwe/canonical-json extra > "$temporary/output" 2>&1 || actual=$?
if [[ "$actual" -ne 2 || -s "$GH_SETTINGS_CALL_LOG" ]]; then
  echo 'Extra argument was not refused before API access.' >&2
  exit 1
fi
checks=$((checks + 1))
echo "Release settings preflight passed: $checks isolated cases."
