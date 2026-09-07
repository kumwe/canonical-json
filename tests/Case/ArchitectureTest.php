<?php

declare(strict_types=1);

namespace Kumwe\CanonicalJson\Tests\Case;

use Kumwe\CanonicalJson\Tests\TestCase;
use RuntimeException;

final class ArchitectureTest extends TestCase
{
    public function testBoundaryGateRefusesAddedDeletedOrExecutableSource(): void
    {
        $workspace = sys_get_temp_dir() . '/canonical-boundary-' . bin2hex(random_bytes(8));
        if (!mkdir($workspace) || !mkdir($workspace . '/src') || !mkdir($workspace . '/tools')) {
            throw new RuntimeException('Cannot prepare isolated architecture fixtures.');
        }
        $paths = ['composer.json', 'tools/verify-architecture.php', 'src/Profile.php',
            'src/FindingCode.php', 'src/Limits.php'];
        try {
            foreach ($paths as $path) {
                file_put_contents($workspace . '/' . $path, $this->read($path));
            }
            $this->assertSame(0, $this->status($workspace), 'Unchanged semantic boundary passes');
            file_put_contents(
                $workspace . '/src/UnexpectedExecutor.php',
                "<?php\ndeclare(strict_types=1);\nnamespace Kumwe\\CanonicalJson;\nfinal class UnexpectedExecutor {}\n"
            );
            $this->assertSame(1, $this->status($workspace), 'New source type cannot bypass fixed semantic ownership');
            unlink($workspace . '/src/UnexpectedExecutor.php');
            unlink($workspace . '/src/Limits.php');
            $this->assertSame(1, $this->status($workspace), 'Deleting a declared source owner fails closed');
            file_put_contents($workspace . '/src/Limits.php', $this->read('src/Limits.php'));
            file_put_contents(
                $workspace . '/src/Profile.php',
                $this->read('src/Profile.php') . "\n\\json_encode(null);\n",
            );
            $this->assertSame(1, $this->status($workspace), 'Qualified calls cannot bypass executor prohibition');
        } finally {
            foreach ([...$paths, 'src/UnexpectedExecutor.php'] as $path) {
                if (is_file($workspace . '/' . $path)) {
                    unlink($workspace . '/' . $path);
                }
            }
            rmdir($workspace . '/tools');
            rmdir($workspace . '/src');
            rmdir($workspace);
        }
    }

    private function status(string $workspace): int
    {
        $output = [];
        $status = 1;
        exec(escapeshellarg(PHP_BINARY) . ' ' . escapeshellarg($workspace . '/tools/verify-architecture.php')
            . ' 2>&1', $output, $status);

        return $status;
    }
}
