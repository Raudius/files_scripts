# Admin documentation

Before users can start using any custom actions, you must add them through the admin menu. These can be found in the `File actions` section, in the administration settings menu. On a fresh install some ready-made actions should be available there (albeit disabled for the user).

## User input
Other than the selected files, you can ask the user to input other data as part of the action. 

### Target folder
By selecting the `Request target folder` option, the user will be given the option to select a folder. This folder will be accessible to the scripting API via the [`get_target_folder()`](Functions.md#get_target_folder) function.
A common use for this input option is to select a location where to save the file.

### Input values
Any number of additional input values can be added in the script-creation view. These are made available to the scripting API via the [`get_input()`](Functions.md#get_input) function. 

By default, input types are text-fields where the user may type in the value. However other, more restrictive input types may be used:

- **Checkbox** allows for a boolean (`true`/`false`)  input from the user
- **Multiselect** allows the user to select from a set of pre-determined inputs.
- **File-picker** allows the user to choose a document from their Nextcloud files. 

Note: to allow a user to pick a folder the `httpd/unix-directory` mimetype must be added to the filepick options.

### Required fields
User input fields will always be optional: the action may still be triggered even if the user did not input anything. However, additional checks can be performed on the script itself, and an [error](Functions.md#abort) may be returned to the user if any required fields were not (correctly) filled.

```lua
local file_name = get_input('file_name')
if (not file_name or string.len(file_name) == 0) then
  abort('File name was not filled in.')
end 
```


## Scripting

### [Scripting API](Functions.md)
Since the app is still new, the scripting API is prone to changing, so I am hesitant to write extensive documentation/tutorials on how to use it. If you have any questions feel free to open a GitHub ticket.

You can also find example scripts in the [`examples/`](/examples)  folder, these will be also included when you first install the app.

### Nextcloud objects
The scripting API has been extended with Nextcloud-specific functions. These functions may require a "Folder" or a "File" as an input. Lua of course does not know what a file or a folder are, so instead we rely on Lua tables to carry the information required to represent these objects.

These tables may be manually created, but it is preferable to use function that return these objects.

#### Node (File / Folder)
The `Node` type is used to represent a file or folder (see: [is_file](Functions.md) and [is_folder](Functions.md)):
 * `id`: the internal ID of the file or folder
 * `name`: the name of the file or folder
 * `path`: the path (not including the name) to the file or folder from the user's home folder 

#### User
 * `uuid`
 * `display_name`
 * `email_address`

#### Tag
 * `id`
 * `name`
 * `user_assignable`
 * `user_visible`
 * `access_level`

### Testing scripts

If you are writing a new script and need to test it I would highly advise using a test environment. This will avoid any mishaps from affecting any live data.

If for any reason you must test your scripts on a live environment, and do not want to make the scripts available to users yet, you can limit the app to the "admin" group from the Nextcloud "Apps" page (*\<hostname\>/index.php/settings/apps/installed/files_scripts*), by ticking the `Limit to groups` option.

## Flow

Actions can also be configured to work with Nextcloud's automated flows.

When running a script from a flow `get_input_files()` will only contain the file that triggered the flow, and `get_target_folder()` will be the folder containing the file. 

Additionally, for "file rename" and "file copy" events, the previous file's path can be accessed with: `get_input('old_node_path')`
