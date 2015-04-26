<?php

/**
 * @name IngredientTest class
 * @author Alex Agafonov
 * @desc Unit testing for Ingredient class 
 */
class IngredientTest {

	/**
     * Test - item name cannot be empty.
     *
     * @return bool
     */
	public function testItemNameCannotBeEmpty() {
		
		$errorMessage = '';
		try {
			$ingredient = new Ingredient('', 10, 'grams');
		} catch (Exception $e) {
			$errorMessage = $e->getMessage();
		}
		return assert('$errorMessage == \'Item name cannot be empty.\'');
	}
	
	/**
     * Test - amount must be an integer value.
     *
     * @return bool
     */
	public function testAmountMustBeInteger() {
		
		$errorMessage = '';
		try {
			$ingredient = new Ingredient('test', 'a', 'grams');
		} catch (Exception $e) {
			$errorMessage = $e->getMessage();
		}
		return assert('$errorMessage == \'Item amount must be an integer value.\'');
	}
	
	/**
     * Test - units of measure must be a valid index in enum.
     *
     * @return bool
     */
	public function testUnitsOfMeasureMustBeValidEnumValue() {
		
		$errorMessage = '';
		try {
			$ingredient = new Ingredient('test', 10, 'jiffy');
		} catch (Exception $e) {
			$errorMessage = $e->getMessage();
		}
		return assert('$errorMessage == \'Units of measure jiffy is not supported.\'');
	}
}

/**
 * @name FridgeIngredientTest class
 * @author Alex Agafonov
 * @desc Unit testing for FridgeIngredient class 
 */
class FridgeIngredientTest {

	/**
     * Test - use by date must be in dd/mm/yyyy format.
     * This scenario checks what happens when invalid date format is passed.
     *
     * @return bool
     */
	public function testUseByDateCannotAcceptInvalidDateFormat() {
		
		$errorMessage = '';
		try {
			$ingredient = new FridgeIngredient('test', 10, 'grams', '2012-10-21');
		} catch (Exception $e) {
			$errorMessage = $e->getMessage();
		}
		return assert('$errorMessage == \'Use by date format is not supported.\'');
	}
	
	/**
     * Test - use by date must be in dd/mm/yyyy format.
     * This scenario checks what happens when valid date format is passed.
     *
     * @return bool
     */
	public function testUseByDateMustHaveValidDateFormat() {
		
		$ingredient = new FridgeIngredient('test', 10, 'grams', '21/10/2014');
		$useByDate = $ingredient->getUseByDate();
		return assert('$useByDate->format(\'Y-m-d\') == \'2014-10-21\'');
	}
	
	/**
     * Test - check if use by date is expired. Ingredient is expired in this scenario.
     *
     * @return bool
     */
	public function testUseByDateExpired1() {
		
		$ingredient = new FridgeIngredient('test', 10, 'grams', '21/10/2014');
		return assert('$ingredient->hasExpired() == true');
	}
	
	/**
     * Test - check if use by date is expired. Ingredient is NOT expired in this scenario.
     *
     * @return bool
     */
	public function testUseByDateExpired2() {
		
		$ingredient = new FridgeIngredient('test', 10, 'grams', '21/10/2016');
		return assert('$ingredient->hasExpired() == false');
	}
}

/**
 * @name FridgeTest class
 * @author Alex Agafonov
 * @desc Unit testing for Fridge class 
 */
class FridgeTest {
	
	/**
     * Test - checks if fridge has unexpired item.
     * In this scenario ingredient in the fridge is expired but quantities match.
     *
     * @return bool
     */
	public function testHasUnexpiredItem1() {
		
		$fridge = new Fridge();
		$ingredient = new FridgeIngredient('test', 10, 'grams', '21/10/2012');
		$fridge->addItem($ingredient);
		$ingredient = new Ingredient('test', 10, 'grams');
		return assert('$fridge->hasUnexpiredItem($ingredient) == false');
	}
	
	/**
     * Test - checks if fridge has unexpired item.
     * In this scenario ingredient in the fridge is NOT expired and quantities match.
     *
     * @return bool
     */
	public function testHasUnexpiredItem2() {
		
		$fridge = new Fridge();
		$ingredient = new FridgeIngredient('test', 10, 'grams', '21/10/2016');
		$fridge->addItem($ingredient);
		$ingredient = new Ingredient('test', 10, 'grams');
		return assert('$fridge->hasUnexpiredItem($ingredient) == true');
	}
	
