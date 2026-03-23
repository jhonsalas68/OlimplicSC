<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Filament\Facades\Filament;

class TestFilament extends Command
{
    protected $signature = 'test:filament';
    protected $description = 'Test Filament Resources';

    public function handle()
    {
        $panel = Filament::getCurrentOrDefaultPanel();
        $this->info("Panel ID: " . $panel->getId());
        $resources = $panel->getResources();
        $this->info("Resources: " . implode(', ', $resources));
    }
}
