<?php

namespace Database\Seeders;

use App\Jobs\Tag\TagCreated;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Skillz\Nnpcreusable\Models\Tag;


class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tags')->truncate();

        // Seed new data
        $data = [
            [
                'name' => 'Create Daily Volume Customer',
                'tag_class' => 'App\Services\DailyVolumeService',
                'tag_class_method' => 'create',
            ],
            [
                'name' => 'Create Gas Cost',
                'tag_class' => 'App\Services\GasCostService',
                'tag_class_method' => 'create',
            ],
        ];
        foreach ($data as $key => $value) {
            $tags = Tag::create($value);
            TagCreated::dispatch($tags)->onQueue('formbuilder_queue');
        }
        $allTags = Tag::all();
    }
}
