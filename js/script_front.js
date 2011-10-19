
jQuery(document).ready(function($){
	
	//initiating fancybox
	/*
	if($.fn.fancybox){
	if($("#show-login").length !=0)	
	jQuery("#show-login").fancybox({  
       'titleShow'     : 'false',  
        'transitionIn'      : 'fade',  
         'transitionOut'     : 'fade'      
    });
    if($("#show-register").length != 0)
    	jQuery("#show-register").fancybox({  
       'titleShow'     : 'false',  
        'transitionIn'      : 'fade',  
         'transitionOut'     : 'fade'      
    });  
	}
	* */
	
	var common = {
		items : 0,
		keyExtPrice: false,
		in_array : function(item, ar){
		for(a in ar)if(ar[a] == item)return true;
		return;
		},
		del_item: function(item, myArray){
			for (key in myArray) {
				if (myArray[key] == item) {
					myArray.splice(key, 1);
					return myArray;
				}
			}
			return myArray;
		},
		show_item: function(){
			interval = setInterval( function(){
				if(/\S/.test( $('#cart-notify').html() )){
					$('#cart-notify').html('');
					return;				
				}
				 $('#cart-notify').html('<h2 style="font-weight:bold;font-size:20px;color:green">'+common.items+ ' Keyword(s)</h2>').hide().fadeIn('slow')}, 1000);
		}
		
		};
	
	var docCookies = {
		getItem: function (sKey) {
			if (!sKey || !this.hasItem(sKey)) { return null; }
				return unescape(document.cookie.replace(new RegExp("(?:^|.*;\\s*)" + escape(sKey).replace(/[\-\.\+\*]/g, "\\$&") + "\\s*\\=\\s*((?:[^;](?!;))*[^;]?).*"), "$1"));
		},
		/**
		* docCookies.setItem(sKey, sValue, vEnd, sPath, sDomain, bSecure)
		*
		* @argument sKey (String): the name of the cookie;
		* @argument sValue (String): the value of the cookie;
		* @optional argument vEnd (Number, String, Date Object or null): the max-age in seconds (e.g., 31536e3 for a year) or the
		*  expires date in GMTString format or in Date Object format; if not specified it will expire at the end of session; 
		* @optional argument sPath (String or null): e.g., "/", "/mydir"; if not specified, defaults to the current path of the current document location;
		* @optional argument sDomain (String or null): e.g., "example.com", ".example.com" (includes all subdomains) or "subdomain.example.com"; if not
		* specified, defaults to the host portion of the current document location;
		* @optional argument bSecure (Boolean or null): cookie will be transmitted only over secure protocol as https;
		* @return undefined;
		**/
		setItem: function (sKey, sValue, vEnd, sPath, sDomain, bSecure) {
			if (!sKey || /^(?:expires|max\-age|path|domain|secure)$/.test(sKey)) { return; }
			var sExpires = "";
			if (vEnd) {
			switch (typeof vEnd) {
				case "number": sExpires = "; max-age=" + vEnd; break;
				case "string": sExpires = "; expires=" + vEnd; break;
				case "object": if (vEnd.hasOwnProperty("toGMTString")) { sExpires = "; expires=" + vEnd.toGMTString(); } break;
			}
			}
			document.cookie = escape(sKey) + "=" + escape(sValue) + sExpires + (sDomain ? "; domain=" + sDomain : "") + (sPath ? "; path=" + sPath : "") + (bSecure ? "; secure" : "");
		},
		removeItem: function (sKey) {
			if (!sKey || !this.hasItem(sKey)) { return; }
		var oExpDate = new Date();
		oExpDate.setDate(oExpDate.getDate() - 1);
		document.cookie = escape(sKey) + "=; expires=" + oExpDate.toGMTString() + "; path=/";
		},
		hasItem: function (sKey) { return (new RegExp("(?:^|;\\s*)" + escape(sKey).replace(/[\-\.\+\*]/g, "\\$&") + "\\s*\\=")).test(document.cookie); }
	};//End Of Doc Cookies

	//Shopping cart 
	//If cart cookie set
	if( docCookies.hasItem('cItems') ){		
		var pv = docCookies.getItem('cItems');
		var ar = pv.split('-');
		var totItem = Number( ar[0] );
		var totPrice = Number( ar[1] );
		var items = ar[2].split(',');
		if( docCookies.hasItem( 'keyExtPrice' ) ){
			totPrice = totPrice - Number( docCookies.getItem('keyExtPrice'));
			docCookies.removeItem('keyExtPrice');
		docCookies.setItem('cItems', totItem + '-' + totPrice + '-' + items.join(','), null, '/' );	
		}
			$('#totPrice').hide().text( '$'+totPrice ).fadeIn('slow');
			$('#totItem').hide().text( totItem).fadeIn('slow');
		//docCookies.setItem('cItems', pv+'1');
		//alert(docCookies.getItem('cItems'));
		common.items = totItem;
		common.show_item(totItem);
		
		
			$('.add-to-cart').each(function(){
				var cl = $(this).parent().attr('class').match(/\d+/);
				if( common.in_array(cl, items) )
					$(this).text('Remove');
				
			})
	
		}
	else{		
		//docCookies.setItem('cItems', 'test');
	}
	$('#cart-1').live('click', function(e){
		e.preventDefault();
		if($('#cart-wrapper').css('height') =='150px' ){
			$('#cart-wrapper').animate({height:'25px'},500);
			$('#min-cart').text('+');
			return;		
		}
	
		$('#cart-wrapper').animate({height:'150px'},500);
		$('#min-cart').text('--');
			
	})
	
	$('.add-to-cart').live('click', function(e){
		
		e.preventDefault();
		var classA = '';
		classA = $(this).parent().attr('class').match(/\d+/);

		var price = Number( $(this).parents('tr').children('td:nth-child(13)').text().replace(/\$/, '') ) ;
		var tPrice =  $('#totPrice').text() ;
		tPrice = (/\$/.test(tPrice)) ? Number( tPrice.replace(/\$/,'') ) : Number(tPrice) ;
		var tItem =  Number( $('#totItem').text() );
		
		if( $(this).text() == 'Remove' ){
			tPrice -= price;
			$('#totPrice').hide().text( '$'+tPrice ).fadeIn('slow');
			$('#totItem').hide().text( tItem-1 ).fadeIn('slow');
			$(this).text('Add to cart');
			if( docCookies.hasItem('cItems') ){
				var pv = docCookies.getItem('cItems');
				var ar = pv.split('-');
				
				var items = unescape(ar[2]).split(',');
				items = common.del_item(classA, items);
				docCookies.setItem('cItems', (tItem-1)+'-'+tPrice+'-'+items.join(','), null, '/' );
				if( Number(tItem)== 1)docCookies.removeItem('cItems');
				common.items = tItem -1;			
			
			}
		return;
		}
		
		tPrice += price;
		
		$('#totPrice').hide().text( '$'+tPrice ).fadeIn('slow');
		$('#totItem').hide().text( tItem+1 ).fadeIn('slow');		
		$(this).text('Remove');
		
		if( docCookies.hasItem('cItems') ){
		var pv = docCookies.getItem('cItems');
		var ar = pv.split('-');
		var totItem = Number( ar[0] );
		var totPrice = Number( ar[1] );
		var items = unescape(ar[2]).split(',');
		if(!common.in_array(classA,items))
			items[items.length] = classA;
		
		docCookies.setItem('cItems', (tItem+1)+'-'+tPrice+'-'+items.join(','), null, '/');
		
			
		}
		else{
			var items = Array();
			items[0] = classA;
			docCookies.setItem('cItems', (tItem+1)+'-'+tPrice+'-'+items.join(','), null,'/');			
		}
		common.items = tItem +1;
		if(typeof(interval) !== 'undefined')
			window.clearInterval(interval);	
		common.show_item(totItem);
		//$('#cart-notify').html('<h2 style="font-weight:bold">Item Added</h2>');
		//$('#cart-wrapper').css('height','45px'); 
		//$('#cart-notify').animate({marginTop : '-20px'},500);
	})
	
	//For Adding or Removing All
	
				$('a#cartAddAll').bind('click', function(e){
				e.preventDefault();
				if( $(this).text() == 'Add ALL' ){
					$('.add-to-cart').each(function(){
						 if($(this).text() == 'Add To Cart' )
							$(this).click();	
					})
					$(this).text('Remove ALL');
					return;
				}
				
					$('.add-to-cart').each(function(){
						 if( $(this).text( ) == 'Remove' ) 
						$(this).click();
					});
					$(this).text('Add ALL');
			});
	
	//pagenavigation to the page num
	$('#kt-pagenumb').live('click', function(){
		var num = $(this).prev().val();
		var loc = window.location.href;
		loc = (loc.search(/\?/) != -1) ? loc : loc+'?custom=1';
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
		loc = (loc.search(/\?/) != -1) ? loc : loc+'?custom=1';
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
		loc = (loc.search(/\?/) != -1) ? loc : loc+'?custom=1';
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
	// finding Greater than value
	$('#fkbtn').bind('click', function(){
		var val = $(this).prev().val();
		var name = $(this).prev().prev().val();
		name = name.toLowerCase().replace('.', '').replace(/\s/g, '_');
		switch(name){
			case 'global_monthly_searches':
			name = 'global_searches_month';
			break;
			case 'local_monthly_searches':
			name = 'local_searches_month';
			break;
			}
			window.location.search ='?gr=1&grname='+name+'&grval='+val;
		
		})
	
	//Toggling Ascending and Descending		
	$('a.th-toggle').live('click', function(evt){
		evt.preventDefault();
		var text = $(this).text();			
		text = text.toLowerCase().replace('.', '').replace(/\s/g, '_');
		var loc = window.location.href;
		loc = (loc.search(/\?/) != -1) ? loc : loc+'?custom=1';
		var order = (loc.match(/order=asc/))? 'des' : 'asc';
		
		if(loc.match(/orderby=/))
			loc = loc.replace(/(&orderby=[a-z_]*)|(&order=[a-z]*)/g, '');
		
		switch( $(this).text()){
			case 'Global Monthly Searches':
				text = 'global_searches_month';
			break;
			case 'Local Monthly Searches':
				text = 'local_searches_month';
			break;
			case 'Search Results':
				text = 'comp_pages';
			break;
			case 'Real Search Results':
				text = 'real_comp_pages';
			break;
			case 'Ads Displayed':
				text = 'ads_count';
			break;
			
			}
		
		loc = loc+'&orderby='+text+'&order='+order;
		window.location.href = loc;
		})
		
		//For removing from cart
		$('a.remove-cart').live('click', function(e){
			e.preventDefault();
			classA = $(this).parent().attr('class').match(/\d+/);
			var price = Number( $(this).parents('tr').children('td:nth-child(13)').text().replace(/\$/, '') ) ;
				var pv = docCookies.getItem('cItems');
				var ar = pv.split('-');
				tPrice = Number(ar[1]);
				tItem = Number(ar[0]);
				tPrice = tPrice - price; 
				
				var pv = docCookies.getItem('cItems');
				var ar = pv.split('-');
				var items = unescape(ar[2]).split(',');
				items = common.del_item(classA, items);
				
				docCookies.setItem('cItems', (tItem-1)+'-'+tPrice+'-'+items.join(','), null, '/' );
			
				common.items = tItem -1;
				if( Number(tItem)== 1)docCookies.removeItem('cItems');
				$(this).parent().parent().fadeOut('slow');
				$('#totNumber').text(''+(tItem-1));	
				$('#totItem').text(''+(tItem-1));	
				$('#totPrice').text('$'+tPrice);	
				$('#totAm').text('$'+tPrice);	
			
		});
		$('a#cart-remove-all').bind('click', function(e){
			e.preventDefault();
			docCookies.removeItem('cItems');
			docCookies.removeItem('keyExtPrice');
			window.location.href= window.location.href;			
			});
			

		$('a#changeP').live('click',function(e){
			e.preventDefault();
			$('#changeIt').fadeIn('slow');
			
			})
			
			$('a.export-csv').live('click', function(e){
				e.preventDefault();
				loc = window.location.href;				
				loc =  loc + '?export=' + $(this).parent().attr('class');
				window.open(loc,'export');
				})
			$('a.view-table').live('click', function(e){
				e.preventDefault()
				var thisA= $(this);
				if (thisA.text() == 'HIDE'){
					$('#hideShow').fadeOut('slow');
					thisA.text('view in table');
					return;
					}
					
				var classA= $(this).parent().attr('class');
					$.ajax({
					type :  "post",
					url : ktSettings.ajaxurl,
					timeout : 5000,
					data : {
					 'action' : 'wt_table_show',
					 'class' : classA		  
					},			
					success :  function(data){						
						$('#hideShow').html(data).fadeIn('slow');
						thisA.text('HIDE');
					}
				})
				
				
				})
				
			//Add keyword Extenstion Raise Price
			$('#add-key-ext').bind('click', function(){
				var stat = $(this).attr('checked') ;
					var pv = docCookies.getItem('cItems');
					var ar = pv.split('-');
					var tItem = Number( ar[0] );
					var tPrice = Number( ar[1] );
					var items = ar[2].split(',');
				
				if( stat == 'checked' || stat == true ){
					common.keyExtPrice = (common.keyExtPrice)? common.keyExtPrice:Math.ceil(tPrice*0.20);
					tPrice = tPrice + common.keyExtPrice;
					docCookies.setItem('cItems', tItem + '-' + tPrice + '-' + items.join(','), null, '/' );
					$('#totPrice').text('$'+tPrice);	
				    $('#totAm').text('$'+tPrice);
				    docCookies.setItem('keyExtPrice', common.keyExtPrice , null, '/' );	
					
				}
				else if( stat == undefined || stat == false ){
					common.keyExtPrice = (common.keyExtPrice)? common.keyExtPrice:Math.ceil(tPrice*0.20);
					tPrice = tPrice - common.keyExtPrice;
					docCookies.setItem('cItems', tItem + '-' + tPrice + '-' + items.join(','), null, '/' );
					$('#totPrice').text('$'+tPrice);	
				    $('#totAm').text('$'+tPrice);	
				    docCookies.removeItem('keyExtPrice');
				 				
					} 
				
				})
			
})
