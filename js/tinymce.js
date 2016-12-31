tinymce.init({
	browser_spellcheck:true,
	content_css:"/css/tinymce.css",
	menubar:false,
	min_height:200,
	plugins:"link,paste,code",
	paste_auto_cleanup_on_paste:true,
	selector:".tinymce",
	statusbar:false,
	style_formats:[
		{title: "Headers",items:[
			{title:"Header 2",format:"h2"},
			{title:"Header 3",format:"h3"},
			{title:"Header 4",format:"h4"},
			{title:"Header 5",format:"h5"},
			{title:"Header 6",format:"h6"}
		]},
		{title:"Inline",items:[
			{title:"Underline",icon:"underline",format:"underline"},
			{title:"Strikethrough",icon:"strikethrough",format:"strikethrough"},
			{title:"Superscript",icon:"superscript",format:"superscript"},
			{title:"Subscript",icon:"subscript",format:"subscript"}
		]},
		{title:"Blocks",items:[
			{title:"Paragraph",format:"p"},
			{title:"Blockquote",format:"blockquote"}
		]},
		{title:"Alignment",items:[
			{title:"Left",icon:"alignleft",format:"alignleft"},
			{title:"Center",icon:"aligncenter",format:"aligncenter"},
			{title:"Right",icon:"alignright",format:"alignright"},
			{title:"Justify",icon:"alignjustify",format:"alignjustify"}
		]}
	],
	toolbar1:"undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link code",
});