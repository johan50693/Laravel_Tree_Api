<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $root1 = \App\Models\Node::create(['parent_id' => null]);
        $root2 = \App\Models\Node::create(['parent_id' => null]);

        $child1 = \App\Models\Node::create(['parent_id' => $root1->id]);
        $child2 = \App\Models\Node::create(['parent_id' => $root1->id]);

        \App\Models\Node::create(['parent_id' => $root2->id]);
        \App\Models\Node::create(['parent_id' => $child1->id]);
    }
}
