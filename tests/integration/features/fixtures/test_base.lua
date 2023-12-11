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

function assertEqualTable(o1, o2, message)
	if o1 == o2 then return end
	local o1Type = type(o1)
	local o2Type = type(o2)

	assertEquals(o1Type, o2Type, message)
	assertEquals(o1Type, "table", message)

	local keySet = {}
	for key1, value1 in pairs(o1) do
		local value2 = o2[key1]
		assertNotNil(value2, message)
		assertEqualTable(value1, value2, message)
		keySet[key1] = true
	end

	for key2, _ in pairs(o2) do
		assertTrue(keySet[key2], message)
	end
	return true
end
