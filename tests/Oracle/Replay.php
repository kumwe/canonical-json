<?php

declare(strict_types=1);

namespace Kumwe\CanonicalJson\Tests\Oracle;

use InvalidArgumentException;
use JsonException;
use Kumwe\CanonicalJson\FindingCode;
use Kumwe\CanonicalJson\Limits;
use RuntimeException;
use stdClass;

/**
 * Test-only bounded replay oracle. Excluded from the runtime archive and all public manifests.
 *
 * @internal
 * @since 0.1.0
 */
final class Replay
{
    private int $nodes = 0;
    private string $output = '';
    private int $inputBytes = 0;

    public function __construct(private readonly Limits $limits)
    {
    }

    public function encode(mixed $value): string
    {
        $this->nodes = 0;
        $this->output = '';
        $this->inputBytes = 0;
        $normalized = $this->normalize($value, 0);
        $this->emit($normalized);

        return $this->output;
    }

    private function normalize(mixed $value, int $depth): mixed
    {
        if ($depth > $this->limits->maxDepth) {
            throw new InvalidArgumentException(FindingCode::DepthLimit->value);
        }
        if (++$this->nodes > $this->limits->maxNodes) {
            throw new InvalidArgumentException(FindingCode::NodeLimit->value);
        }
        if (is_array($value)) {
            if (count($value) > $this->limits->maxNodes - $this->nodes) {
                throw new InvalidArgumentException(FindingCode::NodeLimit->value);
            }
            foreach (array_keys($value) as $key) {
                $this->admit(is_int($key) ? 8 : strlen($key));
            }
            if (!array_is_list($value)) {
                ksort($value, SORT_STRING);
            }
            foreach ($value as $key => $child) {
                $value[$key] = $this->normalize($child, $depth + 1);
            }

            return $value;
        }
        $this->admit(match (true) {
            is_string($value) => strlen($value),
            is_int($value), is_float($value) => 8,
            is_bool($value) => 1,
            default => 0,
        });
        if (is_float($value) && !is_finite($value)) {
            throw new InvalidArgumentException(FindingCode::NonFiniteNumber->value);
        }
        if ($value === null || is_bool($value) || is_int($value) || is_float($value) || is_string($value)) {
            return $value;
        }

        throw new InvalidArgumentException(FindingCode::UnsupportedType->value);
    }

    private function admit(int $bytes): void
    {
        if ($bytes > $this->limits->maxInputBytes - $this->inputBytes) {
            throw new InvalidArgumentException(FindingCode::InputLimit->value);
        }
        $this->inputBytes += $bytes;
    }

    private function emit(mixed $value): void
    {
        if (!is_array($value)) {
            $this->append($this->scalar($value));

            return;
        }
        $list = array_is_list($value);
        $this->append($list ? '[' : '{');
        $first = true;
        foreach ($value as $key => $child) {
            if (!$first) {
                $this->append(',');
            }
            $first = false;
            if (!$list) {
                $this->append($this->scalar((string) $key));
                $this->append(':');
            }
            $this->emit($child);
        }
        $this->append($list ? ']' : '}');
    }

    private function scalar(mixed $value): string
    {
        try {
            return json_encode(
                $value,
                JSON_PRESERVE_ZERO_FRACTION | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR,
            );
        } catch (JsonException) {
            throw new InvalidArgumentException(FindingCode::InvalidUtf8->value);
        }
    }

    private function append(string $bytes): void
    {
        if (strlen($bytes) > $this->limits->maxOutputBytes - strlen($this->output)) {
            throw new InvalidArgumentException(FindingCode::OutputLimit->value);
        }
        $this->output .= $bytes;
    }

    /** @return array<string, mixed> */
    public static function object(mixed $value): array
    {
        if (!is_array($value) || array_is_list($value)) {
            throw new RuntimeException('Malformed corpus object.');
        }
        $result = [];
        foreach ($value as $key => $item) {
            if (!is_string($key)) {
                throw new RuntimeException('Corpus object keys must be strings.');
            }
            $result[$key] = $item;
        }

        return $result;
    }

    public static function text(mixed $value): string
    {
        if (!is_string($value)) {
            throw new RuntimeException('Corpus text is not a string.');
        }

        return $value;
    }

    public static function integer(mixed $value): int
    {
        if (!is_int($value)) {
            throw new RuntimeException('Corpus count is not an integer.');
        }

        return $value;
    }

    public static function decode(mixed $input): mixed
    {
        $node = self::object($input);
        $type = $node['type'] ?? null;
        return match ($type) {
            'null' => self::nullNode($node),
            'bool' => self::boolNode($node),
            'int' => self::intNode($node),
            'float' => self::floatNode($node),
            'string' => self::stringNode($node),
            'array' => self::arrayNode($node),
            'unsupported' => self::unsupportedNode($node),
            'nested-list' => self::nestedNode($node),
            'repeat-list' => self::repeatNode($node),
            'repeat-string' => self::repeatStringNode($node),
            default => throw new RuntimeException('Unknown corpus node type.'),
        };
    }

