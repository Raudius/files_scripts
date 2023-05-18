--[[
||||
|||| Test utility functions
||||
]]--
log("Running test_util.lua")

-- Test dates
date = create_date_time(2020, 01, 02, 03, 04, 05)
assertEquals(date.year, 2020)
assertEquals(date.month, 1)
assertEquals(date.day, 2)
assertEquals(date.hour, 3)
assertEquals(date.minute, 4)
assertEquals(date.second, 5)

formatted_date = format_date_time(date, "en_GB", nil, "dd MMMM yyyy")
assertEquals(formatted_date, "02 January 2020")

formatted_time = format_date_time(date, "en_GB", nil, "KK:mm")
assertEquals(formatted_time, "03:04")

seconds = format_date_time(date, "en_GB", nil, "ss")
assertEquals(seconds, "05")

-- Test for_each
t = {0, 0, 0, 0, 0, 0}
count = 0
for_each(
  t,
  function (_, value)
    assertEquals(value, 0)
    count = count + 1
  end
)
assertEquals(count, #t)

-- CSV to table

csv = [[Username;Identifier;First name;Last name
booker12;9012;Rachel;Booker
grey07;2070;Laura;Grey
johnson81;4081;Craig;Johnson
jenkins46;9346;Mary;Jenkins
smith79;5079;Jamie;Smith]]

csv_file = new_file(home(), "test.csv", csv)

csv_table = csv_to_table(csv_file, ";", "")
assertEquals(csv_table[2][1], "booker12")
assertEquals(csv_table[6][4], "Smith")


-- JSON to Table
aryaJsonString = [[{
  "name": "Arya",
  "surname": "Stark",
  "city": "Winterfell",
  "age": 11
}]]

arya = json(aryaJsonString)
assertEquals(arya.name, "Arya")
assertEquals(arya.surname, "Stark")
assertEquals(arya.city, "Winterfell")
assertEquals(arya.age, 11)

-- Table to JSON

sansa = {
  name= "Sansa",
  surname= "Stark",
  city= "Winterfell",
  age= 13
}

sansaJsonString = json(sansa)

-- remove whitespaces, and check for each line separately (JSON-object encoding not necessarily in consistent order)
sansaJsonString = sansaJsonString:gsub("%s", "")

assertNotNil(sansaJsonString:find('"name":"Sansa"'))
assertNotNil(sansaJsonString:find('"surname":"Stark"'))
assertNotNil(sansaJsonString:find('"city":"Winterfell"'))
assertNotNil(sansaJsonString:find('"age":13'))
