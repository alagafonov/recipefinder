## Notes

1. Normally classes would be separated into individual files however to avoid using autoloader and adding complexity to the task all classes were placed into a single file.

2. To avoid adding extra dependency unit testing has been done using PHP's assert finction instead of using a framework (e.g. PHPUnit).

## Deployment

Run the following in command line:

1. Main program:

	*./RecipeFinder.php run RecipeCollection.json FridgeContents.csv*

2. Unit testing:

	*./RecipeFinder.php test*