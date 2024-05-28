<?php namespace Initbiz\CumulusCore\Updates\Factories;

use Initbiz\CumulusCore\Classes\FeatureManager;
use Initbiz\CumulusCore\Models\Plan;
use October\Rain\Database\Factories\Factory;

/**
 * PlanFactory
 */
class PlanFactory extends Factory
{
    protected $model = Plan::class;

    /**
     * definition for the default state.
     * @return array<string, mixed>
     */
    public function definition()
    {
        $featureManager = FeatureManager::instance();
        $name = fake()->shuffleArray(['Free', 'Pro', 'Plus'])[0]; 
        $features = $featureManager->getFeaturesOptions();

        return [
            'name' => $name,
            'slug' => \Str::slug($name),
            'features' => fake()->randomElements(array_keys($features)),
        ];
    }
}
