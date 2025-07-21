<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RolePermission>
 */
class RolePermissionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'role_id' => '1',
            'module_id' => '1',
            'pview' => '1',
            'pcreate' => '1',
            'pedit' => '1',
            'pdelete' => '1',
        ];
    }
}
