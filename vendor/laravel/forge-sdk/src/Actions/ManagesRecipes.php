<?php

namespace Laravel\Forge\Actions;

use Laravel\Forge\Resources\Recipe;

trait ManagesRecipes
{
    /**
     * Get the collection of recipes.
     *
     * @return \Laravel\Forge\Resources\Recipe[]
     */
    public function recipes()
    {
        return $this->transformCollection(
            $this->get('recipes')['recipes'], Recipe::class
        );
    }

    /**
     * Get a recipe instance.
     *
     * @param  string  $recipeId
     * @return \Laravel\Forge\Resources\Recipe
     */
    public function recipe($recipeId)
    {
        return new Recipe($this->get("recipes/$recipeId")['recipe']);
    }

    /**
     * Create a new recipe.
     *
     * @return \Laravel\Forge\Resources\Recipe
     */
    public function createRecipe(array $data)
    {
        return new Recipe($this->post('recipes', $data)['recipe']);
    }

    /**
     * Update the given recipe.
     *
     * @param  string  $recipeId
     * @return \Laravel\Forge\Resources\Recipe
     */
    public function updateRecipe($recipeId, array $data)
    {
        return new Recipe($this->put("recipes/$recipeId", $data)['recipe']);
    }

    /**
     * Delete the given recipe.
     *
     * @param  string  $recipeId
     * @return void
     */
    public function deleteRecipe($recipeId)
    {
        $this->delete("recipes/$recipeId");
    }

    /**
     * Run the given recipe.
     *
     * @param  string  $recipeId
     * @return void
     */
    public function runRecipe($recipeId, array $data)
    {
        $this->post("recipes/$recipeId/run", $data);
    }
}
