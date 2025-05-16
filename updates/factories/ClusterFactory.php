<?php

declare(strict_types=1);

namespace Initbiz\CumulusCore\Updates\Factories;

use Initbiz\CumulusCore\Models\Cluster;
use October\Rain\Database\Factories\Factory;

/**
 * ClusterFactory
 */
class ClusterFactory extends Factory
{
    protected $model = Cluster::class;

    /**
     * definition for the default state.
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->company();
        return [
            'name' => $name,
            'slug' => \Str::slug($name),
        ];
    }
}
