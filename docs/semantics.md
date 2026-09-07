# Generic canonical JSON profile v1

Identity: `kumwe-canonical-json/generic-v1`. This is a Kumwe profile, not RFC 8785 JCS.
The complete corpus and its SHA-256 are normative alongside this document. Any disagreement blocks release.

## Accepted values and PHP transport

Values are null, boolean, signed 64-bit integer, finite IEEE-754 binary64, valid UTF-8 string,
or an ordered PHP-style array of those values. Objects (including closures) and resources are refused without
invoking user code. Callable-name strings and callable-shaped arrays remain ordinary accepted data.
There is no callability inspection or autoload. JsonSerializable objects remain unsupported objects.
Strings remain strings; no decimal parsing, locale conversion or Unicode normalization is performed.
Repeated acyclic values are permitted. A cycle cannot be represented by the corpus tree and a PHP
bridge must bound traversal; reaching the depth limit refuses it without unbounded recursive work.

An array carries ordered integer/string keys. For a PHP bridge, PHP's key coercion has already happened;
the corpus represents the resulting keys, not duplicate keys overwritten during array construction.
Canonical signed decimal integer strings without a plus or leading zero, except `-0`, within int64
range are PHP integer keys. Corpus string keys of that form are invalid fixtures: encode them as integer
keys. The empty string, `00`, `01`, `+1`, `-0` and out-of-int64 decimal strings remain string keys.
No two entries may have the same coerced key. Input JSON text parsing is outside this profile.

## Ordering and collection shape

At entry, keys exactly `0,1,...,n-1` in that order are a list. Lists preserve order. Every other array
sorts keys using unsigned lexicographic comparison of their UTF-8 bytes; integer keys compare as their
signed decimal spelling. Members are recursively normalized in that order. The resulting array is
encoded as a JSON array if its ordered keys are now `0,1,...,n-1`, otherwise as a JSON object. This
intentional generic-source behavior means integer keys supplied as `1,0` become a list after sorting.
Runtime's force-object behavior is different and is not adopted. Empty PHP arrays are always `[]`.

## Scalars, escaping and numeric precision

Null and booleans emit `null`, `true`, `false`. Integers emit their shortest signed decimal form without
leading zero or plus; all signed 64-bit integers are preserved, including values outside binary64's
exact integer range. Binary64 values use shortest round-trip decimal conversion with round-to-nearest,
ties-to-even parsing; positive and negative zero remain `0.0` and `-0.0`. A finite float with an integer
significand preserves `.0`, including scientific notation. Exponents use lowercase `e`, no leading
zero, and explicit `+` for nonnegative exponents. Fixed notation is used when the scientific exponent
is from -4 through 16 inclusive; otherwise use scientific notation. Do not use locale or ambient
serialization precision. This matches the examined PHP encoder with `serialize_precision=-1`.
The corpus fixes normal/subnormal extrema, notation transitions, negative zero and int/float distinction.

Strings emit quotes. Escape quote and backslash as `\"` and `\\`; backspace/formfeed/newline/carriage
return/tab use `\b`, `\f`, `\n`, `\r`, `\t`. Other U+0000..U+001F code points use lowercase `\u00xx`.
Slash and valid Unicode remain literal UTF-8, except U+2028/U+2029 emit `\u2028`/`\u2029`.
No BOM, insignificant whitespace or trailing newline is emitted. No invalid UTF-8 replacement, byte
discard or surrogate acceptance is permitted. Key escaping is identical to value escaping.
Digest fixtures use lowercase hexadecimal SHA-256 over precisely the canonical output bytes.
This package describes digest bytes; it supplies no hashing API and owns no signing/key policy.

## Bounds and deterministic refusal order

`Limits` defaults are root-relative maximum depth 64, maximum 100000 value nodes, maximum 8388608
output bytes and maximum 16777216 admitted input bytes. The root has depth zero and counts as one node;
keys are not value nodes. Empty arrays count as one node. Consumers may lower but never raise ceilings.
Input bytes count raw string bytes, eight bytes for each integer/float, one byte per boolean, zero per
null/array/unsupported value, and raw bytes per string key or eight per integer key (including list keys).
Repeated values/keys count at each occurrence. Output includes all punctuation and escaping.
Exactly the boundary is accepted. Input admission is independent of UTF-8 validity and output length.

Refusals contain exactly one stable code. At every node check depth, then increment/check node count.
For an array, check its immediate child count against the remaining node budget **before sorting or
copying keys**. Then admit the byte widths of all its keys before sorting. Next sort non-list keys and
visit children depth-first, preserving list order. For a scalar, admit its byte width before checking
unsupported type and non-finite float. An exhausted admission budget fails immediately; it precedes
later type/float/UTF-8 errors. This bounds map sorting and hostile key/string inspection before UTF-8
validation or scalar serialization. Array key order cannot change the refusal code of its key admission.

That bounded structural pass completes before UTF-8/output emission. During emission, validate each
already-admitted string/key as UTF-8 before accounting its escaped byte length; check output budget
before appending each token. Emit the first refusal in this order. A native streaming implementation
must check budgets before allocation and retain no partial output/digest. The PHP test-only oracle
is semantic evidence, not allocation proof. Diagnostics include no payload values, keys or secrets.
Code identity, not exception message text, is normative.

| Code | Condition |
|---|---|
| `canonical.depth-limit` | A node is deeper than maxDepth |
| `canonical.node-limit` | The next node would exceed maxNodes |
| `canonical.unsupported-type` | A value is neither an accepted scalar nor an array |
| `canonical.non-finite-number` | NaN or either infinity |
| `canonical.invalid-utf8` | Ill-formed UTF-8 in a key or string |
| `canonical.output-limit` | Appending a canonical token would exceed maxOutputBytes |
| `canonical.input-limit` | Admitting raw scalar/key bytes would exceed maxInputBytes |

These bounds and codes are an explicit safety refinement, not a claim about existing App refusals.
The historical generic source has no explicit input/node/output limit and recursively normalizes before
PHP's JSON depth refusal. Its floating representation depends on ambient serialize_precision.
Phase 1 does not change App behavior. Future Computation cutover must measure real consumer envelopes,
audit precision settings and persisted digest compatibility, replay the corpus and retain refusal proof.
