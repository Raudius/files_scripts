--[[
||||
|||| Tests return messages
||||
]]--
log("Running test_messages.lua")

add_message("message 1", INFO)
add_message("message 2", ERROR)
add_message("message 3", WARNING)
add_message("message 4", SUCCESS)

clear_messages()

add_message("Uh-oh, something is wrong :(", ERROR)
add_message("Working on a fix!", INFO)
add_message("This might take a while...", WARNING)
add_message("Fixed :D", SUCCESS)
