/* Author: 

*/

function replaceAll(txt, replace, with_this) {
	return txt.replace(new RegExp(replace, 'g'),with_this);
}

var X = window.X || {};

X.page_load_complete = window.X.page_load_complete || false;

X.PageLoadStatus = {
	requirements:{'login':false},
	ready: function(requirement){
		switch(requirement){
			case 'login': this.requirements['login']=true; break;
		}
		if(X.PageLoadStatus.requirements['login']===true) { X.page_load_complete = true; }
	}
};

X.CachedData = {};

var IE = '\v'=='v';

$.fn.wait = function(time, type) {
	time = time || 1000;
	type = type || "fx";
	return this.queue(type, function() {
		var self = this;
		setTimeout(function() {
			$(self).dequeue();
		}, time);
	});
};

X.Config = {
	x:640, // site width
	y:480, // site height
	print_uri:'',
	max_img_size:'400',
	has_run:0,
	domain:'', // if permission is given (see http://www.mozilla.org/projects/security/components/signed-scripts.html) this can be changed to a remote host
	api_version_url:'/api/1/0/',
	init: function(){
		if(screen != null) {
			X.Config.x = screen.width;
			X.Config.y = screen.height;
		}
	}
};

X.Load = {
	scripts:[],
	style_sheets:[],
	pages:[],
	js: function(url, load_once){
		X.Load.scripts[X.Load.scripts.length] = url;
		$('#javascripts').append('<script src="/js/'+url+'.js"></script>');
		return true;
	},
	css: function(url, load_once){
		X.Load.style_sheets[X.Load.style_sheets.length] = url;
		$('head').append('<link rel="stylesheet" type="text/css" href="/css/'+url+'.css">');
		return true;
	},
	page: function(url){
		this.pages[this.pages.length] = url;
		location.href = X.Config.domain+'/'+url;
		$.ajax({
			type:		'GET',
			async:		true,
			dataType:	'json',
			url:		X.Config.domain+'/'+url+';format=json',
			data:		post_data,
			success:	function (json_data) {
				if(typeof json_data.js != 'undefined') {
					$.each(json_data.js, function(i,n){
						if($.inArray(n,X.Load.scripts)){ X.Load.js(n); }
					});
				}
				if(typeof json_data.css != 'undefined') {
					$.each(json_data.css, function(i,n){ X.Load.css(n); });
				}
			}
		});
	}
};

X.Cookie = {
	// simplified JS cookie creator
	create: function(name,value,days) {
		if(days) {
			var date = new Date();
			date.setTime(date.getTime()+(days*24*60*60*1000));
			var expires = '; expires='+date.toGMTString();
		} else { expires = ''; }
		document.cookie = name+'='+value+expires+'; path=\/';
	},
	// simplified JS cookie reader
	read: function(name) {
		var nameEQ = name + '=';
		var ca = document.cookie.split(';');
		for(var i=0;i < ca.length;i++) {
			var c = ca[i];
			while(c.charAt(0)==' ') c = c.substring(1,c.length);
			if(c.indexOf(nameEQ) == 0) { return c.substring(nameEQ.length,c.length); }
		}
		return null;
	}
};

