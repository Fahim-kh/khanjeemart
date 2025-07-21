<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ModuleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->word,
            'route' => '#',
            'icon' => 'side-menu__icon si si-' . $this->faker->word,
            'parent_id' => null,
            'sorting' => $this->faker->numberBetween(1, 100),
            'is_group_title' => false,
            'color' => 'primary-600',
            'icon_type' => 'class',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Dashboard module state
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function dashboard()
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => 'Dashboard',
                'route' => '#',
                'icon' => 'side-menu__icon si si-screen-desktop',
                'parent_id' => null,
                'sorting' => 1,
                'is_group_title' => false,
            ];
        });
    }

    /**
     * Group title state
     *
     * @param string $name
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function groupTitle($name = 'Application')
    {
        return $this->state(function (array $attributes) use ($name) {
            return [
                'name' => $name,
                'route' => '#',
                'icon' => '',
                'is_group_title' => true,
                'sorting' => $this->faker->numberBetween(1, 100),
            ];
        });
    }
}



//How to Use This Factory:
// 1- Create a Dashboard module:
// Module::factory()->dashboard()->create();
// 2- Create a group title:
// Module::factory()->groupTitle('Application')->create();
// 3- Create with specific parent:
//     Module::factory()->dashboard()
//     ->has(Module::factory()->count(3), 'childs')
//     ->create();
