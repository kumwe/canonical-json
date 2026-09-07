# Public API

All types belong to this package and require PHP 8.5. The package ships no provider or runtime executor.
Metadata values are immutable. The execution interface is implemented outside this package and injected
explicitly; its implementation owns operation-state isolation and enforces the profile's Limits.

## `Kumwe\CanonicalJson\CanonicalEncoder`

Interface with two non-static, required methods and no properties, constructor or default implementation.
`encode(mixed $value): string` returns complete canonical UTF-8 bytes for Profile::GenericV1.
`digest(mixed $value): string` returns exactly 64 lowercase hexadecimal SHA-256 characters over those bytes.
Both accept null, bool, int, finite float, valid UTF-8 string and bounded recursive arrays. They preserve
list order, byte-sort map keys and preserve zero fractions. They reject objects, resources, non-finite
floats, malformed UTF-8 and configured structural/input/output-limit violations with InvalidArgumentException.
Implementations preserve ordered FindingCode identity through their documented exception mapping.
Neither operation mutates input, invokes input callbacks, performs I/O or returns a partial result.
Digesting applies every encode bound, including output bytes. A refusal cannot poison the next operation.

Inject CanonicalEncoder into a consuming constructor or factory; do not select it through a static global,
service locator or extension-detection fallback. Consumers retain only captured values/digests when no
further encoding is required. Computation supplies the future native adapter after verified native releases.
During staged App adoption, an explicit host adapter delegates to the existing App executor until cutover.
No claim is made that the historical executor already enforces the new bounded profile: App compatibility
tests and the subsequent native provisioning/cutover gates must establish that separately.
The test-only Replay implementation exercises the contract and is excluded from distribution.
Definition, runtime, SDK and Studio profiles must not be replaced by this interface without corpus proof.

## `Kumwe\CanonicalJson\Profile`

String-backed enum identifying semantics. `GenericV1` has value `kumwe-canonical-json/generic-v1`.
Native and host contracts carry this identity with the corpus digest; it never selects an implementation.
Public PHP enum properties `$name` and `$value` are readonly strings.
`cases(): array` returns the one case in declaration order.
`from(string $value): self` returns the exact case or throws ValueError for an unknown identity.
`tryFrom(string $value): ?self` returns that case or null. Neither method trims or normalizes input.
Example: `Profile::tryFrom('generic-v1')` is null; the full versioned identity is required.

## `Kumwe\CanonicalJson\FindingCode`

String-backed enum. The complete case/value mapping is fixed in the semantic manifest:

| Case | Backed value |
|---|---|
| `UnsupportedType` | `canonical.unsupported-type` |
| `NonFiniteNumber` | `canonical.non-finite-number` |
| `InvalidUtf8` | `canonical.invalid-utf8` |
| `DepthLimit` | `canonical.depth-limit` |
| `NodeLimit` | `canonical.node-limit` |
| `OutputLimit` | `canonical.output-limit` |
| `InputLimit` | `canonical.input-limit` |

Public enum properties `$name` and `$value` are readonly strings.
`cases(): array` returns all cases in declaration order; declaration order is not failure priority.
`from(string $value): self` returns the exact case or throws ValueError.
`tryFrom(string $value): ?self` returns the exact case or null. No coercion or message matching is supported.
The structural/emission ordering in `docs/semantics.md` determines the first failure, independently
of enum order. Native transport uses these strings; exception translation belongs to its binding.

## `Kumwe\CanonicalJson\Limits`

Final readonly metadata class. `__construct(int $maxDepth = 64, int $maxNodes = 100000,
int $maxOutputBytes = 8388608, int $maxInputBytes = 16777216): void` validates four integer budgets.
Public readonly `$maxDepth` permits 0..64; `$maxNodes` permits 1..100000;
`$maxOutputBytes` permits 1..8388608; `$maxInputBytes` permits 1..16777216. Values are stored unchanged.
InvalidArgumentException is thrown for the first out-of-range
parameter, checked in that order. Native PHP type checks apply; callers should enable strict_types.
There is no nullable field, setter, serialization format or extension point.

These limits describe an operation; constructing a value does not inspect or encode a payload.
An executor must enforce the described counting rules. A root scalar is allowed at maxDepth zero;
any child value is refused. A root empty array also has depth zero and consumes one node.
Example: `new Limits(maxDepth: 0, maxNodes: 1, maxOutputBytes: 2)` permits an empty array's `[]` bytes.

## Manifest agreement

The standard public API manifest records source/reflection signatures and enum case names as constants.
`resources/semantics/v1.json` additionally records exact enum backed values, constructor defaults, profile
budgets, corpus identity and ownership. The corpus test verifies those values against source reflection.
No manifest claims a native class or executable canonical service is supplied by Composer.
