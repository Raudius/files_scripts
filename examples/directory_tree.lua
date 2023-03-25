--- Crawls through the selected folders/files and crates a directory tree.
---
--- The directory tree gets saved to the directory from which the files were
--- selected, with the name "tree.txt".

function listing(nodes, separator)
  separator = separator or ""
  local files = ""
  local folders = ""

  for i, node in ipairs(nodes) do
  	if( is_file(node) )then
      files = files .. separator .. "-" .. node.name .. "\n"
    elseif( is_folder(node) )then
      local list = listing(directory_listing(node), separator .. "\t")
      folders = folders .. separator .. "-[" .. node.name .. "]\n" .. list
    end
  end

  return folders .. files
end

local list = listing(get_input_files(), "")
local out_folder = get_parent(get_input_files()[1])

if( exists(out_folder, "tree.txt") )then
	abort("The file 'tree.txt' already exists in this folder.")
end

new_file(out_folder, "tree.txt", list)
