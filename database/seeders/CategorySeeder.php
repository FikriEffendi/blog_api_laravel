<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $names = [
            'Teknologi',
            'Pemrograman',
            'Laravel',
            'PHP',
            'JavaScript',
            'Tutorial',
            'Berita',
            'Opini',
            'Review',
            'Tips',
        ];

        foreach ($names as $name) {
            $slug = Str::slug($name);

            $existing = Category::where('slug', $slug)->first();
            if (!$existing) {
                $category = new Category();
                $category->name = $name;
                $category->slug = $slug;
                $category->uuid = (string) Str::uuid();
                $category->save();
            }
        }
    }
}
