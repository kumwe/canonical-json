# Architecture

`src/` contains exactly Profile, FindingCode and Limits. These are semantic identity, stable refusal
vocabulary and operation metadata. Production code has no encoding/hash algorithm or collaborators.
The architecture gate fixes this allow-list, one PSR-4 owner, no App import, runtime selection,
native class, provider, alias, environment I/O or runtime dependency besides PHP.

`resources/corpus/` is language-neutral release data. Its test-only replay adapter and a frozen oracle
live under tests/Oracle and never ship. The native Engine must replay the same corpus through its C ABI,
then the extension through PHPT. Package replay is semantic evidence, not native execution evidence.
No test depends on the original uploaded ZIP or another repository being present.

Tests inspect behavior and failures, not only source strings. Structural gates supplement that proof
by blocking an accidental executor or development fixture from a published archive.
The clean-consumer gate installs the exact built ZIP as a dependency with no dev dependencies,
plugins, scripts, external registry or path repository, then loads every documented type and example.