	/**
     * Test - checks if fridge has unexpired item.
     * In this scenario ingredient in the fridge is NOT expired but quantity in the fridge is not sufficient.
     *
     * @return bool
     */
	public function testHasUnexpiredItem3() {
		
		$fridge = new Fridge();
		$ingredient = new FridgeIngredient('test', 10, 'grams', '21/10/2016');
		$fridge->addItem($ingredient);
		$ingredient = new Ingredient('test', 20, 'grams');
		return assert('$fridge->hasUnexpiredItem($ingredient) == false');
	}
	
	/**
     * Test - find recipe from the collection of recipes that match ingredient condition.
     * In this scenario recipe matches all the available ingredients.
     *
     * @return bool
     */
	public function testFindValidRecipe1() {
		
		// Create and fille the fridge.
		$fridge = new Fridge();
		$recipeCollection = new RecipeCollection();
		$fridge->addItem(new FridgeIngredient('test1', 10, 'grams', '21/10/2016'));
		$fridge->addItem(new FridgeIngredient('test2', 2, 'slices', '21/11/2016'));
		$fridge->addItem(new FridgeIngredient('test3', 100, 'ml', '01/01/2016'));
		
		// Create recipe.
		$recipe = new Recipe('Test recipe');
		$recipe->addIngredient(new Ingredient('test1', 5, 'grams'));
		$recipe->addIngredient(new Ingredient('test2', 1, 'slices'));
		$recipe->addIngredient(new Ingredient('test3', 50, 'ml'));
		
		// Add to recipe collection.
		$recipeCollection->addRecipe($recipe);
		$foundRecipe = $fridge->findRecipe($recipeCollection);
		
		return assert('$foundRecipe->getName() == \'Test recipe\'');
	}
	
	/**
     * Test - find recipe from the collection of recipes that match ingredient condition.
     * In this scenario one of the ingredients has higher quantity requirement.
     *
     * @return bool
     */
	public function testFindValidRecipe2() {
		
		// Create and fille the fridge.
		$fridge = new Fridge();
		$recipeCollection = new RecipeCollection();
		$fridge->addItem(new FridgeIngredient('test1', 10, 'grams', '21/10/2016'));
		$fridge->addItem(new FridgeIngredient('test2', 2, 'slices', '21/11/2016'));
		$fridge->addItem(new FridgeIngredient('test3', 100, 'ml', '01/01/2016'));
		
		// Create recipe.
		$recipe = new Recipe('Test recipe');
		$recipe->addIngredient(new Ingredient('test1', 20, 'grams'));
		$recipe->addIngredient(new Ingredient('test2', 1, 'slices'));
		$recipe->addIngredient(new Ingredient('test3', 50, 'ml'));
		
		// Add to recipe collection.
		$recipeCollection->addRecipe($recipe);
		
		return assert('$fridge->findRecipe($recipeCollection) == false');
	}
	
	/**
     * Test - find recipe from the collection of recipes that match ingredient condition.
     * In this scenario one of the ingredients has expired.
     *
     * @return bool
     */
	public function testFindValidRecipe3() {
		
		// Create and fille the fridge.
		$fridge = new Fridge();
		$recipeCollection = new RecipeCollection();
		$fridge->addItem(new FridgeIngredient('test1', 10, 'grams', '21/10/2013'));
		$fridge->addItem(new FridgeIngredient('test2', 2, 'slices', '21/11/2016'));
		$fridge->addItem(new FridgeIngredient('test3', 100, 'ml', '01/01/2016'));
		
		// Create recipe.
		$recipe = new Recipe('Test recipe');
		$recipe->addIngredient(new Ingredient('test1', 10, 'grams'));
		$recipe->addIngredient(new Ingredient('test2', 1, 'slices'));
		$recipe->addIngredient(new Ingredient('test3', 50, 'ml'));
		
		// Add to recipe collection.
		$recipeCollection->addRecipe($recipe);
		
		return assert('$fridge->findRecipe($recipeCollection) == false');
	}
	
