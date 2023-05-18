--[[
||||
|||| This test checks the nextcloud files functions
||||
]]--
log("Running test_files.lua")

-- Delete all files in home
home_files = directory_listing(home())
for _, node in ipairs(home_files) do
  success = file_delete(node)

  assertTrue(
    success,
    "Failed to delete file: " .. node.path .. "/" .. node.name
  )
end


-- Verify there are no files in home
assertEmpty(directory_listing(home()), "Expected no files in home")

-- Create hello_world file, check it exists
assertFalse(exists(home(), "hello_world.txt"))
hello_world = new_file(home(), "hello_world.txt", "Hello, World!")
assertTrue(exists(home(), "hello_world.txt"))

-- Check hello world contains expected contents
assertEquals(file_content(hello_world), "Hello, World!")

-- Assert file is file and not folder
assertTrue(is_file(hello_world))
assertFalse(is_folder(hello_world))

-- Copy file to new location and verify contents are the same
assertFalse(exists(home(), "hello_world_copy.txt"))
hello_world_copy = file_copy(hello_world, full_path(home()), "hello_world_copy.txt")
assertTrue(exists(home(), "hello_world_copy.txt"))
assertEquals(file_content(hello_world_copy), "Hello, World!")

-- Create new folder
test_folder = new_folder(home(), "test_folder")
assertNotNil(test_folder)

assertTrue(is_folder(test_folder))
assertFalse(is_file(test_folder))

-- Verify parent of new folder is home()
test_folder_parent = get_parent(test_folder)
assertSameNode(test_folder_parent, home())

-- Move file to new folder
assertFalse(exists(test_folder_parent, "moved_file.txt"))

moved_file = file_move(hello_world_copy, full_path(test_folder), "moved_file.txt")
assertNotNil(moved_file)
assertFalse(exists(hello_world_copy))
assertEquals(file_content(moved_file), "Hello, World!")

assertTrue(exists(test_folder, "moved_file.txt"))


-- Test meta_data
meta_hello_world = meta_data(hello_world)
assertSameNode(hello_world, meta_hello_world)

assertNotNil(meta_hello_world["size"])
assertNotNil(meta_hello_world["mimetype"])
assertNotNil(meta_hello_world["etag"])
assertNotNil(meta_hello_world["utime"])
assertNotNil(meta_hello_world["can_read"])
assertNotNil(meta_hello_world["can_delete"])
assertNotNil(meta_hello_world["can_update"])
assertNotNil(meta_hello_world["local_path"])
assertNotNil(meta_hello_world["owner_id"])
