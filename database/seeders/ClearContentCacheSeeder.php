<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Services\Content\ContentService;
use Illuminate\Database\Seeder;

class ClearContentCacheSeeder extends Seeder
{
    public function run(): void
    {
        $contentService = app(ContentService::class);
        
        // Clear post types and taxonomies cache
        $contentService->clearPostTypesCache();
        $contentService->clearTaxonomiesCache();
        
        $this->command->info('Content cache cleared successfully.');
    }
}