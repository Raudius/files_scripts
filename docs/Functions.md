  - **[Files:](#Files)** Filesystem operations within the Nextcloud environment
    - [directory_listing](#directory_listing)  
    - [exists](#exists)  
    - [exists_unsafe](#exists_unsafe)  
    - [file_content](#file_content)  
    - [file_copy](#file_copy)  
    - [file_copy_unsafe](#file_copy_unsafe)  
    - [file_delete](#file_delete)  
    - [file_delete_unsafe](#file_delete_unsafe)  
    - [file_move](#file_move)  
    - [file_move_unsafe](#file_move_unsafe)  
    - [file_unlock](#file_unlock)  
    - [full_path](#full_path)  
    - [get_parent](#get_parent)  
    - [home](#home)  
    - [is_file](#is_file)  
    - [is_folder](#is_folder)  
    - [meta_data](#meta_data)  
    - [new_file](#new_file)  
    - [new_folder](#new_folder)  

  - **[Input:](#Input)** Retrieving user inputs
    - [get_input](#get_input)  
    - [get_input_files](#get_input_files)  

  - **[Media:](#Media)** Functions for modifying images, video, audio...
    - [ffmpeg](#ffmpeg)  
    - [ffprobe](#ffprobe)  

  - **[Nextcloud:](#Nextcloud)** Nextcloud specific functionality
    - [comment_create](#comment_create)  
    - [comment_delete](#comment_delete)  
    - [comments_find](#comments_find)  
    - [get_activity](#get_activity)  
    - [get_file_tags](#get_file_tags)  
    - [notify](#notify)  
    - [share_delete](#share_delete)  
    - [share_file](#share_file)  
    - [shares_find](#shares_find)  
    - [tag_create](#tag_create)  
    - [tag_file](#tag_file)  
    - [tag_file_unassign](#tag_file_unassign)  
    - [tags_find](#tags_find)  
    - [users_find](#users_find)  

  - **[Output:](#Output)** Reporting. logging and post-execution functions.
    - [abort](#abort)  
    - [add_message](#add_message)  
    - [clear_messages](#clear_messages)  
    - [log](#log)  
    - [view_files](#view_files)  

  - **[Pdf:](#Pdf)** Modify PDFs (requires qpdf server package)
    - [pdf_decrypt](#pdf_decrypt)  
    - [pdf_merge](#pdf_merge)  
    - [pdf_overlay](#pdf_overlay)  
    - [pdf_page_count](#pdf_page_count)  
    - [pdf_pages](#pdf_pages)  

  - **[Template:](#Template)** Generate files from templates
    - [html_to_pdf](#html_to_pdf)  
    - [mustache](#mustache)  

  - **[Util:](#Util)** Utility functions for scripting convenience
    - [create_date_time](#create_date_time)  
    - [csv_to_table](#csv_to_table)  
    - [for_each](#for_each)  
    - [format_date_time](#format_date_time)  
    - [format_price](#format_price)  
    - [http_request](#http_request)  
    - [include](#include)  
    - [json](#json)  
    - [shell_command](#shell_command)  
    - [sort](#sort)  
    - [wait](#wait)  

## Files
### directory_listing

`directory_listing(Node folder, [String filter_type]='all'): Node[]`  
  
Returns a list of the directory contents, if the given node is not a folder, returns an empty list.  
Optionally a second argument can be provided to filter out files or folders:  
 - If `"file"` is provided: only files are returned  
 - If `"folder"` is provided: only folders are returned  
 - If any other value is provided: both files and folders are returned.
### exists

`exists(Node node, [String file_name]=nil): Bool`  
  
Returns whether a file or directory exists.  
Optionally the name of a file can be specified as a second argument, in which case the first argument will be assumed to be directory. The function will return whether the file exists in the directory.
### exists_unsafe

`exists_unsafe(String path): Bool`  
  
Returns whether a file or directory exists. The expected path must be from the server root directory (e.g. `/alice/files/example.txt`).  
For most cases it is recommended to use the function [exists()](#exists).
### file_content

`file_content(Node node): String|nil`  
  
Returns the string content of the file. If the node is a directory or the file does not exist, `nil` is returned.
### file_copy

`file_copy(Node node, String folder_path, [String name]=nil): Node|nil`  
  
Copies the given node (file or folder) to the specified `folder_path`.  
Optionally a new name can be specified for the file, if none is specified the original name is used.  
  
If the target file already exists, the operation will not succeed.  
  
Returns the resulting file node, or `nil` if the operation failed.
### file_copy_unsafe

`file_copy_unsafe(Node file, String folder_path, [String name]=nil): Node|nil`  
  
Unsafe version of [`file_copy`](#file_copy).  
This function expects an absolute path from the server root (not from the users home folder). This means that files can be copied to locations which the user running the action does not have access to.  
This function performs no validation on the given path and does not check for file overwrites (overwrite handling is left up to the Nextcloud server implementation).  
  
⚠️ Use of this function is strongly discouraged as it offers no safeguards against data-loss and carries potential security concerns.  
  
```lua  
local file = get_input_files()[1]  
file_copy_unsafe(file, "alice/files/inbox", "message.txt")  
```
### file_delete

`file_delete(Node node, [Bool success_if_not_found]=true, [Bool bypass_trahsbin]=true): Bool`  
  
Deletes the specified file/folder node.  
Returns whether deletion succeeded.  
  
By default, the function also returns true if the file was not found. This behaviour can be changed by setting its second argument to `false`.  
  
The third argument `bypass_trashbin` may be used to delete the file permanently, if set to true.
### file_delete_unsafe

`file_delete_unsafe(String path, [Bool success_if_not_found]=true): Bool`  
  
Deletes a file/folder node in the given path.  
By default, the function also returns true if the file was not found. This behaviour can be changed by setting its second argument to `false`.  
  
⚠️ Use of this function is strongly discouraged as it may allow users to delete files from other users.
### file_move

`file_move(Node file, [String folder = nil], [String new_name = nil]): Node|null`  
  
Moves the given `file` to the specified `folder`.  
If no folder is given, the current folder is used (file rename).  
If no new_name is given, the old name is used.  
  
If the target file already exists, the operation will not succeed.  
  
Returns the resulting file, or `nil` if the operation failed.
### file_move_unsafe

`file_move_unsafe(Node file, [String folder = nil], [String new_name = nil]): Node|null`  
  
Unsafe version of [`file_move`](#file_move).  
This function expects an absolute path from the server root (not from the users home folder). This means that files can be copied to locations which the user running the action does not have access to.  
This function performs no validation on the given path and does not check for file overwrites (overwrite handling is left up to the Nextcloud server implementation).  
  
⚠️ Use of this function is strongly discouraged as it offers no safeguards against data-loss and carries potential security concerns.  
  
```lua  
local file = get_input_files()[1]  
file_move_unsafe(file, "alice/files/inbox", "message.txt")  
```
### file_unlock

`file_unlock(Node node, [Bool success_if_not_found]=true): Bool`  
  
Lifts a file lock from the specified file/folder node.  
Returns whether operation succeeded.  
  
By default, the function also returns true if the file was not found. This behaviour can be changed by setting its second argument to `false`.
### full_path

`full_path(Node node): String|nil`  
  
Returns the full path of the given file or directory including the node's name.  
*Example:* for a file `abc.txt` in directory `/path/to/file` the full path is: `/path/to/file/abc.txt`.  
  
If the file does not exist `nil` is returned.
### get_parent

`get_parent(Node node): Node`  
  
Returns the parent folder for the given file or directory.  
The root of the "filesystem" is considered to be the home directory of the user who is running the script. When attempting to get the parent of the root directory, the root directory is returned.  
  
If the given file cannot be found, `nil` is returned.
### home

`home(): Node`  
  
Returns the node object for the user's home directory.
### is_file

`is_file(Node node): Bool`  
  
Returns whether the given node is a file.
### is_folder

`is_folder(Node node): Bool`  
  
Returns whether the given node is a folder.
### meta_data

`meta_data(Node node): Node`  
  
Returns an inflated Node object with additional meta-data information for the given file or directory. The additional meta-data attributes are:  
 - `size`: the size of the file (in bytes)  
 - `mimetype`: the mime-type of the file,  
 - `etag`: the entity tag of the file.  
 - `utime`: the UNIX-timestamp at which the file was uploaded to the server  
 - `mtime`: the UNIX-timestamp at which the file was last modified  
 - `can_read`: whether the user can read the file or can read files from the directory  
 - `can_delete`: whether the user can delete the file or can delete files from the directory  
 - `can_update`: whether the user can modify the file or can write to the directory  
 - `storage_path`: the path of the file relative to its storage root  
 - `local_path`: a path to a version of the file in the server's filesystem. This location might be temporary (local cache), if the file is stored in an external storage  
 - `owner_id`: the user ID from the owner of the file
### new_file

`new_file(Node folder, String name, [String content]=nil): Node|nil`  
  
Creates a new file at specified folder.  
If successful, returns the newly created file node. If file creation fails, returns `nil`.
### new_folder

`new_folder(Node parent, String name): Node|nil`  
  
Creates a new folder at the specified parent folder.  
If successful, returns the newly created folder node. If creation fails, returns `nil`.
## Input
### get_input

`get_input([String input_name=nil]): Table|any`  
  
Returns a Lua table containing the user inputs. If the optional `input_name` parameter is specified the value of the matching input is returned.  
  
```lua  
get_input() 			-- { testVar= 'input' }  
get_input('testVar') -- 'input'  
```
### get_input_files

`get_input_files(): Node[]`  
  
Returns a list of the selected files: these are the files the user selects before running the action.
## Media
### ffmpeg

`ffmpeg(Node input_file, String output_name, Table config): Node|nil`  
  
Converts the input file using FFmpeg according to the specified configuration. The output file will be placed in the same directory as the input file.  
The output file is returned, or `nil` if the operation failed.  
  
The `config` parameter expects a table with the following parameters (only the format.name parameter is needed, other config parameters are optional):  
```lua  
local config = {  
  timeout= 3600,  
  format = {  
    name= "x264",              -- ogg, webm, wmv, wmv3, x264, aac, mp3, vorbis, wav  
    audio_channels= 2,  
    audio_codec= "aac",  
    video_codec= "libx264",  
    audio_bitrate= 128,        -- in kilobits  
    video_bitrate= 2500,       -- in kilobits  
    initial_parameters= {},    -- https://github.com/PHP-FFMpeg/PHP-FFMpeg/tree/0.x#add-additional-parameters  
    additional_parameters= {}, -- https://github.com/PHP-FFMpeg/PHP-FFMpeg/tree/0.x#add-additional-parameters  
    ffmpeg_threads= 4  
  },  
  clip= {  
    start= 0,      -- Start of the clip in seconds (also accepts a string in the format [hh]:[mm]:[ss]:[frames]), defaults to 0  
    duration= 2,   -- Duration of the clip in seconds (defaults to the end of the stream)  
  },  
  width= 1920,     -- Sets output width in pixels  
  height= 1080     -- Sets output height in pixels  
}  
```  
  
**Example1** converts a file to MPEG-4 format, and sets the resolution to 500x400:  
```lua  
local wmv = ffmpeg(get_input_files()[1], "output.mp4", {  
  format = { name= "x264" },  
  width= 500,  
  height= 400  
})  
```
### ffprobe

`ffprobe(Node input_file): Table`  
  
Returns a table detailing the metadata information that could be retrieved from the input file using [ffprobe](https://ffmpeg.org/ffprobe.html).
## Nextcloud
### comment_create

`comment_create(String message, Node target, Table parameters={}): ?Comment`  
  
Writes a comment to a file or folder, returns the resulting comment object (or nil if failed).  
  
The extra parameters table accepts:  
```lua  
paramters = {  
  unsafe_impersonate_user= users_find({ ... })[1]   -- Warning: This parameter breaks intended comment behaviour  
}  
```  
  
Example:  
```lua  
comment_create("Hello world!", get_input_files()[1])  
```
### comment_delete

`comment_delete(Comment comment): Bool`  
  
Deletes a comment, returns whether the operation was successful.
### comments_find

`comments_find(Table parameters): Comment[]`  
  
Finds comment objects. The parameters table can contain the following properties:  
```lua  
local parameters = {  
  id= 481,                     -- Returns the comment with ID 481  
  parent_id= 612,              -- Returns the children of comment 612  
  node= get_input_files()[1],  -- Returns the comments for the file  
}  
```  
  
It searches for each of the provided parameters in order: `id`, `parent_id`, `file`. Returns as the first set of results possible.  
So if it finds a file by `id` it won't continue searching by `parent_id` or `file`.  
  
Examples:  
```lua  
tags({file= get_input_files()[1]}) -- Finds comments for a file  
tags({id= 21})                     -- Finds comment with ID 21  
tags({parent_id= 13})              -- Finds comments tree of comment 13  
tags({id= 21, parent_id= 13})      -- Finds comment with ID 21 or (if comment 21 does not exist) the comment tree of comment 13  
```
### get_activity

`get_activity(object): Event[]`  
  
Returns a table of activity data for the given object. Currently only `File` objects may be used for retrieving activity.  
  
If the activity app is not installed or enabled, this function returns an empty table.  
  
Example:  
```lua  
file = get_input_files()[1]  
activity = get_activity(file)  
add_message(json(activity))  
```
### get_file_tags

`get_file_tags(Node file): Tag[]`  
  
Returns a table of tags that have been assigned to a file.  
  
```lua  
-- Get tags for a file  
local file = get_input_files()[1]  
local tags = get_file_tags(file)  
  
-- Put the names of the tags into a table  
local tag_names = {}  
for _, tag in ipairs(tags) do  
	tag_names[tag.id] = tag.name  
end  
```
### notify

`notify(User user, String subject, String message): Bool`  
  
Sends a simple notification to a user.  
  
```lua  
local user = users_find()[1]  
notify(user, "Hello!", "Message goes here :)")  
```
### share_delete

`share_delete(Share share): Bool  
  
Deletes the share, returns whether the deletion succeeded.
### share_file

`share_file(Node file, Table options): Share|nil`  
  
Creates a share for the given file with the configuration options given.  
  
The configuration table may contain the following properties:  
 - `target`: The target of the share, may be a user object (see: [users_find](#users_find)), or the constant `SHARE_TARGET_LINK` may be used to create a link share.  
 - `expiration`: An expiration date for the share (see: [create_date_time](#create_date_time))  
 - `hide_download`: Whether the download button should be hidden in public (link) shares  
 - `permissions`: The [permissions](#Permissions) for the shared file, these can be combined with the bitwise-or operator `|`.  
 - `label`: A label to attach to the share  
 - `password`: A password with which to protect the share.  
 - `note`: A note to attach to the share  
 - `token`: The share token (used in public share URL: `index.php/s/<share-token>`  
  
Examples:  
```lua  
file = get_input_files()[1]  
  
-- Share the file with user with UID="alice", allow to read and modify only  
found_users = users_find(nil, "alice")  
alice = found_users[1]  
  
share_file(file, {  
  target= alice,  
  permissions= PERMISSION_READ | PERMISSION_UPDATE  
})  
  
  
-- Share the file via a public link  
share_file(file, {  
  target= SHARE_TARGET_LINK,  
  expiration= create_date_time(2025, 06, 07), -- 7th June 2025  
  password= "hunter2",  
  token= "makes-url-pretty"  
})  
```
### shares_find

`shares_find(Node|nil node=nil, Int[] share_types): Share[]`  
  
Finds shares created by, or shared with, the current user. If a node is given it finds shares for that Node. It is also possible to specify  
which [share types](#share-types) to search for. If `share_types` is omitted all types will be searched.  
  
A list of share objects are returned, share objects are Lua tables which contain the following keys:  
 - `_type`: used to identify the type of the object, always equal to `"share"`  
 - `id`: the uid of the share  
 - `full_id`: the full identifier reported by Nextcloud  
 - `node`: the node object of the shared file/folder  
 - `type`: the [type](#share-types) of share  
 - `share_owner`: the ID of the user who created the shared file  
 - `shared_by`:  the ID of the user who created the share  
 - `shared_with`: the ID of the user who received the share  
 - `permissions`: the [permissions](#Permissions) of the file  
 - `token`: the token of the share (used for link shares in the URL `/index.php/s/<share-token>`)  
  
#### Share types  
Nextcloud shares can have different types which offer different functionality, here is a list of constants provided in the API:  
 - `SHARE_TYPE_USER`: file shared with a Nextcloud user  
 - `SHARE_TYPE_GROUP`: file shared with a Nextcloud group  
 - `SHARE_TYPE_LINK`: file shared via a public link  
 - `SHARE_TYPE_REMOTE`: file shared to a federated Nextcloud instance  
 - `SHARE_TYPE_EMAIL`: file shared via email  
 - `SHARE_TYPE_ROOM`: file shared to a Talk room  
 - `SHARE_TYPE_CIRCLE`: file shared with a Nextcloud circle  
 - `SHARE_TYPE_DECK`: file attached to a Deck card  
  
#### Permissions  
When sharing a file with a user you may select what the user can do with the file, these constants can be used to check/control these permissions, constants may be checked and combined with bitwise operations:  
 - `PERMISSION_ALL`: All possible permissions, this option is equal to the bitwise-or of all other permissions  
 - `PERMISSION_READ`: User is allowed to view the file(s)  
 - `PERMISSION_CREATE`: User is able to create files within the shared location  
 - `PERMISSION_DELETE`: User is able to delete the file(s)  
 - `PERMISSION_UPDATE`: User is allowed to modify the file(s)  
 - `PERMISSION_SHARE`: User is allowed to further share the file(s)
### tag_create

`tag_create(String name, [Bool user_visible= true], [Bool user_assignable= true]): ?Tag`  
  
Creates a collaborative tag. Returns the created tag, or `nil` if the tag could not be created (i.e. a tag with the same name already exists).
### tag_file

`tag_file(Node file, Tag tag): Bool`  
  
Adds a tag to a file. Returns whether the tag was added successfully.  
  
```lua  
local tags = tags_find({id= 42})  
if (#tags == 1) then  
  tag_file(get_input_files()[1], tags[1])  
end  
```
### tag_file_unassign

`tag_file_unassign(Node file, Tag tag): Bool`  
  
Removes a tag from a file or folder. Returns whether the tag was successfully removed.
### tags_find

`tag_find(Table parameters): Tag[]`  
  
Finds existing collaborative tags. The parameters table can contain the following properties:  
```lua  
local parameters = {  
  id= 42,  
  name= "teamA",  
  user_visible= true,  
  name_exact= false      -- defaults to false  
}  
```  
  
Examples:  
```lua  
tags_find()                  -- Finds all tags  
tags({user_visible= true})   -- Finds all user-visible tags  
tags_find({name= "2021"})    -- Finds all tags that contain the substring "2021".  
tags_find({name= "2021", name_exact= true})   -- Finds an array containing a tag with the name "2021", or returns an empty array  
```
### users_find

`users_find([String name = nil], [String uuid = nil]): User[]`  
  
Finds a Nextcloud user from the given parameters.  
  
If the name is specified, the function will return all users who have a matching name. If the UUID is given the name is ignored and a user is returned with the given UUID.  
If both parameters are left empty (`nil`), the current user is returned.  
If a user that meets the parameters can't be found an empty array is returned.
## Output
### abort

`abort(String message): void`  
  
Aborts execution with an error message. This error message will be shown to the user in a toast dialog.
### add_message

`add_message(String message, [String type="info"]): void`  
  
Adds a message to be shown to the user after the action is completed as a toast message. The optional type parameter  
determines the type of toast shown.  
  
Type can be one of: "error", "warning", "success" or "info" (default).  
  
```lua  
add_message("I'm Blue")  
add_message("I'm Red", "error")  
add_message("I'm Orange", "warning")  
add_message("I'm Green", "success")  
```
### clear_messages

`clear_messages(String message, [String type="info"]): void`  
  
Clears all messages that have been previously added with [`add_message`](#add_message).  
  
```lua  
add_message("Don't show this...")  
add_message("...or this")  
  
clear_messages()  
  
add_message("Show this")  
add_message("...and this")  
```
### log

`log(String message, [Int level=1], [Table context={}]): void`  
  
Logs a message to the Nextcloud log.  
  
You may optionally specify a [log level](https://docs.nextcloud.com/server/latest/admin_manual/configuration_server/logging_configuration.html#log-level) (defaults to 1).  
You may append some context to the log by passing a table containing the relevant data.
### view_files

`view_files(Node[] nodes): void`  
  
Sets a list of files to be viewed after execution.
## Pdf
### pdf_decrypt

`pdf_decrypt(Node file, [String password]=nil, [String new_file_name]=nil): Node|nil`  
  
Removes protections from the PDF file. If `new_file_name` is specified a new file is created, otherwise the existing file gets overwritten.  
  
Returns the node object for the resulting file.
### pdf_merge

`pdf_merge(Node[] files, Node folder, [String new_file_name]=nil): Node|nil`  
  
Merges any PDF documents in the given `files` array. The output file is saved to the specified folder.  
The output's file name can be specified, if not specified the name `{timestamp}_merged.pdf` is used.  
  
The output file's node is returned, or `nil` if operation failed.
### pdf_overlay

`pdf_overlay(Node target, Node overlay, [String new_file_name]=null, [Bool repeat]=true): Node`  
  
Overlays the `overlay` PDF document onto the `target` PDF file. The overlay happens sequentially: page 1 of `overlay` gets rendered over page 1 of `target`, page 2 over page 2...  
By default, the overlay repeats (after we run out of overlay pages we start again from page 1), this can be changed by setting the `repeat` parameter to `false`.  
  
A new file can be created by specifying the `new_file_name` parameter (the file will be created on the target file's folder). By default, the target file gets overwritten.  
  
Returns the node object of the resulting file.
### pdf_page_count

`pdf_page_count(Node node): Int`  
  
Returns the number of pages in the PDF document.  
If the document is not a valid PDF document, -1 is returned.
### pdf_pages

`pdf_pages(Node file, String page_range, [String new_file_name]=nil): Node|nil`  
  
Creates a new PDF only containing the specified pages. Page range parameter allows multiple formats see [qpdf documentation](https://qpdf.readthedocs.io/en/stable/cli.html#page-ranges).  
  
Returns the output file's node object, or `nil` if operation failed.
## Template
### html_to_pdf

~~`html_to_pdf(String html, [Table config]={}, [Table position]={}): string|nil`~~  
  
This function has been removed. The dependency that makes this function work is excessively large, and it is unnecessary to package it with the `file_scripts` app.  
  
You can continue using the function by manually installing the [`files_scripts_deprecated`](https://github.com/Raudius/files_scripts_deprecated) app, which bundles all the removed functions.
### mustache

`mustache(String template, [Table variables]={}): String`  
  
Renders a [Mustache](https://mustache.github.io) template.  
Returns the resulting string.
## Util
### create_date_time

`create_date_time(Number year, [Number month], [Number day], [Number hour], [Number minute], [Number second]): Date`  
  
Creates a `Date` object containing date and time information. If no values are specified the current date-time is returned.  
  
The `Date` object is a Lua table containing the following values:  
```lua  
date = {  
  year= 2022,  
  month= 06,  
  day= 25,  
  hour= 16,  
  minute= 48,  
  second= 27  
}  
```
### csv_to_table

`csv_to_table(Node input, String separator=',', String enclosure='"'): Table`  
  
Creates a table from a CSV-formatted file.  
Optionally field separator and enclosure characters may be specified.
### for_each

`for_each(Table items, Function function): Table`  
  
Calls the function on each key/item pair.  
Note that inside the function only global values can be accessed.  
  
```lua  
bits = {"to", "be", "or", "not", "to", "be"}  
sentence = ""  
  
for_each(  
  bits,  
  function (key, value)  
    sentence = sentence .. value .. " "  
  end  
)  
```
### format_date_time

`format_date_time(Date date, [String locale], [String timezone], [String pattern]): String`  
  
Returns a formatted date string.  
  
  - **Date:** See [create_date_time](#create_date_time)  
  - **Locale:** A valid CLDR locale (if nil, the default PHP local will be used).  
  - **Timezone:** A string containing any value in the ICU timezone database, or any offset of "GMT" (e.g `GMT-05:30`)  
  - **Pattern:** A string containing an [ICU-valid pattern](https://unicode-org.github.io/icu/userguide/format_parse/datetime/#datetime-format-syntax).
### format_price

`format_price(Number value, [String symbol], [String currency], [String locale]): String`  
  
Formats a number to a formatted string. The symbol, currency and locale can be specified for more precise formatting.  
By default, locale is set to `en`, and no symbol/currency are specified.  
  
**Symbol:** any string is allowed. It is be used as the currency symbol in the output string  
**Currency:** a string containing a valid ISO 4217 currency code. It is used for calculating currency subdivisions (cents, pennies, etc.)  
**Locale:** a string containing a valid CLDR locale. It is used for formatting in a locale specific way (e.g. symbol before or after value)
### http_request

`http_get(String url, [String method]='GET', [Table data]={}): String`  
  
Performs an HTTP request to the given URL using the given method and data.  
Returns the response. If the content could not be fetched, `nil` is returned.  
  
**Note:** Be wary of sending any personal information using this function! Only to be used for fetching templates or other static data.
### include

`include(Node|string lua_file): Bool`  
  
Loads the given Lua file into the global environment. Can be used to load common functions and variables, effectively extending the scripting API.  
Scripting API functions are loaded and available to be used inside the included file.  
  
```lua  
-- Load file from a file on the server OR load from a Node object  
success = include("/var/www/private/my_api.lua")  
success = include(get_input_files()[1])  
if (not success) then  
  abort("Failed to load required script")  
end  
  
-- Globally defined functions in the included file are now available in this script :)  
my_api_function()  
```
### json

`json(Table|string input): String|Table|nil`  
  
If the input is a string, returns a Table of the JSON represented in the string.  
If the input is a table, returns the JSON representation of that object.  
If encoding/decoding fails, `nil` is returned.
### shell_command

`shell_command(String command): void`  
  
Issues the given command to the linux shell. Returns a table with the result, the table contains the following indices:  
  - `exit_code`  
  - `output`  
  - `errors`
### sort

`sort(Table items, [String key]=nil, [Bool ascending]=true): Table`  
  
Sorts a Lua table and returns the result.  
If the argument `key` is returned it will sort the elements using that key (see example below).  
If the `ascending` argument is set to `false`, the ordering will be reversed (largest first).  
  
This function uses [PHP's](https://www.php.net/manual/en/types.comparisons.php) default type comparison  
This function may be slightly more convenient than Lua's own: [table.sort](https://www.lua.org/manual/5.3/manual.html#pdf-table.sort), such as in cases where you need the ascending/descending parameter.  
  
**Note:** if you input an associative Table, the keys will be removed in the process.  
  
Example:  
```lua  
fruits = {"grape", "apple", "banana", "orange"}  
fruits = sort(fruits)  
-- {"apple", "banana", "grape", "orange"}  
  
fruits = {{name="grape"}, {name="apple"}, {name="banana"}, {name="orange"}}  
fruits = sort(fruits, "name", true)  
-- {{name="apple"}, {name="banana"},{name="grape"},{name="orange"}}  
```
### wait

`wait(Number seconds): void`  
  
Halts the execution for the specified amount of time (in seconds), rounded to the closest second.
