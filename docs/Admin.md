# Admin documentation

Before users can start using any custom actions, you must add them through the admin menu. These can be found in the `FileActions` section of the administration menu. On a fresh install some ready-made actions should be available there (albeit disabled for the user).

## User input
Other than the selected files, you can ask the user to input other data as part of the action. 

### Target folder
By selecting the `Request target folder` option, the user will be given the option to select a folder. This folder will be accessible to the scripting API via the [`get_target_folder()`](Functions.md#get_target_folder) function.
A common use for this input option is to select a location where to save the file.

### Input values
Any number of additional input values can be added in the script-creation view. These are made available to the scripting API via the [`get_input()`](Functions.md#get_input) function. 

### Required fields
User input fields will always be optional: the action may still be triggered even if the user did not input anything. However, additional checks can be performed on the script itself, and an [error](Functions.md#abort) may be returned to the user if any required fields were not (correctly) filled.

```lua
local file_name = get_input().name
if (not file_name or string.len(file_name) == 0) then
  abort('File name was not filled in.')
end 
```


## Scripting

### [Scripting API](Functions.md)
Since the app is still new, the scripting API is prone to changing, so I am hesitant to write extensive documentation/tutorials on how to use it. If you have any questions feel free to open a GitHub ticket.

### Dealing with files / folders
The scripting API has been extended with Nextcloud-specific functions. These functions may require a "Folder" or a "File" as an input. Lua of course does not know what a file or a folder are, so instead we rely on Lua tables to carry the information required to represent these objects.

The `Node` type which may be referenced in the [functions documentation](Functions.md), is nothing more than a table containing 3 values:
 * `id`: the internal ID of the file or folder
 * `name`: the name of the file or folder
 * `path`: the path (not including the name) to the file or folder from the user's home folder 

In principle if you know these 3 values you could construct the Table yourself, but convenient [functions](Functions.md#Files) are available to traverse and operate on Nextcloud files.

### Testing scripts

If you are writing a new script and need to test it I would highly advise using a test environment. This will avoid any mishaps from affecting any live data.

If for any reason you must test your scripts on a live environment, and do not want to make the scripts available to users yet, you can limit the app to the "admin" group from the Nextcloud "Apps" page (*your.nextcloud.com/index.php/settings/apps/installed/files_scripts*), by ticking the `Limit to groups` option.
