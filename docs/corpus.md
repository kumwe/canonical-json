# Language-neutral corpus

`resources/corpus/v1.json` uses schema `kumwe-canonical-json-corpus/v1`, one profile identity and 79
named cases. `v1.sha256` hashes the complete UTF-8 file bytes including the final newline. The semantics
and ownership manifests repeat that digest. Consumers record the released package version and digest.
Do not regenerate expectations from an implementation under test or silently accept a changed digest.

Each case has `id`, `input`, optional `limits`, and `expected`. An accepted result contains exact UTF-8
`output` and lowercase hexadecimal `sha256`. A refused result contains only `finding`, a stable code.
Expected output is one JSON value's exact bytes, not a decoded object comparison. Limits override only
the named generic-v1 budgets. Unknown fields, duplicate IDs, malformed fixtures and an empty corpus fail.

## Tagged value representation

The fixture wrapper is JSON; payload data uses these tagged nodes so a non-PHP implementation can
distinguish integers/floats, binary64 negative zero, bad UTF-8 and ordered typed array keys exactly.

| Node type | Additional fields | Meaning |
|---|---|---|
| `null` | none | Null value |
| `bool` | `value` boolean | Boolean value |
| `int` | `decimal` string | Shortest signed int64 spelling |
| `float` | `hex` string | Exactly 16 lowercase hex digits, IEEE-754 binary64 network byte order |
| `string` | `base64` string | Strict padded RFC 4648 bytes, valid or invalid UTF-8 |
| `array` | `entries` list | Ordered `{key: node, value: node}` entries; keys are int/string only |
| `unsupported` | none | Host object/resource/callable refusal category; never execute it |
| `nested-list` | `depth`, `leaf` | Wrap leaf in that many one-element lists, 0..65 |
| `repeat-list` | `count`, `value` | Repeat a value into a list, 0..100000 elements |
| `repeat-string` | `count`, `base64` | Repeat exactly one decoded byte, 0..8388608 times |

The compact expansion recipes specify large boundary values without storing megabytes of padding.
They are corpus transport, not production APIs. Corpus keys must describe post-coercion PHP array keys;
duplicate or coercing keys are malformed fixtures, not a valid input silently overwritten by the loader.
Scalar IEEE encodings include NaN/infinities solely for refusal proof.

## Replay responsibilities

The package suite validates every fixture, exact bytes/digest and ordered code with a test-only oracle.
Every accepted vector additionally replays the frozen generic App source with serialize_precision=-1.
No test reads another repository, attachment ZIP, network endpoint or mutable external fixture.

Engine must replay every case through its C ABI with the same budgets and ordering. Extension PHPT
must replay every case through its PHP API and separately test marshalling, handles, cycles, callbacks,
exceptions and worker reuse. Binding-specific invalid inputs are separate from corpus syntax errors.
Native allocations must be bounded before allocation; the PHP test oracle is not a performance model.

App keeps its current executor unit/parity tests during semantic adoption. After the later verified
Computation cutover, App retains corpus identity/readiness, composed digest outcomes and responsibility
tests. It must not rerun the package's entire unit/corpus suite as vendor implementation tests.
