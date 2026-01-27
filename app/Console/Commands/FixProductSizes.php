<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixProductSizes extends Command
{
    protected $signature = 'products:fix-sizes {--dry-run : Show changes without saving}';

    protected $description = 'Normalize existing produits.tailles values into valid JSON arrays (e.g., ["S","M"]) and remove invalid tokens.';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');

        $products = DB::table('produits')->select('id', 'name', 'tailles')->orderBy('id')->get();
        $updatedCount = 0;

        foreach ($products as $p) {
            $raw = $p->tailles;
            $parsed = $this->parseSizes($raw);
            $normalized = json_encode($parsed, JSON_UNESCAPED_UNICODE);

            $current = is_null($raw) ? 'null' : (string) $raw;
            $needsUpdate = $normalized !== $current;

            if ($needsUpdate) {
                $updatedCount++;
                $this->line("#{$p->id} {$p->name} -> " . $current . '  =>  ' . $normalized);
                if (!$dryRun) {
                    DB::table('produits')->where('id', $p->id)->update(['tailles' => $normalized]);
                }
            }
        }

        $this->info(($dryRun ? '[DRY-RUN] ' : '') . "Done. {$updatedCount} product(s) normalized.");
        return Command::SUCCESS;
    }

    private function parseSizes($raw): array
    {
        if (is_array($raw)) {
            $parsed = $raw;
        } elseif (is_string($raw)) {
            $tmp = json_decode($raw, true);
            if (is_array($tmp)) {
                $parsed = $tmp;
            } else {
                if (preg_match_all('/"([^\"]+)"/u', $raw, $m) && !empty($m[1])) {
                    $parsed = $m[1];
                } else {
                    $clean = trim($raw);
                    if (preg_match('/^\s*\[\s*,\s*,?\s*\]\s*$/u', $clean)) {
                        $parsed = [];
                    } else {
                        $clean = trim($clean, "[]\r\n\t \0\x0B");
                        $clean = str_replace(['"','“','”','‟','’','‘'], ' ', $clean);
                        $parts = preg_split('/\s*,\s*|\s*;\s*|\r?\n+/u', $clean);
                        $parsed = array_values(array_filter(array_map('trim', (array) $parts)));
                    }
                }
            }
        } else {
            $parsed = [];
        }

        // Filter invalid tokens
        $parsed = array_filter((array) $parsed, function ($v) {
            $v = trim((string) $v);
            if ($v === '' || $v === ',' || $v === '[' || $v === ']') {
                return false;
            }
            $lower = mb_strtolower($v);
            if (in_array($lower, ['null', 'undefined', 'n/a', 'na', 'none', 'vide'], true)) {
                return false;
            }
            return (bool) preg_match('/[\p{L}\p{N}]/u', $v);
        });

        return array_values(array_unique($parsed));
    }
}


