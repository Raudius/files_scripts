--- Generates an invoice from a JSON file containing the order data
--- Sample JSON:
--[[
{
	"invoice_number": "310887",
	"vat_percent": 24,
	"customer_details": [
		{ "key": "Name", "value": "Jane Doe" },
		{ "key": "Phone", "value": "+01 345 678 901" },
		{ "key": "Address", "value": "42 Main road - 95531" }
	],
	"items": [
		{ "item": "Item 1", "price": 34.99 },
		{ "item": "Item 2", "price": 159.99 },
		{ "item": "Item 3", "price": 0.99 }
	]
}
--]]


-----------------------------
--- Init global variables ---
target_folder = get_input("target_folder")
today = create_date_time()

html = http_request("https://raw.githubusercontent.com/Raudius/files_scripts/master/examples/invoice.mustache")
if (not html or string.len(html) < 1) then
	abort("Could not fetch the invoice template.")
end

----------------------
--- Formats a date ---
function fdate(date, fmt)
	return format_date_time(date, "en_GB", null, fmt)
end

--------------------------------------------
--- Formats a value as a localised price ---
function fprice(v)
	return format_price(v, "€", "EUR", "en_GB")
end

----------------------------------------------------
--- Generates an invoice using the provided data ---
function generate_invoice(data)
	-- Set invoice date
	data.invoice_date = fdate(today, "dd MMMM yyyy")

	-- Total calculation
	local rows_formatted = {}
	local total_rows = 0
	for k,v in pairs(data.items) do
		total_rows = total_rows + v.price
		rows_formatted[k]= { item= v.item, price= fprice(v.price) }
	end

	-- VAT calculation
	local vat_percent = (data.vat_percent or 0) / 100
	local vat = total_rows * vat_percent

	-- Price formatting
	data.total_rows = fprice(total_rows)
	data.vat = fprice(vat)
	data.total = fprice(total_rows + vat)
	data.rows = rows_formatted

	-- Generate and save
	local name = fdate(today, "yyyyMM") .. " Invoice " .. data.invoice_number .. ".pdf"
	local invoice_pdf = html_to_pdf(mustache(html, data))
	new_file(target_folder, name, invoice_pdf)
end

-------------------------------------------------
--- Generates the invoice for each input file ---
function generate_invoices()
	for k,v in pairs(get_input_files()) do
		local in_data = json(file_content(v))
		if (in_data and type(in_data) == "table") then
			generate_invoice(in_data)
		else
			abort("Could not read: " .. v.name)
		end
	end
end

-- Run
generate_invoices()
