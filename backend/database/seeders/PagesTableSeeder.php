<?php

use Illuminate\Database\Seeder;
use App\Models\Page;

class PagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Page::create([
            'title' => 'Sample Page',
            'content' => '<p>This is a sample page.</p>',
            'slug' => 'sample-page'
        ]);
    }
}

