# Changelog

## 2.0.4

### Changed
- Added `new_folder` and `get_file_tags`
- Updated `meta_data` function with filepath and owner info


## 2.0.3

### Fixes
- Fixed composer issue

## 2.0.2

### Fixes
-Updated PHP interpreter dependency

## 2.0.1

### Changed
- Separated stable24 branch which bring maintenance fixes (`file_scripts 2.0.x`) for Nextcloud 23, 24
- Updated PHP interpreter dependency

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
