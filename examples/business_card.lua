--- Creates a simple business card PDF from a Mustache HTML template.
--- The PDF is saved to the user's home directory with the name "business-card.pdf"
---
--- This action does not currently do anything with the selected files, ideally either the
--- html, or the vars would get loaded from a file:
---
--- html = file_content(get_input_files()[1])
--- vars = json(file_content(get_input_files()[1]))

vars = get_input()
html = [[
<div style="box-shadow: .5mm 1mm 2mm .5mm rgba(0,0,0,0.2); width: 90mm; height: 50mm;">
	<div style="height: 16mm; text-align: center; font-size: 8mm; line-height: 15mm;">
		<b>{{ company_name }}<b>
	</div>
	<div style="height: 28mm; text-align: center; font-size: 3mm; line-height: 4.5mm;">
        <div><b>{{ name }}</b></div>
        <div><i>{{ title }}</i></div>
        <div><br/></div>
        <div>{{ phone }}</div>
        <div >{{ email }}</div>
	</div>
	<div style="text-align: center; font-size: 3mm;">{{ website }}</div>
</div>
]]

card = mustache(html, vars)
content = html_to_pdf(card)

if( exists(home(), "business-card.pdf") )then
	abort("File 'business-card.pdf' already exists in your home folder.")
end

new_file(home(), "business-card.pdf", content)
