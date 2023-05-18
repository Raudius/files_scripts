log("Running test_abort_with_messages.lua")

add_message("message 1", INFO)
add_message("message 2", ERROR)
add_message("message 3", WARNING)
add_message("message 4", SUCCESS)

a = 1
b = 2

if (a < b) then
  abort("Basic arithmetic is not broken yet.")
end
