<?php

namespace Database\Seeders;

use App\Models\WorkspaceType;
use Illuminate\Database\Seeder;

class WorkSpaceTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $types = ['Education', 'Marketing', 'Operation', 'Engineering', 'Others'];
        foreach ($types as $type)
        {
            WorkspaceType::create(['name' => $type]);
        }
    }
}
