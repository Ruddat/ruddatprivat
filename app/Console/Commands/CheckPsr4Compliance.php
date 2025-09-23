<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Finder\Finder;

class CheckPsr4Compliance extends Command
{
    protected $signature = 'check:psr4';
    protected $description = 'Prüft, ob alle Klassen/Namespaces mit PSR-4 Autoloading übereinstimmen';

    public function handle()
    {
        $errors = [];
        $baseNamespace = 'App\\';
        $baseDir = app_path();

        $finder = new Finder();
        $finder->files()->in($baseDir)->name('*.php');

        foreach ($finder as $file) {
            $relativePath = str_replace($baseDir . DIRECTORY_SEPARATOR, '', $file->getRealPath());
            $expectedNamespace = $baseNamespace . str_replace(['/', '.php'], ['\\', ''], $relativePath);

            // Datei einlesen und Klassendefinition finden
            $content = file_get_contents($file->getRealPath());
            if (preg_match('/^namespace\s+([^;]+);/m', $content, $matches)) {
                $namespace = trim($matches[1]);
                if (!str_starts_with($expectedNamespace, $namespace)) {
                    $errors[] = [
                        'file'      => $relativePath,
                        'namespace' => $namespace,
                        'expected'  => $expectedNamespace,
                    ];
                }
            }
        }

        if (empty($errors)) {
            $this->info('✅ Alle Klassen entsprechen PSR-4.');
        } else {
            $this->error("⚠️ Es wurden " . count($errors) . " Verstöße gefunden:");
            foreach ($errors as $error) {
                $this->line("- Datei: {$error['file']}");
                $this->line("  Namespace: {$error['namespace']}");
                $this->line("  Erwartet:  {$error['expected']}");
            }
        }

        return 0;
    }
}