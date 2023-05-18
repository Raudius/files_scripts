--[[
||||
|||| Tests "Nextcloud" functions (tags, comments, etc.)
||||
]]--
log("Running test_nextcloud.lua")

-- Create a sample file to be used in the tests
sample_file = new_file(home(), "sample_file.txt")
assertNotNil(sample_file)
assertTrue(exists(sample_file))


-- Test commenting
comments = comments_find({ node= sample_file })
assertEmpty(comments)

test_comment = comment_create("test comment...", sample_file)
assertNotNil(test_comment)

comments = comments_find({ node= sample_file })
assertNotEmpty(comments)

delete_success = comment_delete(test_comment)
assertTrue(delete_success)

comments = comments_find({ node= sample_file })
assertEmpty(comments)


-- Test tag creation
tags = tags_find()
assertEmpty(comments) -- tags should get cleared before tests start

test_tag = tag_create("test_tag")
assertNotNil(test_tag, "test_tag creation failed")

tags = tags_find()
assertEmpty(comments) -- tags should get cleared before tests start


-- Test file tagging
file_tags = get_file_tags(sample_file)
assertEmpty(file_tags)

tag_file(sample_file, test_tag)

file_tags = get_file_tags(sample_file)
assertNotEmpty(file_tags)


-- Test file untagging
unassign_success = tag_file_unassign(sample_file, test_tag)
assertTrue(unassign_success, "failed to remove tag")

file_tags = get_file_tags(sample_file)
assertEmpty(file_tags)


-- Test user search
current_user_search = users_find()
assertNotEmpty(current_user_search)

current_user = current_user_search[1]
assertEquals(current_user.uuid, "admin") -- expect admin to run this file

user_alice_search = users_find("alice")
assertNotEmpty(user_alice_search, "failed to find user alice")

user_alice = user_alice_search[1]
assertEquals(user_alice.uuid, "alice")


-- Test user notify (no assertions possible, but can test for no-exceptions)
notify(user_alice, "Hello, Alice!")
