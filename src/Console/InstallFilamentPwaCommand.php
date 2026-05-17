<?php

declare(strict_types=1);

namespace Banulakwin\FilamentPwa\Console;

use Illuminate\Console\Command;

final class InstallFilamentPwaCommand extends Command
{
    protected $signature = 'filament-pwa:install {--force : Overwrite published files}';

    protected $description = 'Publish Filament PWA config, assets, views, and mobile bottom nav override';

    public function handle(): int
    {
        $force = (bool) $this->option('force');

        $this->call('vendor:publish', [
            '--tag' => 'filament-pwa-config',
            '--force' => $force,
        ]);
        $this->call('vendor:publish', [
            '--tag' => 'filament-pwa-assets',
            '--force' => true,
        ]);
        $this->call('vendor:publish', [
            '--tag' => 'filament-pwa-mobile-bottom-nav',
            '--force' => true,
        ]);

        $this->components->info('Published filament-pwa config, sw.js, webmanifest, and mobile-bottom-nav view.');
        $this->line('Configure filament-pwa.badge_count_provider and recipient_criteria in config/filament-pwa.php.');
        $this->line('Register FilamentPwaPlugin::make() in your panel provider.');

        return self::SUCCESS;
    }
}