X.Img = {
	// Popup for displaying images in profiles/galleries
	open: function(pageToLoad, w, h, premium) {
		if(premium == '') { premium = 0; }
		var winheight = parseInt(h);
		var scrollingwidth = parseInt(w);
		var scrollingheight = parseInt(winheight);
		var scrolling = 0;
	  
	  	if(w!=0 && h!=0) {
			if(X.Config.x > 800) {
				if(h > 700) { scrollingwidth = parseInt(w) + 20; scrollingheight = 700; scrolling = 1; }
				if(w > 1000) { scrollingheight = parseInt(winheight) + 20; scrollingwidth = 1000; scrolling = 1; }
				if((w > 1000) &&(h > 700)) { scrollingheight = 700; scrollingwidth = 1000; scrolling = 1; }
			} else {
				if(h > 450) { scrollingwidth = parseInt(w) + 20; scrollingheight = 450; scrolling = 1; }
				if(w > 730) { scrollingheight = parseInt(winheight) + 20; scrollingwidth = 730; scrolling = 1; }
				if((w > 730) &&(h > 450)) { scrollingheight = 450; scrollingwidth = 730; scrolling = 1; }
			}
			var resolution=' width="'+ w +'" height="'+ h +'"';
		}
		else { var resolution=''; xposition=320; yposition=240; }
		
		xposition =(X.Config.x - scrollingwidth) / 2;
		yposition =(X.Config.y - scrollingheight) / 2;
		args = 'width=' + scrollingwidth + ','
			+ 'height=' + parseInt(scrollingheight) + ','
			+ 'location=0, menubar=0, resizable=1, scrollbars=' + scrolling + ','
			+ 'status=0, titlebar=0, toolbar=0, hotkeys=0, '
			+ 'screenx=' + xposition + ','
			+ 'screeny=' + yposition + ','
			+ 'left=' + xposition + ','
			+ 'top=' + yposition;
		oWin = window.open('','_blank',args);
		pic_pop = '<HTML><head><TITLE>Click to close window.<\/TITLE><\/head>'
			+ '<script language="javascript" type="text/javascript">function resize_to_fit() {'
			+ 'var NS =(navigator.appName=="Netscape")?true:false;'
			+ 'iWidth =(NS)?window.innerWidth:document.body.clientWidth; iHeight =(NS)?window.innerHeight:document.body.clientHeight;'
			+ 'iWidth = document.images[0].width - iWidth; iHeight = document.images[0].height - iHeight;'
			+ 'window.resizeBy(iWidth, iHeight-1); self.focus(); };<\/script>'
			+ '<BODY bgcolor="#000000" text="#000000" rightmargin="0" bottommargin="0" '
			+ 'leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" style="margin:0" onload="resize_to_fit()">'
			+ '<div align="center">'
			+ '<a href="javascript:self.close();"><img src="'+ pageToLoad +'"'+resolution+' vspace="0" hspace="0" border="0"><\/a><\/div>'
			+ '<\/BODY><\/HTML>';
		oWin.document.open();
		oWin.document.writeln(pic_pop);
		oWin.document.close();
	},
	fix: function(reference_id) {
		// DOM pointer grab
		dom_pointer = $('#'+reference_id);
	
		// if the image needs to be resized or has been resized, proceed
		if(dom_pointer.css('width') > X.Config.max_img_size 
		|| (typeof X.Img.img_registry[reference_id] != 'undefined' && X.Img.img_registry[reference_id] > X.Config.max_img_size)) {
			// if the image has been resized, restore it
			if(dom_pointer.css('width') == X.Config.max_img_size) { dom_pointer.css('width',X.Img.img_registry[reference_id]); }
			// otherwise, resize it
			else {
				// save its previous width
				X.Img.img_history[reference_id] = dom_pointer.css('width');
				// use the cursor to show it can be resized with a click
				dom_pointer.css('cursor','pointer');
				// resize it
				dom_pointer.css('width',X.Config.max_img_size);
			}
		}
	},
	// tired of hotlink messages from cheap webmasters
	coldlink: function(img) {
		if(img.src!=X.Img.img_src[img.id]) { img.src=X.Img.img_src[img.id]; }
		else { img.src=X.Config.domain+'/hotlink.wtd?url='+img.src; }
	},
	// resize to fit in containing box / max img size config option
	shrink: function(object_pointer) {
		object_pointer.id = 'img'+X.Img.img_id_cnt;
		X.Img.img_id_cnt += 1;
		X.Img.img_src[object_pointer.id]=object_pointer.src;
		X.Img.fix(object_pointer.id);
	}
};

X.Site = {
	// Bookmark the page
	bookmark: function(the_page,the_title) {
		if(document.all) { window.external.AddFavorite(the_page, the_title); }
		else if(window.sidebar) { window.sidebar.addPanel(the_title, the_page, ''); }
	},
	print: function() {
		location.href=location.href+X.Config.print_uri;
	},
//	resize_page: function() {
//		if(typeof window.innerHeight != 'undefined') {
//			var page_height = window.innerHeight - document.getElementById('head').scrollHeight;
//			if(page_height > 0) {
//				document.getElementById('page').style.minHeight=page_height+'px';
//			}
//		} else { // IE sucks!
//			var page_height = document.documentElement.clientHeight - document.getElementById('head').scrollHeight;
//			if(page_height > 0) {
//				document.getElementById('page').style.minHeight=page_height+'px';
//			}
//		}
//	},
	init: function(){
//		X.Site.resize_page();
//		window.onresize = function(){ X.Site.resize_page(); };
	}
};

