<?php

/**
 * @name IngredientTest class
 * @author Alex Agafonov
 * @desc Unit testing for Ingredient class 
 */
class IngredientTest {

	public function testItemNameCannotBeEmpty() {
		
		$errorMessage = '';
		try {
			$ingredient = new Ingredient('', 10, 'grams');
		} catch (Exception $e) {
			$errorMessage = $e->getMessage();
		}
		return assert('$errorMessage == \'Item name cannot be empty.\'');
	}
	
	public function testAmountMustBeInteger() {
		
		$errorMessage = '';
		try {
			$ingredient = new Ingredient('test', 'a', 'grams');
		} catch (Exception $e) {
			$errorMessage = $e->getMessage();
		}
		return assert('$errorMessage == \'Item amount must be an integer value.\'');
	}
	
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
 * @name IngredientTest class
 * @author Alex Agafonov
 * @desc Unit testing for Ingredient class 
 */
class FridgeIngredientTest {

	public function testUseByDateCannotAcceptInvalidDateFormat() {
		
		$errorMessage = '';
		try {
			$ingredient = new FridgeIngredient('test', 10, 'grams', '2012-10-21');
		} catch (Exception $e) {
			$errorMessage = $e->getMessage();
		}
		return assert('$errorMessage == \'Use by date format is not supported.\'');
	}
	
	public function testUseByDateMustHaveValidDateFormat() {
		
		$ingredient = new FridgeIngredient('test', 10, 'grams', '21/10/2014');
		$useByDate = $ingredient->getUseByDate();
		return assert('$useByDate->format(\'Y-m-d\') == \'2014-10-21\'');
	}
	
	public function testUseByDateExpired1() {
		
		$ingredient = new FridgeIngredient('test', 10, 'grams', '21/10/2014');
		return assert('$ingredient->hasExpired() == true');
	}
	
	public function testUseByDateExpired2() {
		
		$ingredient = new FridgeIngredient('test', 10, 'grams', '21/10/2016');
		return assert('$ingredient->hasExpired() == false');
	}
}

/**
 * @name IngredientTest class
 * @author Alex Agafonov
 * @desc Unit testing for Ingredient class 
 */
class FridgeTest {
	
	public function testHasUnexpiredItem1() {
		
		$fridge = new Fridge();
		$ingredient = new FridgeIngredient('test', 10, 'grams', '21/10/2012');
		$fridge->addItem($ingredient);
		$ingredient = new Ingredient('test', 10, 'grams');
		return assert('$fridge->hasUnexpiredItem($ingredient) == false');
	}
	
	public function testHasUnexpiredItem2() {
		
		$fridge = new Fridge();
		$ingredient = new FridgeIngredient('test', 10, 'grams', '21/10/2016');
		$fridge->addItem($ingredient);
		$ingredient = new Ingredient('test', 10, 'grams');
		return assert('$fridge->hasUnexpiredItem($ingredient) == true');
	}
	
	public function testHasUnexpiredItem3() {
		
		$fridge = new Fridge();
		$ingredient = new FridgeIngredient('test', 10, 'grams', '21/10/2016');
		$fridge->addItem($ingredient);
		$ingredient = new Ingredient('test', 20, 'grams');
		return assert('$fridge->hasUnexpiredItem($ingredient) == false');
	}
	
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
 * @name IngredientTest class
 * @author Alex Agafonov
 * @desc Unit testing for Ingredient class 
 */
class RecipeTest {

	public function testRecipeNameCannotBeEmpty() {
		
		$errorMessage = '';
		try {
			$ingredient = new Recipe('');
		} catch (Exception $e) {
			$errorMessage = $e->getMessage();
		}
		return assert('$errorMessage == \'Recipe name cannot be empty.\'');
	}
	
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
 * @name IngredientTest class
 * @author Alex Agafonov
 * @desc Unit testing for Ingredient class 
 */
class RecipeCollectionTest {
	
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