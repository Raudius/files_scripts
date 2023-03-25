--- Merges any selected PDF files to a single file in the selected location and with the provided name.
--- PDFs are sorted by name in ascending order before they are merged.
--- Non-PDF files will get ignored.
---
--- NOTE: This command requires `qpdf` to be installed on the server.
--- https://github.com/qpdf/qpdf

local dir = get_input("output_location")
local name = get_input("file_name")
local files = get_input_files()

if (name == nil or name == "") then
	abort("No file name was provided")
end

if (string.find(name, ".pdf") == nil) then
	name = name .. ".pdf"
end

if (exists(dir, name)) then
	abort("A file already exists in the chosen location")
end

files = sort(files, "name")

pdf_merge(files, dir, name)
