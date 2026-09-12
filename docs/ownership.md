# Canonical ownership decision

Examined App baseline: `960ce8ec00cf724a7cae03e5ba09c4852c9ab54e`.
The baseline identifies semantic provenance; it does not describe the current Core tree.

| Source/profile | Owner and decision | Incompatibility requiring separate ownership |
|---|---|---|
| Shared/Domain/CanonicalJson | Generic semantics here; executor remains App | Full int64, finite floats, PHP arrays |
| BusinessDefinition/Domain/CanonicalDefinitionJson | Business Definition | Refuses all floats; depth 32, width 512 |
| Extension/Runtime/RuntimeCanonicalJson | App extension runtime | Allows objects; forces maps to objects; drops `.0` |
| OpenApi/Infrastructure/CanonicalOpenApiJson | App OpenAPI infrastructure | Pretty whitespace and a final newline |
| Producer/Canonical/CanonicalJson | Producer/Studio portability | UTF-16 ordering, safe integers, distinct objects |
| Extension SDK/Support/CanonicalJson | Extension SDK manifest support | Refuses floats and objects; depth/width rules |

Producer `0.2.0` also canonicalizes negative zero, refuses prototype-polluting member names and emits
SRI-style digests. Its installed source under `vendor/kumwe/producer/src/Canonical/CanonicalJson.php`
is authoritative for existing Studio consumers. SDK `0.2.4` owns its existing signing/manifest profile.
Neither package is a runtime dependency of this package. No overlapping encoder is copied
from those libraries and no assumption of corpus equivalence permits replacing their consumers.

The concrete `1.0` counterexample distinguishes the generic profile from Runtime; `{}` versus `[]`
and out-of-safe-range integers distinguish Producer; any float distinguishes Definition and SDK.
Those distinctions are documented to prevent future name-based consolidation.

## FQCN and native ownership

`resources/ownership/v1.json` is the semantic-side joint ownership input:

- Composer owns exactly `Kumwe\CanonicalJson\CanonicalEncoder`, `Profile`, `FindingCode`, and `Limits`.
- Engine owns the C++ algorithm and `kumwe_engine_v1_*` C ABI; it has no PHP FQCN.
- The extension alone may register concrete `Kumwe\Engine\*` classes, never these PHP enums/DTOs.
- This package owns the generic execution port; Computation owns its native adapter and readiness.

This package supplies no native concrete signature. Native implementations must maintain a
collision-free ownership agreement with their exact concrete FQCN/method/ABI inventory. The empty
native-class list describes this package's exports; it does not attest to another repository's
implementation status. A conforming native release must replay the same corpus digest.

The test-only frozen source is an oracle under `tests/Oracle/CanonicalJson.php`; its namespace is the
only semantic source change. It is absent from the consumer archive and is never runtime-selectable.
The fixture inventory records the original source checksum so adoption can detect App drift exactly.

## Explicit generic execution port

`Kumwe\CanonicalJson\CanonicalEncoder` declares `encode(mixed): string` and `digest(mixed): string`.
Portable packages receive this contract explicitly. No executor, container provider or runtime selector
is shipped here. The implementation must preserve GenericV1 bytes, ordered refusals and operation limits;
digest enforces the same limits as encoding. Computation owns the native adapter after verified native
releases. App may adapt its existing executor during the staged adoption, retaining its tests until the
Computation cutover. Distinct Definition, SDK, Runtime and Studio profiles keep their current owners.
