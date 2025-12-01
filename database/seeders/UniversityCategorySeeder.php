<?php

namespace Database\Seeders;

use App\Models\Term;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class UniversityCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $universities = [
            'Debre Berhan University',
            'Wollo University',
            'Woldia University',
            'Wolkite University',
        ];

        $this->command->info('Checking for duplicate categories...');
        
        // First, find and report any duplicates
        foreach ($universities as $university) {
            $duplicates = Term::where('taxonomy', 'category')
                ->where(function($q) use ($university) {
                    $q->where('name', $university)
                      ->orWhere('name', 'like', $university)
                      ->orWhere('slug', Str::slug($university));
                })
                ->get();
            
            if ($duplicates->count() > 1) {
                $this->command->warn("Found {$duplicates->count()} duplicates for '{$university}':");
                foreach ($duplicates as $dup) {
                    $this->command->line("  - ID: {$dup->id}, Name: '{$dup->name}', Slug: '{$dup->slug}'");
                }
                
                // Keep the first one, delete the rest
                $keep = $duplicates->first();
                $toDelete = $duplicates->slice(1);
                foreach ($toDelete as $del) {
                    // Move any relationships to the kept term
                    DB::table('term_relationships')
                        ->where('term_id', $del->id)
                        ->update(['term_id' => $keep->id]);
                    $del->delete();
                    $this->command->info("  Deleted duplicate ID: {$del->id}");
                }
            } elseif ($duplicates->count() == 1) {
                $this->command->info("'{$university}' exists (ID: {$duplicates->first()->id})");
            }
        }

        $this->command->newLine();
        $this->command->info('Creating/updating university categories...');

        foreach ($universities as $university) {
            $term = Term::firstOrCreate(
                [
                    'name' => $university,
                    'taxonomy' => 'category',
                ],
                [
                    'slug' => Str::slug($university),
                    'description' => "News category for {$university}",
                ]
            );

            // Ensure post_types includes 'news'
            $currentPostTypes = $term->post_types ?? [];
            if (!in_array('news', $currentPostTypes)) {
                $term->post_types = array_merge($currentPostTypes, ['news']);
                $term->save();
            }

            $this->command->info("Category '{$university}' ready (ID: {$term->id})");
        }

        $this->command->newLine();
        $this->command->info('All university categories have been set up!');
        
        // Show final list
        $this->command->newLine();
        $this->command->info('Final category list:');
        $allCategories = Term::where('taxonomy', 'category')->get();
        foreach ($allCategories as $cat) {
            $postTypes = is_array($cat->post_types) ? implode(', ', $cat->post_types) : $cat->post_types;
            $this->command->line("  ID: {$cat->id} | Name: '{$cat->name}' | Slug: '{$cat->slug}' | Post Types: [{$postTypes}]");
        }
    }
}
