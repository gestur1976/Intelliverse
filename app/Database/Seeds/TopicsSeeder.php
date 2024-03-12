<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class TopicsSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'title' => 'Tech',
                'slug' => 'tech',
            ],
            [
                'title' => 'Science',
                'slug' =>'science',
            ],
            [
                'title' => 'Health',
                'slug' => 'health',
            ],
            [
                'title' => 'Business',
                'slug' => 'business',
            ],
            [
                'title' => 'Entertainment',
                'slug' => 'entertainment',
            ],
            [
                'title' => 'Sports',
                'slug' =>'sports',
            ],
            [
                'title' => 'Travel',
                'slug' => 'travel',
            ],
            [
                'title' => 'Lifestyle',
                'slug' => 'lifestyle',
            ],
            [
                'title' => 'Food',
                'slug' => 'food',
            ],
            [
                'title' => 'Music',
                'slug' =>'music',
            ],
            [
                'title' => 'Art',
                'slug' => 'art',
            ],
            [
                'title' => 'Books',
                'slug' => 'books',
            ],
            [
                'title' => 'Cars',
                'slug' => 'cars',
            ],
            [
                'title' => 'Gaming',
                'slug' => 'gaming',
            ],
            [
                'title' => 'Education',
                'slug' => 'education',
            ],
            [
                'title' => 'Politics',
                'slug' => 'politics',
            ],
            [
                'title' => 'Psychology',
                'slug' => 'psychology',
            ],
        ];

        $this->db->table('topics')->insertBatch($data);
    }
}
