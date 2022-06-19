  - **[Input:](#Input)** Retreiving user inputs
    - [get_input](#get_input)  
    - [get_target_folder](#get_target_folder)  
    - [get_input_files](#get_input_files)  

  - **[Util:](#Util)** Utility functions for scripting convenience
    - [json](#json)  
    - [sort_by](#sort_by)  
    - [http_request](#http_request)  

  - **[Template:](#Template)** Generate files from templates
    - [mustache](#mustache)  
    - [html_to_pdf](#html_to_pdf)  

  - **[Error:](#Error)** Reporting and logging
    - [abort](#abort)  

  - **[Pdf:](#Pdf)** Modify PDFs (requires qpdf server package)
    - [pdf_merge](#pdf_merge)  
    - [pdf_overlay](#pdf_overlay)  
    - [pdf_decrypt](#pdf_decrypt)  

  - **[Files:](#Files)** File operations within the Nextcloud environment
    - [meta_data](#meta_data)  
    - [new_file](#new_file)  
    - [file_content](#file_content)  
    - [is_file](#is_file)  
    - [copy_file](#copy_file)  
    - [is_folder](#is_folder)  
    - [full_path](#full_path)  
    - [directory_listing](#directory_listing)  
    - [root](#root)  
    - [exists](#exists)  
    - [get_parent](#get_parent)  

## Input
### get_input

`get_input(): Table`  
  
Returns a Lua table containing the user inputs.
### get_target_folder

`get_target_folder(): Node|nil`  
  
Returns the target directory node. If none is provided, returns nil.
### get_input_files

`get_input_files(): Node[]`  
  
Returns a list of the selected files: these are the files the user selects before running the action.
## Util
### json

`json(Table table): String`  
  
Returns the json encoding of the given table. If the encoding fails, `nil` is returned.
### sort_by

`sort_by(Table items, String key, [Bool ascending]=true): Table`  
  
Sorts a Lua table by an attribute. This is only meant for "arrays" (tables with sequential integer keys) and not for "maps" because Lua cannot guarantee sorting for associative Tables.  
  
Example:  
```  
fruits = {{name="grape"}, {name="apple"}, {name="banana"}, {name="orange"}}  
fruits = sort_by(fruits, "name", true)  
-- {{name="apple"}, {name="banana"},{name="grape"},{name="orange"}}  
```
### http_request

`http_get(String url, [String method]='GET', [Table data]={}): String`  
  
Performs an HTTP request to the given URL using the given method and data.  
Returns the response. If the content could not be fetched, `nil` is returned.
## Template
### mustache

`mustache(String template, [Table variables]={}): String`  
  
Renders a [Mustache](https://mustache.github.io) template.  
Returns the resulting string.
### html_to_pdf

`html_to_pdf(String html, [Table config]={}, [Table position]={}): string|nil`  
  
Renders the HTML onto a PDF file.  
  
A configuration table can be passed to configure various aspects of PDF generation. For more information see the [MPDF documentation](https://mpdf.github.io/reference/mpdf-variables/overview.html).  
The position (x, y, w, h) of where to render the HTML onto the page can also be provided. For more information see the [MPDF documentation](https://mpdf.github.io/reference/mpdf-functions/writefixedposhtml.html)  
  
Returns the PDF as a string (or `nil` if PDF generation failed).
## Error
### abort

`abort(String message): void`  
  
Aborts execution with an error message. This error message will be shown to the user in a toast dialog.
## Pdf
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
### pdf_decrypt

`pdf_decrypt(Node file, [String password]=nil, [String new_file_name]=nil): Node|nil`  
  
Removes protections from the PDF file. If `new_file_name` is specified a new file is created, otherwise the existing file gets overwritten.  
  
Returns the node object for the resulting file.
## Files
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
### new_file

`new_file(Node folder, String name, [String content]=nil): Node|nil`  
  
Creates a new file at specified folder.  
If successful, returns the newly created file node. If file creation fails, returns `nil`.
### file_content

`file_content(Node node): String|nil`  
  
Returns the string content of the file. If the node is a directory or the file does not exist, `nil` is returned.
### is_file

`is_file(Node node): Bool`  
  
Returns whether the given node is a file.
### copy_file

`copy_file(Node file, String folder_path, [String name]=nil): Bool`  
  
Copies the given `file` to the specified `folder_path`.  
Optionally a new name can be specified for the file, if none is specified the original name is used.  
  
If the target file already exists, the operation will not succeed.  
  
Returns whether the operation was successful.
### is_folder

`is_folder(Node node): Bool`  
  
Returns whether the given node is a folder.
### full_path

`full_path(Node node): String|nil`  
  
Returns the full path of the given file or directory including the node's name.  
*Example:* for a file `abc.txt` in directory `/path/to/file` the full path is: `/path/to/file/abc.txt`.  
  
If the file does not exist `nil` is returned.
### directory_listing

`directory_listing(Node folder): Node[]`  
  
Returns a list of the directory contents, if the given node is not a folder, returns an empty list.
### root

`root(): Node`  
  
Returns the node object for the user's root directory.
### exists

`exists(Node node, [String file_name]=nil): Bool`  
  
Returns whether a file or directory exists.  
Optionally the name of a file can be specified as a second argument, in which case the first argument will be  
assumed to be directory. The function will return whether the file exists in the directory.
### get_parent

`get_parent(Node node): Node`  
  
Returns the parent folder for the given file or directory.  
The root of the "filesystem" is considered to be the home directory of the user who is running the script. When attempting to get the parent of the root directory, the root directory is returned.  
  
If the given file cannot be found, `nil` is returned.
