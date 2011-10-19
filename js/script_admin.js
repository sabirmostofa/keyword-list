
jQuery(document).ready(function($){
	
	//initiating autocomplete
	var options = { 
	serviceUrl : ajaxurl +'?action=myajax-submit',
	width:300
	};			
	$('#kt-ajax').autocomplete(options);
	
	//pagenavigation to the page num
	$('#kt-pagenumb').bind('click', function(){
		var num = $(this).prev().val();
		var loc = window.location.href;
		var pattern = /paged=\d+/;
		if(pattern.exec(loc))
		 loc = loc.replace(pattern,'paged='+num);
		 else
		 loc= loc+'&paged='+num;
		 
		 window.location.href=loc;		
		
		});
	// selecting keywords per page
	$('#select-kpp').change(function(){
		var num = $(this).val();
		var loc = window.location.href;
		var pattern = /kpp=\d+/;
		if(pattern.exec(loc))
			loc = loc.replace(pattern,'kpp='+num);
		else
			loc= loc+'&kpp='+num; 
		window.location.href=loc;
		
	});
			
	//selecting Category			
	$('#select-cat').change(function(){
		var cat = $(this).val();
		var loc = window.location.href;
		var pattern = /cat=\S+/;			
		if(cat == 'All')
			if(pattern.exec(loc)){
				loc = loc.replace(/&cat=[^&]+/,'');
				window.location.href=loc;
				return;
			}	
		
		if(pattern.exec(loc))
			loc = loc.replace(pattern,'cat='+cat);
		else
			loc= loc+'&cat='+cat;
		 
		window.location.href=loc;	
			
			
	});
			
	// Edit an entry
	$('#kt-main tr ').bind('click', function(){
		var str = $(this).html();
		var pattern = /<td>.*?<\/td>/;
		var ip =/\S/;
		var p = /(<td>)|(<\/td>)/g;
		var a =new Array();
		
		$('#kt-man td input').each(function(index){
			$(this).val('');
			})
			
			
		var i = 0;
		while(pattern.exec(str)){
		     if(i==0 || i ==1 ){
				 str=str.replace(pattern, '');
				 ++i;
				 continue;
				 }
			var val = pattern.exec(str);						
			val = String(val);
			val = val.replace(/(<span.*?>)|(<\/span>)/g,'');						
			a[i++ -2]= val.replace(p, '');				
			str=str.replace(pattern, '');
		
		}					
			$('#kt-man td input').each(function(index){
				if(ip.exec( $(this).val() ))
					return;
				else
					$(this).val(a[index]);
				});
	});
					
	// Clear the Add fields
	$('#add-clear').bind('click', function(evt){
		evt.preventDefault();
		$('#kt-man td input').each(function(index){
			$(this).val('');
		})
		
		
	});					
	//Delete
	$('a.delete-kt').bind('click',function(evt){
		evt.preventDefault();
		var ans = confirm('This entry will be deleted. Are you sure?');
		if(ans==false)return;
		var key = $(this).parents('').next().html();
			
		$.ajax({
			type :  "post",
			url : ajaxurl,
			timeout : 5000,
			data : {
			 'action' : 'ajax_remove',
			 'key' : key		  
			},			
			success :  function(data){						
				window.location.href=window.location.href;
			}
		})	//end of ajax					
			
	});// end of delete	
						
	//Delete Multiple
	$('#check-all').bind('click',function(){
		if($(this).attr('checked') == true || $(this).attr('checked') == 'checked'){
			var val = $(this).attr('checked'); 
			$('input.checky').each(function(){
				$(this).attr('checked', val);				
				})
		}
			
			else if( $(this).attr('checked') == false || $(this).attr('checked') == undefined ){
				$('input.checky').each(function(){
					$(this).removeAttr('checked');
					
				})
			}		
	});
					
	$('#delete-all-kt').bind('click', function(evt){
			evt.preventDefault();
			var ans = confirm('All selected Entries will be deleted. Are you sure?');
			if(ans==false)return;
			var i = 0;
			var ar = new Array();
			
			$('input.checky').each(function(){
			if( $(this).attr('checked') == true || $(this).attr('checked') == 'checked')
				 ar[i++] = $(this).parents('td').next().next().text();							 
				 
			})
			
			$.ajax({
			type :  "post",
			url : ajaxurl,
			timeout : 5000,
			data : {
			 'action' : 'ajax_remove_multiple',
			 'keys' : ar.join(',')	  
			},			
			success :  function(data){						
				window.location.href=window.location.href;
			}
			})	//end of ajax		
			
	})// End of Delete Multiple process
						
	//Toggling Ascending and Descending		
	$('a.th-toggle').bind('click', function(evt){
		evt.preventDefault();
		var text = $(this).text();			
		text = text.toLowerCase().replace('.', '').replace(/\s/g, '_');
		var loc = window.location.href;
		var order = (loc.match(/order=asc/))? 'des' : 'asc';
		
		if(loc.match(/orderby=/))
			loc = loc.replace(/(&orderby=[a-z_]*)|(&order=[a-z]*)/g, '');
		
		loc = loc+'&orderby='+text+'&order='+order;
		window.location.href = loc;
		})
			
	//Highlighting The Searched Keywords
	var sr = $('#kt-ajax').val();
	if(sr.match(/\S/))
		$('#kt-main tr td:nth-child(3)').each(function(index){
			var htm = $(this).html();
			var text = $(this).text();
			var len = sr.length;
			var ar = new Array();
			var i = 0;
							
			var ini = text.search(sr);
			var mid = text.substr(ini,len);
			var first= text.substring(0,ini);
			var last= text.substring(ini+len, text.length);
			var ht = '<td>'+first+'<span style="color:green;font-weight:bold;">'+mid+'</span>'+last+'</td>';
			$(this).html(ht);								
		})				
				
			
		
	
	})
	
