## Notes

1. Normally classes would be separated into individual files however to avoid using autoloader and adding complexity to the task all classes were placed into one file.

2. To avoid adding extra dependencies unit testing was done using PHP's assert function instead of a framework (e.g. PHPUnit).

## Deployment

Run the following in command line:

1. Main program:

	*./RecipeFinder.php run RecipeCollection.json FridgeContents.csv*

2. Unit testing:

	*./RecipeFinder.php test*