# Changelog

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
