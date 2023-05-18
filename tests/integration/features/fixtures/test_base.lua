--[[
||||
|||| This file contains common functions to be shared by all tests, should be included on all scripts.
||||
]]--

INFO = "info"
ERROR = "error"
WARNING = "warning"
SUCCESS = "success"

function assertTrue(value, message)
  message = message or "Assertion failed"
  if (value ~= true) then
    abort(message)
  end
end

function assertFalse(value, message)
  assertTrue(value == false, message)
end

function assertNil(value, message)
  assertTrue(value == nil, message)
end

function assertNotNil(value, message)
  assertTrue(value ~= nil, message)
end

function assertNotEmpty(value, message)
  assertTrue(#value > 0, message)
end

function assertEmpty(value, message)
  assertTrue(#value == 0, message)
end

function assertEquals(value1, value2, message)
  assertTrue(value1 == value2, message)
end

function assertSameNode(node1, node2, message)
  local success = true
  if (type(node1) ~= "table" or type(node2) ~= "table") then
    success = false
  end

  if (
    node1.id ~= node2.id
    or node1.name ~= node2.name
    or node1.path ~= node2.path
  ) then
    success = false
  end

  assertTrue(success, message)
end
