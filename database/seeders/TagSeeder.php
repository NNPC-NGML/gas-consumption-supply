<?php

namespace Database\Seeders;

use App\Jobs\User\TagCreated;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Skillz\Nnpcreusable\Models\Tag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

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
                'name' => 'create EOI',
                'tag_class' => 'App\controllers\EOIController',
                'tag_class_method' => 'store',
            ],
        ];
        foreach ($data as $key => $value) {
            $tags = Tag::create($value);
            TagCreated::dispatch($tags)->onQueue('formbuilder_queue');
        }
        $allTags = Tag::all();
    }
}
