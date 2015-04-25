#!/usr/bin/php
<?php

/**
 * @name Enum abstract class
 * @author Alex Agafonov
 * @desc Implements basic enum type.
 */
abstract class Enum {
	
	/**
     * Check if enum has certain index.
     *
     * @param $item string  index
     * @return bool
     */
	public static function has($item) {
		return defined('static::'.$item);
	}
	
	/**
     * Get value of index.
     *
     * @param $item string  index
     * @return mixed
     */
	public static function get($item) {
		return constant('static::'.$item);
	}
}

/**
 * @name UnitsOfMeasure abstract class
 * @author Alex Agafonov
 * @desc Enum for units of measure
 */
class UnitsOfMeasure extends Enum {
    const grams = 0;
    const ml = 1;
    const slices = 2;
}

/**
 * @name Ingredient class
 * @author Alex Agafonov
 * @desc Contains information about single ingridient.
 */
class Ingredient {
    
    /**
     * Item name.
     *
     * @var string
     */
    private $itemName;
    
    /**
     * The amount.
     *
     * @var int
     */
    private $amount;
    
    /**
     * The unit of measure, values of (for individual items eggs, bananas etc) grams, ml (milliliters), slices.
     *
     * @var enum
     */
    private $unitOfMeasure;
    
    /**
     * Use by date.
     *
     * @var date
     */
    private $useByDate;
    
    /**
     * Transforms associative array into key value array using index names.
     *
     * @param $itemName string  item name
     * @param $amount   int     the amount
     * @param $unitOfMeasure    enum    the unit of measure
     * @param $useByDate    date    use by date
     * @return void
     */
    public function __construct($itemName, $amount, $unitOfMeasure) {
        
        $this->setItemName($itemName);
        $this->setAmount($amount);
        $this->setUnitOfMeasure($unitOfMeasure);
    }
    
    /**
     * Sets item name.
     *
     * @param $itemName string  item name
     * @return void
     */
    public function setItemName($itemName) {
        
        // Validate item name.
        if (!$itemName) {
            throw new InvalidArgumentException('Item name cannot be empty.');
        }
        
        $this->itemName = $itemName;
    }
    
    /**
     * Gets item name.
     *
     * @return string
     */
    public function getItemName() {
        
        return $this->itemName;
    }
    
    /**
     * Sets amount.
     *
     * @param $amount   int     the amount
     * @return void
     */
    public function setAmount($amount) {
        
        // Make sure amount is integer.
        if (filter_var($amount, FILTER_VALIDATE_INT) === false) {
            throw new InvalidArgumentException('Item amount must be an integer value.');
        }
        
        $this->amount = $amount;
    }
    
    /**
     * Gets amount.
     *
     * @return int
     */
    public function getAmount() {
        
        return $this->amount;
    }
    
    /**
     * Sets units of measure.
     *
     * @param $unitOfMeasure    enum    the unit of measure
     * @return void
     */
    public function setUnitOfMeasure($unitOfMeasure) {
        
        // Make sure units of measure is a valid enum value.
        if (!UnitsOfMeasure::has($unitOfMeasure)) {
            throw new InvalidArgumentException('Units of measure '.$unitOfMeasure.' is not supported.');
        }
        
        $this->unitOfMeasure = UnitsOfMeasure::get($unitOfMeasure);
    }
    
    /**
     * Gets units of measure.
     *
     * @return int
     */
    public function getUnitOfMeasure() {
        
        return $this->unitOfMeasure;
    }
}

/**
 * @name Ingredient class
 * @author Alex Agafonov
 * @desc Contains information about single ingridient.
 */
class FridgeIngredient extends Ingredient {
    
    /**
     * Use by date.
     *
     * @var date
     */
    private $useByDate;
    
    /**
     * Transforms associative array into key value array using index names.
     *
     * @param $itemName string  item name
     * @param $amount   int     the amount
     * @param $unitOfMeasure    enum    the unit of measure
     * @param $useByDate    date    use by date
     * @return void
     */
    public function __construct($itemName, $amount, $unitOfMeasure, $useByDate) {
        
    	parent::__construct($itemName, $amount, $unitOfMeasure);
        $this->setUseByDate($useByDate);
    }
    
    /**
     * Sets use by date.
     *
     * @param $useByDate    date    use by date
     * @return void
     */
    public function setUseByDate($useByDate) {
        
        // Parse date and make sure it is valid.
        try {
            $this->useByDate = DateTime::createFromFormat('d/m/Y', $useByDate)->setTime(0, 0);
        } catch (Exception $e) {
            throw new InvalidArgumentException('Units of measure '.$unitOfMeasure.' is not supported.');
        }
    }
    
    /**
     * Gets use by date.
     *
     * @return date
     */
    public function getUseByDate() {
        
        return $this->useByDate;
    }
    
	/**
     * Has expired.
     *
     * @return bool
     */
    public function hasExpired() {
    	$now = new DateTime();
    	$now->setTime(0, 0);
        return $this->useByDate < $now;
    }
}