    /** @param array<string, mixed> $node */
    private static function nullNode(array $node): null
    {
        self::keys($node, ['type']);

        return null;
    }

    /** @param array<string, mixed> $node */
    private static function boolNode(array $node): bool
    {
        self::keys($node, ['type', 'value']);
        if (!is_bool($node['value'])) {
            throw new RuntimeException('Malformed boolean fixture.');
        }

        return $node['value'];
    }

    /** @param array<string, mixed> $node */
    private static function intNode(array $node): int
    {
        self::keys($node, ['type', 'decimal']);
        $decimal = self::text($node['decimal']);
        if (preg_match('/^(0|-?[1-9][0-9]*)$/D', $decimal) !== 1 || (string) (int) $decimal !== $decimal) {
            throw new RuntimeException('Malformed signed int64 fixture.');
        }

        return (int) $decimal;
    }

    /** @param array<string, mixed> $node */
    private static function floatNode(array $node): float
    {
        self::keys($node, ['type', 'hex']);
        $hex = self::text($node['hex']);
        if (preg_match('/^[0-9a-f]{16}$/D', $hex) !== 1) {
            throw new RuntimeException('Malformed binary64 fixture.');
        }
        $bytes = hex2bin($hex);
        $decoded = is_string($bytes) ? unpack('E', $bytes) : false;
        if (!is_array($decoded) || !is_float($decoded[1] ?? null)) {
            throw new RuntimeException('Cannot decode binary64 fixture.');
        }

        return $decoded[1];
    }

    /** @param array<string, mixed> $node */
    private static function stringNode(array $node): string
    {
        self::keys($node, ['type', 'base64']);
        $base64 = self::text($node['base64']);
        $bytes = base64_decode($base64, true);
        if ($bytes === false || base64_encode($bytes) !== $base64) {
            throw new RuntimeException('Malformed byte-string fixture.');
        }

        return $bytes;
    }

    /** @param array<string, mixed> $node
     * @return array<int|string, mixed>
     */
    private static function arrayNode(array $node): array
    {
        self::keys($node, ['type', 'entries']);
        $entries = $node['entries'];
        if (!is_array($entries) || !array_is_list($entries)) {
            throw new RuntimeException('Array fixture entries must be a list.');
        }
        $result = [];
        foreach ($entries as $input) {
            $entry = self::object($input);
            self::keys($entry, ['key', 'value']);
            $keyNode = self::object($entry['key']);
            if (!in_array($keyNode['type'] ?? null, ['string', 'int'], true)) {
                throw new RuntimeException('Array fixture key must be int or string.');
            }
            $key = self::decode($keyNode);
            if (!is_int($key) && !is_string($key)) {
                throw new RuntimeException('Array fixture key failed decoding.');
            }
            $probe = [$key => true];
            if (array_key_first($probe) !== $key || array_key_exists($key, $result)) {
                throw new RuntimeException('Fixture key is coerced or duplicated.');
            }
            $result[$key] = self::decode($entry['value']);
        }

        return $result;
    }

    /** @param array<string, mixed> $node */
    private static function unsupportedNode(array $node): stdClass
    {
        self::keys($node, ['type']);

        return new stdClass();
    }

    /** @param array<string, mixed> $node */
    private static function nestedNode(array $node): mixed
    {
        self::keys($node, ['type', 'depth', 'leaf']);
        $depth = self::integer($node['depth']);
        if ($depth < 0 || $depth > 65) {
            throw new RuntimeException('Nested fixture exceeds the controlled test recipe.');
        }
        $value = self::decode($node['leaf']);
        for ($index = 0; $index < $depth; $index++) {
            $value = [$value];
        }

        return $value;
    }

    /** @param array<string, mixed> $node
     * @return list<mixed>
     */
    private static function repeatNode(array $node): array
    {
        self::keys($node, ['type', 'count', 'value']);
        $count = self::integer($node['count']);
        if ($count < 0 || $count > 100000) {
            throw new RuntimeException('Repeated fixture exceeds the controlled test recipe.');
        }

        return array_fill(0, $count, self::decode($node['value']));
    }

    /** @param array<string, mixed> $node */
    private static function repeatStringNode(array $node): string
    {
        self::keys($node, ['type', 'count', 'base64']);
        $count = self::integer($node['count']);
        $byte = self::stringNode(['type' => 'string', 'base64' => $node['base64']]);
        if ($count < 0 || $count > 8388608 || strlen($byte) !== 1) {
            throw new RuntimeException('Repeated string exceeds the controlled test recipe.');
        }

        return str_repeat($byte, $count);
    }

    /** @param array<string, mixed> $node
     * @param list<string> $expected
     */
    public static function keys(array $node, array $expected): void
    {
        $actual = array_keys($node);
        sort($actual);
        sort($expected);
        if ($actual !== $expected) {
            throw new RuntimeException('Corpus object contains missing or unknown members.');
        }
    }
}
