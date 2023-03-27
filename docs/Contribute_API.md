# Contributing to the the API

Adding functions to the API is a relatively easy task, which requires little to no prior experience with PHP. In this tutorial I will outline the steps that can take you from zero to pull request.

For the purposes of this tutorial we will create a function which reverses the contents of a file.

## Step 1: Create a `RegistrableFunction`

Create a class file inside a relevant subdirectory of [`lib/Interpreter/Functions`](https://github.com/Raudius/files_scripts/tree/master/lib/Interpreter/Functions), and make the class extend the `RegistrableFunction` 

Note that the Lua function name will match the PHP function, except all letters will be lower case.

<details>
<summary>Show code</summary>

```php
namespace OCA\FilesScripts\Interpreter\Functions\Util;

use OCA\FilesScripts\Interpreter\RegistrableFunction;

class Invert_Content extends RegistrableFunction {
  public function run() {}
}
```
</details>

## Step 2: Set the inputs

To prevent runtime errors dont set types to the parameters and make sure to set default values for each one. Then validate each parameter accordingly.

It is handy to make use of certain `RegistrableFunction` functions: for example when unpacking file data into file objects, or when reindexing Lua tables to PHP arrays. You can find examples of this in other API function implementations.

<details>
<summary>Show code</summary>

```php
namespace OCA\FilesScripts\Interpreter\Functions\Util;

use OCA\FilesScripts\Interpreter\RegistrableFunction;

class Invert_Content extends RegistrableFunction {
  public function run($file = [], $overwrite = false): bool {
    // Unpack the file data into an `OCP\Files\File` object
    $node = $this->getFile($this->getPath($node));

    // Check that file exists
    if ($node === null) {
      return false;
    }
  }
}
```
</details>

## Step 3: Write the logic

Depending on what your function will do you may need to familiarize yourself with some of the internal workings of the Nextcloud public API. For example for working with Nextcloud files you will need to look into the [`OCP\Files\Node`](https://github.com/nextcloud/server/blob/master/lib/public/Files/Node.php) class.
<details>
<summary>Show code</summary>


```php
namespace OCA\FilesScripts\Interpreter\Functions\Util;

use OCA\FilesScripts\Interpreter\RegistrableFunction;

class Invert_Content extends RegistrableFunction {
  public function run($file = [], $overwrite = false): bool {
    // Unpack the file data into an `OCP\Files\File` object
    $node = $this->getFile($this->getPath($node));

    // Check that file exists
    if ($node === null) {
      return false;
    }
    
    try {
      // Determine the target file (if not overwriting, create a new file with "reversed_" prepended to the name)
      if ($overwrite) {
        $targetNode = $node;
      } else {
        $newName = 'reversed_' . $node->getName();
        $targetNode = $node->getParent()->newFile($newName);
      }
      
      // Get content from input file and reverse it
      $content = strrev($node->getContent());
      $targetNode->putContent($content);
    } catch (\Exception $e) {
      // This will catch errors from creating/writing the target node.
      // Although omitted, its usually wise to log some error here.
      return false;
    }
    
    return true;
  }
}
```
</details>

## Step 4: Register the function

Simply add the file to the default [`FunctionProvider`](https://github.com/Raudius/files_scripts/blob/master/lib/Interpreter/FunctionProvider.php) by appending it to the long list of functions in the constructor. Let the dependency injection do the magic of instantiating the class.

Now you can test your function by using it in a file action!
<details>
<summary>Show code</summary>

```php
// Import your class
use OCA\FilesScripts\Interpreter\Functions\Util\Invert_Content;

class FunctionProvider implements IFunctionProvider {
  public function __construct(
    // ...
    // ...
    // Add your class to the end of the list in the constructor
    Invert_Content $f100
  ) {
```
</details>


## Step 5: Add docummentation
You can document your function directly in the PHP file by adding a doc comment (starting with `/**` instead of `/*`) to the class.
Make sure to stick to the somewhat standarized formatting of other functions in the API and make full use of Markdown formatting where appropriate.
<details>
<summary>Show code</summary>

```php
namespace OCA\FilesScripts\Interpreter\Functions\Util;

use OCA\FilesScripts\Interpreter\RegistrableFunction;

/**
 * `invert_content(File file, [Bool overwrite=True]): Bool`
 *
 * Inverts the content of the current file. Returns whether the operation was successful.
 * 
 * The inverted version will be written to a new file called `reversed_{filename}` in the same folder as the input file. 
 * If the `overwrite` parameter is set to `true` the inverted version will be written directly to the input file.
 * 
 * Example:
 * ```lua
 * local file = get_input_files()[1]
 * local success = invert_content(file, true)
 * ```
 */
class Invert_Content extends RegistrableFunction {
  // ...
```
</details>

Do not worry about generating the Markdown files inside `docs/`. This is done before each release by running the script inside `/bin/generate_docs.php`