X.FormFields = {
	highlight: function(the_field) {
		var x=eval("document."+the_field); // eval? <-- bad
		x.focus();
		x.select();
		if(document.all) {
			the_range=x.createTextRange();
			the_range.execCommand("Copy");
			alert("Link to profile copied. To paste this link press Ctrl+V.");
		}
	},
	// Used to switch all checkbox inputs in a form on/off
	// or invert the selection.
	toggleCheckboxes: function(form,toggle_type) {
		var integer=0;
		for(integer=0;integer<form.elements.length;integer++) {
			if(form.elements[integer].type!='checkbox') { continue; }
			if(toggle_type==0) { form.elements[integer].checked=false; }
			else if(toggle_type==1) { form.elements[integer].checked=true; }
			else if(toggle_type==2) { form.elements[integer].checked=(form.elements[integer].checked?false:true); }
		}
	}
};

X.Pages = {
	page_html:'',
	start:0,
	end:0,
	items_per_page:0,
	total_pages:0,
	previous_page:0,
	next_page:0,
	this_page:0,
	first_page:0,
	last_page:0,
	remaining_items:0,
	makePages: function(callback,start,end,items_per_page,pages_shown) {
		X.Pages.page_html='';
		X.Pages.pageMath(start,end,items_per_page,pages_shown);
		X.Pages.pageHTML(callback);
	},
	pageMath: function(start,end,items_per_page,pages_shown) {
		// Page math
		X.Pages.total_pages=Math.ceil(end / items_per_page);
		X.Pages.start=start;
		X.Pages.end=end;
		X.Pages.items_per_page=items_per_page;

		if(X.Pages.total_pages < 2) {
			return false;
		}

		var remainder= (X.Pages.start % X.Pages.items_per_page);
		if(remainder != 0) {
			 X.Pages.start= (start - remainder);
		}

		X.Pages.previous_page= (X.Pages.start - X.Pages.items_per_page);
		X.Pages.next_page= (X.Pages.start + X.Pages.items_per_page);
		X.Pages.this_page= (X.Pages.start / X.Pages.items_per_page);
			
		// If this isnt the first page, figure out how many pages will be displayed
		if(X.Pages.this_page > 0) {
			// half the total pages subtracted from your starting point
			X.Pages.remaining_items=Math.ceil(X.Pages.this_page - (pages_shown / 2)); 
			// if the remainder is a positive number then just use half for the rest of the display
			if(X.Pages.remaining_items >= 0) {
				X.Pages.first_page=X.Pages.remaining_items;
				X.Pages.remaining_items = (pages_shown / 2);
			} else {
			// otherwise start from 0
				X.Pages.first_page=0;
				X.Pages.remaining_items= pages_shown - ((pages_shown / 2) + X.Pages.remaining_items);
			}
		} else {
			// If no pages have been displayed you still have to show all the pages possible.
			X.Pages.remaining_items= pages_shown;
			X.Pages.first_page=0;
		}
	
		// Calculate where the last page would be by default.
		X.Pages.last_page= (X.Pages.remaining_items + X.Pages.this_page);
		// If the remainder of pages is more than the total of pages available, just use the total pages
		if(X.Pages.last_page > X.Pages.total_pages) {
			X.Pages.last_page = X.Pages.total_pages;
		}
	},
	pageHTML: function(callback) {
		// First
		if(X.Pages.start > 0) {
			var first_link = X.Pages.link(callback,'first_button',0,'pages')+' ';
		} else {
			var first_link = '';
		}
		
		// Back
		if(X.Pages.previous_page >= 0) {
			var back_link = X.Pages.link(callback,'back_button',X.Pages.previous_page,'pages')+' ';
		} else {
			var back_link = '';
		}
		
		// Next
		if(X.Pages.next_page < X.Pages.end) {
			var next_link = ' '+X.Pages.link(callback,'next_button',X.Pages.next_page,'pages');
		} else {
			var next_link = '';
		}
		
		// Last
		if((X.Pages.start + X.Pages.items_per_page) < X.Pages.end) {
			var last_link = ' '+X.Pages.link(callback,'last_button',(X.Pages.total_pages - 1) * X.Pages.items_per_page,'pages');
		} else {
			var last_link = '';
		}
		
		// Individual pages
		var iterations = 0;
		// march version
		for (var x = X.Pages.first_page; x < X.Pages.last_page; x++) {
			// why is this here? should this be a "return" when nothings finished rendering?
			//if($iterations > 50) 		{ return $page_list; } 
			if(x == X.Pages.this_page)	{
				X.Pages.page_html+='<span class="current">'+ (x + 1)+'</span> ';
			} else {
				X.Pages.page_html+=X.Pages.link(callback,(x + 1),(x * X.Pages.items_per_page),'pages')+' ';
			}
			iterations++;
		}
		
		// 1-20 of 1000 << First | < Back | Next > | Last >>
		if((X.Pages.start + X.Pages.items_per_page) > X.Pages.end)	{
			var page_end= X.Pages.end;
		} else {
			var page_end= (X.Pages.start + X.Pages.items_per_page);
		}

		X.Pages.page_html='<div style="margin:5px 0px 5px 0px">'
			+'<div class="pages" style="width:200px; float:right; text-align:right">'+first_link+' '+back_link+' '+next_link+' '+last_link+'</div>'
			+'<div class="pages" style="width:200px; float:left; text-align:left">'+((X.Pages.start + 1))+' - '+page_end+' of '+X.Pages.end+' Results. </div>'
			+'<div class="pages">'+X.Pages.page_html+'</div>'
			+'</div>';
	},
	link: function(callback,label,pos,css) {
		if(typeof css == 'undefined' || typeof css =='Undefined') {
			var style='';
		} else {
			var style=' class="'+css+'"';
		}
		
		if  (typeof pos == 'undefined' || typeof pos == 'Undefined') {
			pos = 0;
		}
		
		return '<span'+style+' onClick="'+callback+'('+pos+')">'+label+'</span>';
	},
	getPages: function() {
		if(X.Pages.page_html=='') {
			return false;
		} else {
			return X.Pages.page_html;
		}
	}
};

