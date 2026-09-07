<?php

declare(strict_types=1);

namespace Kumwe\CanonicalJson\Tests\Case;

use InvalidArgumentException;
use Kumwe\CanonicalJson\FindingCode;
use Kumwe\CanonicalJson\Limits;
use Kumwe\CanonicalJson\Profile;
use Kumwe\CanonicalJson\Tests\Oracle\CanonicalJson;
use Kumwe\CanonicalJson\Tests\Oracle\Replay;
use Kumwe\CanonicalJson\Tests\TestCase;
use RuntimeException;

final class CorpusTest extends TestCase
{
    public function testAllVectorsAndDigestsReplayWithFrozenSourceParity(): void
    {
        $corpus = $this->json('resources/corpus/v1.json');
        Replay::keys($corpus, ['schema', 'profile', 'cases']);
        $this->assertSame('kumwe-canonical-json-corpus/v1', $corpus['schema'], 'Corpus schema');
        $this->assertSame(Profile::GenericV1->value, $corpus['profile'], 'Profile identity');
        $this->assertSame(
            trim($this->read('resources/corpus/v1.sha256')),
            hash('sha256', $this->read('resources/corpus/v1.json')),
            'Normative corpus digest'
        );
        $cases = $corpus['cases'];
        if (!is_array($cases) || !array_is_list($cases)) {
            throw new RuntimeException('Corpus cases must be a nonempty list.');
        }
        $this->assertSame(79, count($cases), 'Reviewed vector count must not silently shrink');
        $precision = ini_set('serialize_precision', '-1');
        $seen = [];
        try {
            foreach ($cases as $input) {
                $case = Replay::object($input);
                Replay::keys($case, isset($case['limits'])
                    ? ['id', 'input', 'limits', 'expected'] : ['id', 'input', 'expected']);
                $id = Replay::text($case['id']);
                $this->assertTrue(preg_match('/^[a-z0-9-]+$/D', $id) === 1, 'Portable case identity');
                $this->assertFalse(isset($seen[$id]), 'Unique case identity ' . $id);
                $seen[$id] = true;
                $limits = $this->limits($case['limits'] ?? null);
                $value = Replay::decode($case['input']);
                $expected = Replay::object($case['expected']);
                $oracle = new Replay($limits);
                if (isset($expected['finding'])) {
                    Replay::keys($expected, ['finding']);
                    $code = Replay::text($expected['finding']);
                    $this->assertTrue(FindingCode::tryFrom($code) !== null, 'Known finding ' . $id);
                    $failure = $this->assertThrows(
                        static fn (): string => $oracle->encode($value),
                        InvalidArgumentException::class,
                        $id,
                    );
                    $this->assertSame($code, $failure->getMessage(), 'Ordered refusal ' . $id);
                    continue;
                }
                Replay::keys($expected, ['output', 'sha256']);
                $output = Replay::text($expected['output']);
                $this->assertSame($output, $oracle->encode($value), 'Exact bytes ' . $id);
                $this->assertSame($output, CanonicalJson::encode($value), 'Frozen App source parity ' . $id);
                $this->assertSame($expected['sha256'], hash('sha256', $output), 'Digest ' . $id);
                $this->assertSame($expected['sha256'], $oracle->digest($value), 'Execution port digest ' . $id);
                $this->assertSame($expected['sha256'], CanonicalJson::digest($value), 'Source digest ' . $id);
            }
        } finally {
            if (is_string($precision)) {
                ini_set('serialize_precision', $precision);
            }
        }
    }

