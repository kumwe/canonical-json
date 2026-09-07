<?php

declare(strict_types=1);

use Kumwe\CanonicalJson\FindingCode;
use Kumwe\CanonicalJson\Limits;
use Kumwe\CanonicalJson\Profile;

/** @var list<string> $arguments */
$arguments = $_SERVER['argv'] ?? [];
require $arguments[1] ?? dirname(__DIR__) . '/vendor/autoload.php';

$limits = new Limits(maxDepth: 32, maxNodes: 10000, maxOutputBytes: 1048576);
if ($limits->maxDepth !== 32 || Profile::GenericV1->value !== 'kumwe-canonical-json/generic-v1') {
    throw new RuntimeException('Semantic metadata did not preserve its public contract.');
}
if (FindingCode::tryFrom('canonical.invalid-utf8') !== FindingCode::InvalidUtf8) {
    throw new RuntimeException('The finding vocabulary differs from the agreed native contract.');
}
$corpus = dirname(__DIR__) . '/resources/corpus/v1.json';
$digest = file_get_contents(dirname(__DIR__) . '/resources/corpus/v1.sha256');
if (!is_string($digest) || hash_file('sha256', $corpus) !== trim($digest)) {
    throw new RuntimeException('The shipped corpus does not match its digest.');
}
echo "Canonical semantic metadata and corpus identity are ready; no executor was loaded.\n";
