<?php

declare(strict_types=1);

namespace Kumwe\CanonicalJson\Tests\Case;

use InvalidArgumentException;
use Kumwe\CanonicalJson\CanonicalEncoder;
use Kumwe\CanonicalJson\FindingCode;
use Kumwe\CanonicalJson\Limits;
use Kumwe\CanonicalJson\Tests\Oracle\Replay;
use Kumwe\CanonicalJson\Tests\TestCase;
use ReflectionClass;

final class CanonicalEncoderTest extends TestCase
{
    public function testPortPreservesGenericBytesAndDigestThroughIndependentCalls(): void
    {
        $encoder = $this->encoder(new Limits());
        $input = ['z' => 1.0, 'a' => ['é', '/']];
        $bytes = '{"a":["é","/"],"z":1.0}';
        $this->assertSame($bytes, $encoder->encode($input), 'Generic profile preserves floats and UTF-8');
        $this->assertSame(hash('sha256', $bytes), $encoder->digest($input), 'Digest commits the exact bytes');
        $this->assertSame(['z', 'a'], array_keys($input), 'No caller-visible map mutation');
        $this->assertSame('[2,1]', $encoder->encode([2, 1]), 'List order remains semantic');
        $this->assertSame($bytes, $encoder->encode($input), 'Shared sequential use does not retain prior data');
    }

    public function testBothOperationsRefuseTheSameBoundaryAndRecoverForTheNextCall(): void
    {
        $encoder = $this->encoder(new Limits(maxOutputBytes: 2));
        foreach ([$encoder->encode(...), $encoder->digest(...)] as $operation) {
            $error = $this->assertThrows(
                static fn (): string => $operation('x'),
                InvalidArgumentException::class,
                'Digest must enforce the same output budget as encoding',
            );
            $this->assertSame(FindingCode::OutputLimit->value, $error->getMessage(), 'Same ordered refusal');
            $this->assertSame('[]', $encoder->encode([]), 'Failure cannot poison the next operation');
        }
        $this->assertSame(hash('sha256', '[]'), $encoder->digest([]), 'Inclusive limit remains usable');
    }

    public function testContractExposesOnlyExplicitValueOperationsWithoutAnExecutor(): void
    {
        $type = new ReflectionClass(CanonicalEncoder::class);
        $this->assertTrue($type->isInterface(), 'Composer supplies a port, not an implementation');
        $this->assertSame([], $type->getProperties(), 'No global executor or operation state');
        $this->assertSame(['encode', 'digest'], array_map(
            static fn (\ReflectionMethod $method): string => $method->getName(),
            $type->getMethods(),
        ), 'Deliberately narrow public contract');
        foreach ($type->getMethods() as $method) {
            $this->assertFalse($method->isStatic(), 'Consumers explicitly receive their execution service');
            $this->assertSame('mixed', (string) $method->getParameters()[0]->getType(), 'Complete profile input');
            $this->assertSame('string', (string) $method->getReturnType(), 'Exact bytes or digest output');
        }
    }

    private function encoder(Limits $limits): CanonicalEncoder
    {
        return new Replay($limits);
    }
}