	/**
     * Test - find recipe from the collection of recipes that match ingredient condition.
     * In this scenario there are two recipes one with ingredients closer to expiry date.
     *
     * @return bool
     */
	public function testFindValidRecipe4() {
		
		// Create and fille the fridge.
		$fridge = new Fridge();
		$recipeCollection = new RecipeCollection();
		$fridge->addItem(new FridgeIngredient('test1', 10, 'grams', '21/11/2016'));
		$fridge->addItem(new FridgeIngredient('test2', 2, 'slices', '21/11/2016'));
		$fridge->addItem(new FridgeIngredient('test3', 100, 'ml', '01/11/2016'));
		
		// Create recipe.
		$recipe = new Recipe('Test recipe');
		$recipe->addIngredient(new Ingredient('test1', 10, 'grams'));
		$recipe->addIngredient(new Ingredient('test2', 1, 'slices'));
		
		// Add to recipe collection.
		$recipeCollection->addRecipe($recipe);
		
		// Create recipe.
		$recipe = new Recipe('Test recipe 2');
		$recipe->addIngredient(new Ingredient('test1', 10, 'grams'));
		$recipe->addIngredient(new Ingredient('test3', 50, 'ml'));
		
		// Add to recipe collection.
		$recipeCollection->addRecipe($recipe);
		$foundRecipe = $fridge->findRecipe($recipeCollection);
		
		return assert('$foundRecipe->getName() == \'Test recipe 2\'');
	}
	
	/**
     * Test - find recipe from the collection of recipes that match ingredient condition.
     * In this scenario there are two recipes with all ingredients having the same expiry date.
     *
     * @return bool
     */
	public function testFindValidRecipe5() {
		
		// Create and fille the fridge.
		$fridge = new Fridge();
		$recipeCollection = new RecipeCollection();
		$fridge->addItem(new FridgeIngredient('test1', 10, 'grams', '21/11/2016'));
		$fridge->addItem(new FridgeIngredient('test2', 2, 'slices', '21/11/2016'));
		$fridge->addItem(new FridgeIngredient('test3', 100, 'ml', '21/11/2016'));
		
		// Create recipe.
		$recipe = new Recipe('Test recipe');
		$recipe->addIngredient(new Ingredient('test1', 10, 'grams'));
		$recipe->addIngredient(new Ingredient('test2', 1, 'slices'));
		
		// Add to recipe collection.
		$recipeCollection->addRecipe($recipe);
		
		// Create recipe.
		$recipe = new Recipe('Test recipe 2');
		$recipe->addIngredient(new Ingredient('test1', 10, 'grams'));
		$recipe->addIngredient(new Ingredient('test3', 50, 'ml'));
		
		// Add to recipe collection.
		$recipeCollection->addRecipe($recipe);
		$foundRecipe = $fridge->findRecipe($recipeCollection);
		
		return assert('$foundRecipe->getName() == \'Test recipe\'');
	}
}

/**
 * @name RecipeTest class
 * @author Alex Agafonov
 * @desc Unit testing for Recipe class 
 */
class RecipeTest {

	/**
     * Test - recipe name cannot be empty.
     *
     * @return bool
     */
	public function testRecipeNameCannotBeEmpty() {
		
		$errorMessage = '';
		try {
			$ingredient = new Recipe('');
		} catch (Exception $e) {
			$errorMessage = $e->getMessage();
		}
		return assert('$errorMessage == \'Recipe name cannot be empty.\'');
	}
	
	/**
     * Test - input parameter must be of type Ingredient.
     *
     * @return bool
     */
	public function testIngredientMustBeOfTypeIngredient() {
		
		try {
			$ingredient = new Recipe('Test recipe');
			$ingredient->addIngredient(array(1 => 1));
		} catch (Exception $e) {
			$errorMessage = $e->getMessage();
		}
		return assert('$errorMessage == \'Passed item is not a valid Ingredient object.\'');
	}
}

/**
 * @name RecipeCollectionTest class
 * @author Alex Agafonov
 * @desc Unit testing for RecipeCollection class 
 */
class RecipeCollectionTest {
	
	/**
     * Test - input parameter must be of type Recipe.
     *
     * @return bool
     */
	public function testRecipeMustBeOfTypeRecipe() {
		
		try {
			$recipeCollection = new RecipeCollection();
			$recipeCollection->addRecipe(array(1 => 1));
		} catch (Exception $e) {
			$errorMessage = $e->getMessage();
		}
		return assert('$errorMessage == \'Passed item is not a valid Recipe object.\'');
	}
}

?>