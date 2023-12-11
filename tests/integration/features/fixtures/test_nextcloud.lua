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



-- Check permission constants exists
assertNotNil(PERMISSION_ALL, 'Constant not set: PERMISSION_ALL')
assertNotNil(PERMISSION_READ, 'Constant not set: PERMISSION_READ')
assertNotNil(PERMISSION_CREATE, 'Constant not set: PERMISSION_CREATE')
assertNotNil(PERMISSION_DELETE, 'Constant not set: PERMISSION_DELETE')
assertNotNil(PERMISSION_UPDATE, 'Constant not set: PERMISSION_UPDATE')
assertNotNil(PERMISSION_SHARE, 'Constant not set: PERMISSION_SHARE')

-- Check that PERMISSION_ALL is equal to read + create + delete + share + update
all_permissions = PERMISSION_READ | PERMISSION_CREATE | PERMISSION_DELETE | PERMISSION_SHARE | PERMISSION_UPDATE
assertEquals(PERMISSION_ALL, all_permissions, "Bitwise add all permissions does not match PERMISSION_ALL " .. tostring(all_permissions) .. " vs " .. tostring(PERMISSION_ALL))

-- Check share type constants exist
assertNotNil(SHARE_TYPE_USER, "Constant not set: SHARE_TYPE_USER")
assertNotNil(SHARE_TYPE_GROUP, "Constant not set: SHARE_TYPE_GROUP")
assertNotNil(SHARE_TYPE_LINK, "Constant not set: SHARE_TYPE_LINK")
assertNotNil(SHARE_TYPE_REMOTE, "Constant not set: SHARE_TYPE_REMOTE")
assertNotNil(SHARE_TYPE_EMAIL, "Constant not set: SHARE_TYPE_EMAIL")
assertNotNil(SHARE_TYPE_ROOM, "Constant not set: SHARE_TYPE_ROOM")
assertNotNil(SHARE_TYPE_CIRCLE, "Constant not set: SHARE_TYPE_CIRCLE")
assertNotNil(SHARE_TYPE_DECK, "Constant not set: SHARE_TYPE_DECK")

-- Check share target constants exists
assertNotNil(SHARE_TARGET_LINK, "Constant not set: SHARE_TARGET_LINK")

-- Test share listing (expect no shares yet)
found_shares = shares_find(sample_file)
assertEmpty(found_shares, "sample file has shares before any were created")

-- Create share on sample file
share = share_file(sample_file, {
	target= user_alice
})
assertEquals(share["_type"], "share")

-- Search for shares again
found_shares = shares_find(sample_file)
assertNotEmpty(found_shares, "no shares found after share creation")
found_share = found_shares[1]
assertEquals(json(found_share), json(share), "created-share and found-share not equal")

-- Delete share
success = share_delete(found_share)
assertTrue(success, "failed to delete the share")
found_shares = shares_find(sample_file)
assertEmpty(found_shares, "sample file has shares after share was deleted")

-- Create a public link share
share = share_file(sample_file, {
	target= SHARE_TARGET_LINK,
	expiration= create_date_time(2025, 06, 07), -- 7th June 2025
	password= "hunter2",
	token= "makes-url-pretty"
})
assertNotNil(share)

-- Check share exists and is correct
found_shares = shares_find(sample_file)
assertNotEmpty(found_shares, "no shares found after share creation")
found_share = found_shares[1]
assertEquals(json(found_share), json(share), "created-share and found-share not equal (link share)")



--
-- Test get_activity
--

local file = new_file(home(), "activity_foo.txt")
local activity = get_activity(file)

assertEquals(#activity, 1, "Newly created file should have one activity event (creation)")

created_event = activity[1]
assertEquals(created_event["_type"], "event", "get_activity() should always return event objects")
assertEquals(created_event["type"], "file_created", "Newly created file should have the `file_created` event")


file = file_move(file, nil, "activity_bar.txt")
activity = get_activity(file)

assertEquals(#activity, 2, "After moving the file it should have 2 events (creation, modification)")
assertEquals(activity[1]["_type"], "event")
assertEquals(activity[2]["_type"], "event")

-- First event should be "latest", file changed
assertEquals(activity[1]["type"], "file_changed", "After moving a file, its first event should be `file_changed`")

-- Second event should be the same as the previous created event
assertEqualTable(activity[2], created_event, "Moved file should still have the creation event")