    public function testMalformedCorpusTransportIsRefusedBeforeReplay(): void
    {
        $bad = [
            ['type' => 'int', 'decimal' => '01'],
            ['type' => 'int', 'decimal' => '9223372036854775808'],
            ['type' => 'float', 'hex' => '1'],
            ['type' => 'string', 'base64' => 'YQ'],
            ['type' => 'null', 'extra' => null],
            ['type' => 'bool', 'value' => 1],
            ['type' => 'unknown'],
            ['type' => 'nested-list', 'depth' => 66, 'leaf' => ['type' => 'null']],
            ['type' => 'repeat-list', 'count' => 100001, 'value' => ['type' => 'null']],
            ['type' => 'repeat-string', 'count' => 8388609, 'base64' => 'YQ=='],
        ];
        foreach ($bad as $node) {
            $this->assertThrows(
                static fn (): mixed => Replay::decode($node),
                RuntimeException::class,
                'Malformed fixture cannot masquerade as conformance evidence'
            );
        }
        $entry = ['key' => ['type' => 'int', 'decimal' => '0'], 'value' => ['type' => 'null']];
        $this->assertThrows(
            static fn (): mixed => Replay::decode(['type' => 'array', 'entries' => [$entry, $entry]]),
            RuntimeException::class,
            'Duplicate keys cannot silently overwrite corpus input'
        );
        $coerced = ['key' => ['type' => 'string', 'base64' => 'MA=='], 'value' => ['type' => 'null']];
        $this->assertThrows(
            static fn (): mixed => Replay::decode(['type' => 'array', 'entries' => [$coerced]]),
            RuntimeException::class,
            'Corpus keys describe post-coercion PHP keys'
        );
    }

    public function testReferencesResourcesAndCallbacksCannotEscapeBoundsOrInvokeCode(): void
    {
        $oracle = new Replay(new Limits());
        $cycle = [];
        $cycle['self'] = &$cycle;
        $failure = $this->assertThrows(
            static fn (): string => $oracle->encode($cycle),
            InvalidArgumentException::class,
            'Cycle must stop at a finite depth'
        );
        $this->assertSame(FindingCode::DepthLimit->value, $failure->getMessage(), 'Cycle uses depth refusal');
        unset($cycle);
        $invoked = false;
        $callback = static function () use (&$invoked): void {
            $invoked = true;
        };
        $resource = fopen('php://memory', 'r+');
        if ($resource === false) {
            throw new RuntimeException('Cannot allocate the resource rejection fixture.');
        }
        try {
            foreach ([$callback, $resource] as $value) {
                $failure = $this->assertThrows(
                    static fn (): string => $oracle->encode($value),
                    InvalidArgumentException::class,
                    'Unsupported transport value'
                );
                $this->assertSame(FindingCode::UnsupportedType->value, $failure->getMessage(), 'Stable refusal');
            }
        } finally {
            fclose($resource);
        }
        $this->assertFalse($invoked, 'Encoding must not execute an input callback');
        $shared = ['x' => 1];
        $repeated = [&$shared, &$shared];
        $this->assertSame('[{"x":1},{"x":1}]', $oracle->encode($repeated), 'Acyclic repeated values are allowed');
    }

    private function limits(mixed $input): Limits
    {
        if ($input === null) {
            return new Limits();
        }
        $values = Replay::object($input);
        foreach (array_keys($values) as $key) {
            $this->assertTrue(
                in_array($key, ['maxDepth', 'maxNodes', 'maxOutputBytes', 'maxInputBytes'], true),
                'Known budget'
            );
        }

        return new Limits(
            Replay::integer(array_key_exists('maxDepth', $values) ? $values['maxDepth'] : 64),
            Replay::integer(array_key_exists('maxNodes', $values) ? $values['maxNodes'] : 100000),
            Replay::integer(array_key_exists('maxOutputBytes', $values) ? $values['maxOutputBytes'] : 8388608),
            Replay::integer(array_key_exists('maxInputBytes', $values) ? $values['maxInputBytes'] : 16777216),
        );
    }

    public function testMalformedBudgetMetadataCannotSilentlyUseDefaults(): void
    {
        foreach (['maxDepth', 'maxNodes', 'maxOutputBytes', 'maxInputBytes'] as $name) {
            foreach ([null, '1', 1.0, true] as $value) {
                $this->assertThrows(
                    fn (): Limits => $this->limits([$name => $value]),
                    RuntimeException::class,
                    'Explicit malformed budgets are not omitted defaults'
                );
            }
        }
    }
}