/**
 * @name Fridge class
 * @author Alex Agafonov
 * @desc Contains information about the fridge and its contents.
 */
class Fridge {
    
    /**
     * Ingredients that are currently in the fridge.
     *
     * @var array
     */
    private $items;
    
    public function __construct() {
    }
    
    /**
     * Add single item to the fridge.
     *
     * @param $item FridgeIngredient  ingredient in the fridge
     * @return void
     */
    public function addItem($item) {
        $this->items[$item->getItemName()] = $item;
    }
    
    /**
     * Check if there is a certain ingredient in the fridge and it is not expired.
     *
     * @param $ingredient Ingredient ingredient in the fridge
     * @return void
     */
    public function hasUnexpiredItem($item) {
        
        // Lookup item first to see if we have it in the fridge.
        if (!isset($this->items[$item->getItemName()])) {
            return false;
        }
        
        $fridgeItem = $this->items[$item->getItemName()];
        
        //Make sure we have the right amount, unit of measurment and item is not expired. 
        if ($fridgeItem->getAmount() >= $item->getAmount() && $fridgeItem->getUnitOfMeasure() == $item->getUnitOfMeasure() && !$fridgeItem->hasExpired()) {
        	return true;
        }
        
        return false;
    }
    
    public function findRecipe($recipeCollection) {
    	
    	$currentRecipe = false;
    	$currentMinExpiryDate = false;
    	
    	// Make sure recipe collection is not empty.
    	if (!$recipeCollection->isEmpty()) {
    		
    		// Go through each recipe.
    		foreach ($recipeCollection as $recipe) {
    			
    			// Get ingredients from current recipe.
    			$ingredients = $recipe->getIngredients();
    			
    			// Make sure recipe has ingredients.
    			if (!empty($ingredients)) {
    				
    				// Flag to indicate that we found all unexpired ingredients required for the recipe.
    				$foundAllIngredients = true;
    				$minExpiryDate = false;
    				
    				// Go through each ingredient.
    				foreach ($ingredients as $ingredient) {
    					
    					// Make sure it is present in the fridge and is not expired.
    					if (!$this->hasUnexpiredItem($ingredient)) {
    						$foundAllIngredients = false;
    						break;
    					}
    					
    					// Keep track of closest expiry date.
    					$ingredientUseByDate = $this->items[$ingredient->getItemName()]->getUseByDate();
    					if ($ingredientUseByDate < $minExpiryDate || !$minExpiryDate) {
    						$minExpiryDate = $ingredientUseByDate;
    					}

    				}
    				
    				// If found all ingredients then this is our recipe.
    				if ($foundAllIngredients) {
    					
    					//Check that expiry date is closest compared to tge previously found recipe.
    					if ($minExpiryDate < $currentMinExpiryDate || !$currentMinExpiryDate) {
	    					$currentRecipe = $recipe;
	    					$currentMinExpiryDate = $minExpiryDate;
    					}
    				}
    			}
    		}
    	}
    	
    	return $currentRecipe;
    }
    
}

class FridgeManager {
    
    public static function fillFridgeFromCSVFile(&$fridge, $csvPath) {
    	
    	// Make sure file exists.
    	if (!is_readable($csvPath)) {
    		throw new Exception('Cannot open '.$csvPath.'. The file does not exist of you do not have permissions to access it.');
    	}
    	
    	// Open file.
    	if (!$handle = fopen($csvPath, 'r')) {
    		throw new Exception('Could not open file '.$csvPath.'.');
    	}
    	
    	$line = 0;
    	
    	try {

	    	// Go through each row in the file and fill the fridge
	    	while($row = fgetcsv($handle)) {
	    		
	    		// Instantiate FridgeIngredient class.
	    		$line++;
	    		$fridgeIngredient = new FridgeIngredient($row[0], $row[1], $row[2], $row[3]);
	    		$fridge->addItem($fridgeIngredient);
	    	}
    	} catch (Exception $e) {
    		
    		fclose($handle);
    		throw new Exception('CSV file import error on line '.$line.': '.$e->getMessage());
    	}
    }
    
}

/**
 * @name Recipe class
 * @author Alex Agafonov
 * @desc Contains information about the recipe.
 */
class Recipe {
    
    /**
     * Recipe name.
     *
     * @var string
     */
    private $name;
    
    /**
     * List of ingredients.
     *
     * @var array
     */
    private $ingredients;
    
    public function __construct($name, $ingredients = array()) {
        
        $this->setName($name);
        if (!empty($ingredients)) {
            foreach ($ingredients as $ingredient) {
                $this->setIngredient($ingredient);
            }
        }
    }
    
    /**
     * Sets recipe name.
     *
     * @param $name string  recipe name
     * @return void
     */
    public function setName($name) {
        
        // Validate recipe name.
        if (!$name) {
            throw new InvalidArgumentException('Recipe name cannot be empty.');
        }
        
        $this->name = $name;
    }
    
    /**
     * Gets recipe name.
     *
     * @return string
     */
    public function getName() {
        
        return $this->name;
    }
    
