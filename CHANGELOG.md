# Changelog

## 3.0.0

## Added
- ⚠️ Deprecated functions removed from Scripting API!
- Nextcloud 27 support
- Added `view_files` function
- Added sharing functions to API
- Added `Ctrl+S` keyboard shortcut to save scripts during editing
- Added integration event `RegisterScriptFunctionsEvent` to add API functions from other apps
- Scripts can now optionally be enabled to work on public shares
- Actions can now be limited by mimetype
- New selection component for setting filepick mimetypes and multiselect options
- Expanded some script input types with additional options

## Fixed
- Documentation link in settings now points to installed version docs (not master docs)
- Fixed checkbox input behaviour
- Removed deprecated function usages from example scripts

## Deprecated
- Deprecated `html_to_pdf` function

## 2.2.1

## Changed
- Fixed some issues with new NcSelect component

## 2.2.0

### Added
- Nextcloud 26 support
- Custom messages with `add_message` and `clear_messages`
- Limit actions to certain user-groups
- Added `include` function to Scripting API

### Changed
- Fix example scripts not getting inputs
- Fix incomplete log messages
- Fixed occ commands not correctly parsing input
- Fixed multi-select input not allowing selection
- Fixed file-pick input default and start-browse location
- `tag_file_unassign` now works on folders
- `file_copy`/`file_move` now work on folders
- Fixed `meta_data` crashing scripts on externally mounted folders
- Updated multi-select Vue component

## 2.1.1
### Added
- Added function `get_file_tags` @0xFaul
- Setting option to register file actions directly to the menu @vitstradal
- Added filepath and owner info to `meta_data` function
- Added `new_folder` function

## 2.1.0
### Added
- Different user-input types
- occ commands `files_scripts:list`, `files_scripts:run`

### Changed
- Updated to new nextcloud Vue components
- Fixed theming and visual bugs
- Updated interpreter dependency

### Deprecated
- Target folder (file-picker input should be used instead)

## 2.0.0

### Added
- PHP-based Lua interpreter

### Fixed
- `tags_find` no longer returns an non-sequential table

## 1.3.0

### Added
- Added support for Nextcloud 25
- Added function `comment_create`
- Added function `comment_delete`
- Added function `comments_find`

### Fixed
- Fixed tag functions


## 1.2.0

### Added

- Added function `file_copy_unsafe`
- Added function `file_move_unsafe`
- Added function `exists_unsafe`
- Added FFmpeg functions `ffmpeg`, `ffprobe`
- Added function `csv_to_table`
- Added tagging functions `tag_create`, `tag_file`, `tag_file_unassign`, `tags_find`
- Added function `shell_command`
- Added function `users_find` 
- Added function `notify`

### Fixed
- Removed unnecessary logging

### Deprecated
- `root()` is deprecated in favour of `home()`
- `copy_file()` is deprecated in favour of `file_copy()`


## 1.1.0

### Added

- Flow support
- Added function `pdf_linearize`
- Added function `file_move`
- Added function `file_unlock`
- Added function `log`

### Fixed
- Updated PDF dependency to fix merge issue with faulty PDF files


## 1.0.0

### Added

- First release!
- Create Lua scripts
- Allow for user inputs
- Allow for user selected directory
- Enable/disable scripts
- Run scripts from the Files app