X.Format = {
	user_path: function(id){ return Math.floor( parseInt(id) / 10000 ) + '/' + id; },
	/* Made by Mathias Bynens <http://mathiasbynens.be/> */
	number_format: function(a, b, c, d) {
		if(typeof b == 'undefined') b=0;
		if(typeof c == 'undefined') c='.';
		if(typeof d == 'undefined') d=',';
		a = Math.round(a * Math.pow(10, b)) / Math.pow(10, b);
		e = a + '';
		f = e.split('.');
		if(!f[0]) { f[0] = '0'; }
		if(!f[1]) { f[1] = ''; }
		if(f[1].length < b) {
		 	g = f[1];
		 	for (i=f[1].length + 1; i <= b; i++) { g += '0'; }
			f[1] = g;
		}
		if(d != '' && f[0].length > 3) {
			h = f[0];
			f[0] = '';
			for(j = 3; j < h.length; j+=3) {
				i = h.slice(h.length - j, h.length - j + 3);
				f[0] = d + i +  f[0] + '';
			}
			j = h.substr(0, (h.length % 3 == 0) ? 3 : (h.length % 3));
			f[0] = j + f[0];
		}
		c = (b <= 0) ? '' : c;
		return f[0] + c + f[1];
	}
};

$(document).ready(function(){
	X.Config.init();
	X.Site.init();
	
	$('a:not(:has(img))')
		.filter('a[rel=external]')
		.before('<img align="absmiddle" src="/img/icons/tiny/world_go.png" width="16" height="16"> ')
		.click(function(){
	    var oWin = window.open($(this).attr('href'), '_blank');
		if(oWin){
			if(oWin.focus){oWin.focus();}
			return false;
		}
		oWin = null;
		return true;
	});
	
	var SearchForm = $('#search_button').parent();
	
	SearchForm.submit(function(event){
		event.preventDefault();
		
		if($('#search_input').val() == 'Search for toys...'){
			location.href = '/Search/?q=';
		} else {
			location.href = '/Search/?q='+$('#search_input').val();
		}
		
		return false;
	});
	
	LinkUpForm = $('#LinkUpRequestForm');
	
	LinkUpForm.submit(function(event){
		event.preventDefault();
		return false;
	});
	
	$('input[type=submit]',LinkUpForm).click(function(){
		$.ajax({
			type:'POST',
			cache:false,
			dataType:'json',
			url: LinkUpForm.attr('action'),
			data: LinkUpForm.serialize(),
			success: function(data, textStatus, jqXHR){
//				console.log(data);
//				console.log(textStatus);
				console.log(typeof data.Confirmations.Success != 'undefined');
				if(typeof data.Confirmations.Success != 'undefined'){
					alert(data.Confirmations.Success);
				} else {
//					console.log(data.Errors);
					$.each(data.Errors, function(i,n){
						alert("Error: "+i+" \nMessage: "+n);
					});
				}
			},
			error:function(jqXHR, textStatus, errorThrown){
				alert('Womp womp whaaa. ERRor');
//				console.log(textStatus);
//				console.log(errorThrown);
			}
		});
	});
});