    /**
     * Adds ingredient to the recipe.
     *
     * @param $item Ingredient  ingredient
     * @return void
     */
    public function addIngredient($item) {
        
        // Validate recipe name.
        if (get_class($item) != 'Ingredient') {
            throw new InvalidArgumentException('Passed item is not a valid Ingredient object.');
        }
        
        $this->ingredients[] = $item;
    }
    
 	/* Get all ingredients.
     *
     * @return array
     */
    public function getIngredients() {
        return $this->ingredients;
    }
}

/**
 * @name Recipe class
 * @author Alex Agafonov
 * @desc Contains information about the recipe.
 */
class RecipeCollection implements Iterator {
    
    /**
     * List of ingredients.
     *
     * @var array
     */
    private $recipes;
    
    public function __construct() {
    }
    
    /**
     * Adds recipe to collection.
     *
     * @param $item Recipe  ingredient
     * @return void
     */
    public function addRecipe($recipe) {
        
        // Validate recipe name.
        if (get_class($recipe) != 'Recipe') {
            throw new InvalidArgumentException('Passed item is not a valid Recipe object.');
        }
        
        $this->recipes[] = $recipe;
    }
    
	/**
     * Return the current element.
     *
     * @return int
     */
  	public function current() {
    	return current($this->recipes);
  	}
  
	/**
     * Return the key of the current element.
     *
     * @return int
     */
  	public function key() {
    	return key($this->recipes);
  	}
  	
	/**
     * Move forward to next element.
     *
     * @return Recipe
     */
  	public function next() {
    	return next($this->recipes);
  	}
  	
	/**
     * Rewind the Iterator to the first element.
     *
     * @return bool
     */
	public function rewind() {
		return reset($this->recipes);
  	}
  	
  	/**
     * Checks if current position is valid.
     *
     * @return bool
     */
  	public function valid() {
    	return key($this->recipes) !== null;
  	}
  	
	/**
     * Checks if collection is empty.
     *
     * @return bool
     */
  	public function isEmpty() {
    	return count($this->recipes)>0?false:true;
  	}
}

class RecipeManager {
    	
    public static function fillRecipeCollectionFromJSONString(&$recipeCollection, $jsonData) {
        
    	// Decode json data.
    	if ($recipeList = json_decode($jsonData)) {
    		
    		// Go through all recipes and add to collection.
    		foreach ($recipeList as $recipeItem) {

    			// Make sure dataset has ingredients.
    			if (isset($recipeItem->ingredients) && !empty($recipeItem->ingredients)) {
	    			
    				// Instantiate Recipe class.
    				$recipe = new Recipe($recipeItem->name);
    			
    				// Go through each ingredient and add it to the Recipe object.
    				foreach ($recipeItem->ingredients as $ingredient) {
    					
    					// Instantiate Ingredient class.
    					$ingredient = new Ingredient($ingredient->item, $ingredient->amount, $ingredient->unit);
    					$recipe->addIngredient($ingredient);
    				}
    				
    				// Add recipe to ingredients.
    				$recipeCollection->addRecipe($recipe);
    			}
    		}
    	}
    }
    
	public static function fillRecipeCollectionFromJSONFile(&$recipeCollection, $jsonFilePath) {
    	
	    // Make sure file exists.
	    if (!is_readable($jsonFilePath)) {
	    	throw new Exception('Cannot open '.$jsonFilePath.'. The file does not exist of you do not have permissions to access it.');
	    }
	    
	    // Open file.
	    if (!$handle = fopen($jsonFilePath, 'r')) {
	    	throw new Exception('Could not open file '.$jsonFilePath.'.');
	    }
	    
	    try {
	
	    	// Get contents of the file.
	    	$jsonData = fread($handle, filesize($jsonFilePath));
	    	self::fillRecipeCollectionFromJSONString($recipeCollection, $jsonData);
	    	
	    } catch (Exception $e) {
	    	
	    	fclose($handle);
	    	throw new Exception('JSON file import error: '.$e->getMessage());
	    }
    	
    }
}

// Check command line input.
if ($argv[1] == 'run') {
	
	// Make sure JSON file was provided.
	if (!isset($argv[2])) {
		echo 'Error: missing recipe JSON data file.';
	}
	
	// Make sure CSV file was provided.
	if (!isset($argv[3])) {
		echo 'Error: missing fridge content CSV data file.';
	}
	
	try {
		
		// Import json file into recipe collection.
		$recipeCollection = new RecipeCollection();
		RecipeManager::fillRecipeCollectionFromJSONFile($recipeCollection, $argv[2]);
		
		// Import fringe content into Fridge structure. 
		$fridge = new Fridge();
		FridgeManager::fillFridgeFromCSVFile($fridge, $argv[3]);
		if ($recipe = $fridge->findRecipe($recipeCollection)) {
			echo $recipe->getName()."\n";
		} else {
			echo 'Order Takeout'."\n";
		}
	} catch (Exception $e) {
		
		// Got an error.
		echo 'Error: '.$e->getMessage()."\n";
	}
}



