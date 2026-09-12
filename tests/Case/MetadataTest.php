<?php

declare(strict_types=1);

namespace Kumwe\CanonicalJson\Tests\Case;

use InvalidArgumentException;
use Kumwe\CanonicalJson\CanonicalEncoder;
use Kumwe\CanonicalJson\FindingCode;
use Kumwe\CanonicalJson\Limits;
use Kumwe\CanonicalJson\Profile;
use Kumwe\CanonicalJson\Tests\Oracle\Replay;
use Kumwe\CanonicalJson\Tests\TestCase;
use ReflectionClass;
use ValueError;

final class MetadataTest extends TestCase
{
    public function testBudgetsPreserveInclusiveBoundariesAndRejectEveryInvalidParameter(): void
    {
        $minimum = new Limits(0, 1, 1, 1);
        $maximum = new Limits();
        $this->assertSame(
            [0, 1, 1, 1],
            array_values(get_object_vars($minimum)),
            'Minimum budgets are usable values'
        );
        $this->assertSame([64, 100000, 8388608, 16777216], array_values(get_object_vars($maximum)), 'Maximum defaults');
        foreach (
            [
            [-1, 1, 1], [65, 1, 1], [0, 0, 1], [0, 100001, 1],
            [0, 1, 0], [0, 1, 8388609], [0, 1, 1, 0], [0, 1, 1, 16777217],
            ] as $limits
        ) {
            $this->assertThrows(
                static fn (): Limits => new Limits(...$limits),
                InvalidArgumentException::class,
                'Out-of-profile budgets fail closed'
            );
        }
        $type = new ReflectionClass(Limits::class);
        $this->assertTrue($type->isFinal() && $type->isReadOnly(), 'Limits cannot acquire mutable shared state');
    }

    public function testSemanticManifestFreezesEnumValuesBudgetsAndCorpusIdentity(): void
    {
        $manifest = $this->json('resources/semantics/v1.json');
        Replay::keys($manifest, ['schema', 'profile', 'profile_case', 'limits', 'findings', 'corpus',
            'specification', 'ownership']);
        $this->assertSame('kumwe-canonical-json-semantics/v1', $manifest['schema'], 'Semantic schema');
        $this->assertSame(Profile::GenericV1->value, $manifest['profile'], 'Exact profile identity');
        $this->assertSame(Profile::GenericV1->name, $manifest['profile_case'], 'Exact profile case');
        $this->assertSame(get_object_vars(new Limits()), $manifest['limits'], 'Default budgets');
        $findings = [];
        foreach (FindingCode::cases() as $case) {
            $findings[$case->name] = $case->value;
            $this->assertSame($case, FindingCode::from($case->value), 'Exact code round-trip');
        }
        $this->assertSame($findings, $manifest['findings'], 'Every finding case and backed value is normative');
        $corpus = Replay::object($manifest['corpus']);
        $this->assertSame('resources/corpus/v1.json', $corpus['path'], 'Single corpus owner');
        $this->assertSame(79, $corpus['cases'], 'Complete reviewed corpus');
        $this->assertSame(hash('sha256', $this->read('resources/corpus/v1.json')), $corpus['sha256'], 'Corpus digest');
        $ownership = $this->json('resources/ownership/v1.json');
        $this->assertSame($corpus['sha256'], $ownership['corpus_sha256'], 'Native ownership uses the same corpus');
        $this->assertSame(
            [CanonicalEncoder::class, FindingCode::class, Limits::class, Profile::class],
            $ownership['composer_owned'],
            'Exactly four Composer owners, no native collision'
        );
        $this->assertSame([], $ownership['extension_owned_classes'], 'No extension class is implemented here');
        $this->assertSame(null, Profile::tryFrom('generic-v1'), 'Unqualified profile cannot select semantics');
        $this->assertSame(null, FindingCode::tryFrom('invalid-utf8'), 'Unknown unqualified code');
        $this->assertThrows(static fn (): Profile => Profile::from('unknown'), ValueError::class, 'Unknown profile');
        $this->assertThrows(static fn (): FindingCode => FindingCode::from('unknown'), ValueError::class, 'Bad code');
    }

    public function testFrozenOracleMatchesTheRecordedOriginalSourceWithNamespaceChangeOnly(): void
    {
        $inventory = $this->json('docs/consumer-inventory.json');
        $source = Replay::object($inventory['semantic_source']);
        $restored = str_replace(
            'namespace Kumwe\\CanonicalJson\\Tests\\Oracle;',
            'namespace Kumwe\\App\\Shared\\Domain;',
            $this->read('tests/Oracle/CanonicalJson.php')
        );
        $this->assertSame($source['sha256'], hash('sha256', $restored), 'Frozen source checksum');
        $this->assertSame([], $inventory['old_to_new_symbols'], 'No PHP executor is falsely claimed extracted');
        $this->assertSame([], $inventory['phase2_removals'], 'Semantic adoption retains the App executor');
    }

    public function testReleaseRecordPinsEveryPublishedManifestAndCorpus(): void
    {
        $record = $this->read('docs/release-record.md');
        $count = preg_match_all('/path: "([^"\n]+)"\n\s+sha256: "([0-9a-f]{64})"/', $record, $matches);
        $this->assertSame(6, $count, 'Every API/semantic/ownership/corpus input is pinned exactly once');
        $paths = $matches[1];
        $this->assertSame([
            'resources/public-api/v1.json', 'resources/capabilities/v1.json', 'resources/service-map/v1.json',
            'resources/semantics/v1.json', 'resources/ownership/v1.json', 'resources/corpus/v1.json',
        ], $paths, 'Release record pins the reviewed public artifact set');
        foreach ($paths as $index => $path) {
            $this->assertSame(hash('sha256', $this->read($path)), $matches[2][$index], 'Release record digest ' . $path);
        }
    }
}
