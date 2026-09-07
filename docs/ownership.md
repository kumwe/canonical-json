# Canonical ownership decision

Examined App baseline: `960ce8ec00cf724a7cae03e5ba09c4852c9ab54e`.
This decision follows the repository brief's explicit generic-only semantic scope.

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
Neither package is a runtime dependency of this semantic extraction. No overlapping encoder is copied
from those libraries and no assumption of corpus equivalence permits replacing their consumers.

The concrete `1.0` counterexample distinguishes the generic profile from Runtime; `{}` versus `[]`
and out-of-safe-range integers distinguish Producer; any float distinguishes Definition and SDK.
Those distinctions are documented to prevent future name-based consolidation.

## FQCN and native ownership

`resources/ownership/v1.json` is the semantic-side joint ownership input:

- Composer owns exactly `Kumwe\CanonicalJson\Profile`, `FindingCode`, and `Limits`.
- Engine owns the future C++ algorithm and `kumwe_engine_v1_*` C ABI; it has no PHP FQCN.
- The extension alone may register concrete `Kumwe\Engine\*` classes, never these PHP enums/DTOs.
- Computation owns its semantic execution port and the later thin native adapter/provider.
- No canonical PHP executor interface is invented here to compete with Computation.

No native concrete signatures have been implemented or agreed in this Phase 1 package. The native
tasks must extend the collision-free ownership agreement with their exact concrete FQCN/method/ABI
inventory before coding those surfaces. The empty native-class list means this package supplies none;
it is not an attestation that a future extension exists. Its release must replay the same corpus digest.

The test-only frozen source is an oracle under `tests/Oracle/CanonicalJson.php`; its namespace is the
only semantic source change. It is absent from the consumer archive and is never runtime-selectable.
The fixture inventory records the original source checksum so adoption can detect App drift exactly.
