<?php
/*
Template Name: keywords
*/

get_header('wt'); 
//
if( get_option('kt-settings-var') )extract( get_option('kt-settings-var'));
//Variables Needed for the page

$current_page = 1;
$total_pages =1;
$table_heads = array('Keyword', 'Add/Remove','Global Monthly Searches', 'Local Monthly Searches', 'CPC', 'Search Results', 'Actual Results', 'Average PR', 'Competition', 'Ads Displayed', 'Keyword Rating', 'Category','Price','.COM','.NET','.ORG'); 
$all_res = $wpdb->get_results("SELECT * from wp_keywords_list", 'ARRAY_N');

$table_head_des = array(
'Keyword' => 'keyword title will visible after you buy', 
'Add/Remove' => '',
'Global Monthly Searches' => 'Exact Match Total monthly searches world wide', 
'Local Monthly Searches' => 'Exact Match Total monthly local searches (U.S.)',
'CPC' =>'Cost Per Click, the higher the better as this is usually closely parallel to projected adsense CPCs.',
'Search Results' =>'Number of results in google. A high number doesn\'t always mean high competition.',
'Real Search Results' =>'Number of results in google that are actually relevant to the keyword. The lower the better here as that means less competition.', 
//'CP Strength' =>'How strong are the real competing pages. Lower the easier that keyword is.',
'Average PR' => 'Average Page Rank of the top 20 ranking sites.',
//'PR Strength' =>'Rating indication of how high or low the PR strength is.',
'Competition' => 'A rating of the top 10 ranked sites, analyzing their average backlink count, PR, age, if the keyword is in their title, description or H1 tag and much more.', 
'Ads Displayed' =>'Number of ads displayed when searching the keyword.', 
'Keyword Rating' =>'Overall rating based on ALL criteria.', 
'Category' =>'The niche that keyword is related to.',
'Price' =>'How much that keyword costs to buy.',
'.COM' =>' Is the exact match .com domain available?',
'.NET' =>'Is the exact match .net domain available?',
'.ORG' =>'Is the exact match .org domain available?'
);

$table_heads = array();
foreach ($table_head_des as $key => $value){
	$table_heads[]="<abbr title=\"{$value}\">{$key}</abbr>";
	}

// Pagenavi vars

$results = $wpdb->get_results("SELECT * from wp_keywords_list", 'ARRAY_N');
$all_res = $results;
$kpp=(isset($_REQUEST['kpp'])) ? $_REQUEST['kpp']: 20;
$total_pages = ceil ( count($results)/$kpp );
if ( isset( $_REQUEST['paged'] ) )
	$current_page= isset($_REQUEST['paged'])? $_REQUEST['paged']:1;
else
	$current_page= get_query_var('paged') ? get_query_var('paged') : 1;


$lower_limit= ($current_page-1)*$kpp;
$higher_limit = $lower_limit + $kpp;


	$d_query = "SELECT * from wp_keywords_list"; 
        
	//search logic
	if( isset( $_REQUEST['searchdb'] ) ){
		
		$search = $_REQUEST['search-from-db'];
		$d_query .= " where keyword like '%$search%'";                
		
		$total_pages = ceil(count($wpdb->get_results("select * from wp_keywords_list where keyword like '%$search%'", 'ARRAY_N'))/$kpp);
	}
	if( isset( $_REQUEST['cat'] ) ){
		
		$catg = $_REQUEST['cat'];
		$d_query .= (stripos( $d_query,'where' ))? " and category='$catg":" where category='$catg'";
		
		$total_pages = ceil(count($wpdb->get_results("select * from wp_keywords_list where category='$catg'", 'ARRAY_N'))/$kpp);
	}
	if(isset ($_REQUEST['gr']) ){
		$name = $_REQUEST['grname'];
		$val = $_REQUEST['grval'];
		$d_query .= (stripos( $d_query,'where' ))? " and $name > $val" : " where $name > $val";		
		}

	
	if(isset($_REQUEST['orderby'])){
	$orderby = $_REQUEST['orderby'];	
	$order = ($_REQUEST['order'] == 'asc')? 'asc':'desc';
	$d_query .= " order by {$orderby} {$order}";
	}
	else
		$d_query .= " order by added desc";
		$d_query .= " limit {$lower_limit},{$kpp} ";
	//var_dump($d_query);
	$results = $wpdb->get_results($d_query, 'ARRAY_N');
	$all_res = $results;
	
// which number to show


$span = 3;
$start_num = 1;
if ( $total_pages <= $span)$start_num=1;
elseif(($total_pages-$current_page) <= $span )$start_num = $total_pages-$span;
elseif( ($total_pages-$current_page) >$span) $start_num = $current_page;

// How many keywords to show per page
$nums = array(1,5,10,20,50,100);

//Categories
$cats = $wpdb->get_results("SELECT category from wp_keywords_list where 1=1", 'ARRAY_N');

// greater than values
$grval =(isset( $_REQUEST['grval'] ))? $_REQUEST['grval']  : 0;

?>
<!-- POST Content-->
<div class="col col_16">
          	<div class="indent-right">
	<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
				<?php// the_content(); ?>
	<?php endwhile; ?>
    </div>
</div>


	<!-- Page Navigation: variables declared previously -->	

	<center><strong><h1>Keyword List</h1></strong>
<p style="margin-right:150px; margin-left:100px;">Browse through our list and click add to cart to select any keywords you might be interested in ordering.
Click the column header to sort ascending and click again to sort descending</p>
<p style="margin-right:150px; margin-left:100px;">Data up to date as of August 2011.
   All data like search volumes, CPC from Google Keyword tool. All competition data is based of our softwares calculations and manual reviews</p>
<p><h5>Search Volume Is <strong>Exact Match!</strong></h5></p>
 <div class='kt-pagenavi'>
 Find Keywords where
<select name='fkgr'>
<option <?php if( isset($_REQUEST['grname']) && $_REQUEST['grname'] =='local_searches_month' ) echo 'selected="selected"'; ?>>Local Monthly Searches</option>
<option <?php if( isset($_REQUEST['grname']) && $_REQUEST['grname'] =='global_searches_month' ) echo 'selected="selected"'; ?>>Global Monthly Searches</option>
<option <?php if( isset($_REQUEST['grname']) && $_REQUEST['grname'] =='cpc' ) echo 'selected="selected"'; ?>>CPC</option>
</select>
is Greater than
<input type='text' id='fkval' name='fkval' value='<?php echo $grval; ?>'/>
<button id ='fkbtn'>Find</button>
 <br/><br/>
 <?php
 
 if($current_page != 1)echo "<span><a href=\"". get_pagenum_link($current_page-1) ."\">Prev</a></span>";
 $count = 0;
 for( $i=$start_num; $i <= $total_pages; $i++):
 if($count++ > $span)continue;
 
 ?>
 
 <span <?php if($i == $current_page)echo 'class="current"'; ?>>
 <a  href="<?php echo get_pagenum_link($i); ?>"><?php echo $i; ?></a>
 </span>
 
<?php 
endfor;

// Showing the Dot and the last page at right
 if($current_page != $total_pages && ($total_pages-$current_page) > $span)
 echo "<span>..</span> <span>...</span><span><a href=\"". get_pagenum_link($total_pages) ."\">". $total_pages."</a></span><span><a href=\"". get_pagenum_link($current_page+1) ."\">Next</a></span>";


?>
<label style="margin-left:10px;" for="kt-pagenum">Page No:(max <?php echo $total_pages; ?>)</label>
<input style="width:50px" type='text' value="<?php echo $current_page; ?>" name='kt-pagenum'/>
<button id="kt-pagenumb" >Go</button>

<!-- selecting keywords per page -->


<label style="margin-left:10px;" for="kpp">Keywords Per page</label>
<select name='kpp' id="select-kpp" >
<?php
foreach($nums as $num){
	$str='';
	if($num == $kpp) $str=" selected=\"selected\" ";
	
	echo "<option". $str .">{$num}</option>";
}
?>
</select>

<!-- Selecting Category -->
<label for="kpp">Category:</label>
<select name='cat-sel' id="select-cat" >
<?php


array_unshift($cats, array('All'));

$arC = array();
foreach($cats as $cat){
	if(!in_array($cat[0], $arC))
		$arC[] =$cat[0];
	else
		continue;
	$str='';
	if( isset($_REQUEST['cat']))
		if($cat[0] == $_REQUEST['cat']) $str=" selected=\"selected\" ";
	
	echo "<option". $str .">{$cat[0]}</option>";
}
?>
</select>
</center>
<!-- keyword search section -->
<form action="" method="get"  style="margin-left:50px">
    <label for="search-from-db">Search Keyword:</label>
    <input type="text" name="search-from-db" value="<?php if(isset($_REQUEST['search-from-db']))echo $_REQUEST['search-from-db'];  ?>"/>
    <input type="submit" name="searchdb" value="Search"/>
</form>
 </div>	<!-- End of Pagenavi-->	

<!-- Table -->
<table id='kt-main' class='widefat'>
	<thead>
		<tr>
			<?php 
			foreach($table_heads as $key => $head){
				if($key == 1)
					echo "<th>{$head} <a id='cartAddAll' href=''>Add ALL</a></th>" ;
				else 
				echo "<th><a class='th-toggle' href=''>{$head}</a></th>"; 
			}
			?>
		</tr>
	</thead>
	<tbody>
	
	<?php
	if($current_page > $total_pages)
		echo "<tr><td colspan=9 ><div class='updated'>Not too many Pages available. There are total {$total_pages} pages</div></td></tr>"; 
	foreach( $all_res as $result){
		echo '<tr>';
		$class = 'm' . $result[0];		
		$result = array_slice($result, 1, 18);
		$result = $wpKeywordsTable -> reverse_process_array($result);
		unset($result[11]);
        unset($result[9]);
		unset($result[7]);
		foreach ($result as $res)echo "<td class=\"{$class}\">{$res}</td>";
		echo '</tr>';
		
	}
	?>

</tbody>

	<tfoot>
		<tr>
			<?php 
			foreach($table_heads as $key => $head){
				if($key == 1)
					echo "<th>{$head}</th>" ;
				else 
					echo "<th><a class='th-toggle' href=''>{$head}</a></th>"; 
			}			
			?>
		</tr>
	</tfoot>

</table>

<!-- Shopping Cart -->
<div id='cart-notify'>
	</div>
<div id="cart-wrapper">
	
	<div id="cart-1">
		<div class="ntr"><h2 style="display:inline">Shopping Cart</h2>
		</div>
		<a id='min-cart' style="" href='' >--</a>
	</div>
	<div id='cart-content'>
		<span style="font-weight:bold">Total Item(s): </span> <span id="totItem">0</span><br/>
		<span style="font-weight:bold">Total Price : </span><span id= "totPrice">0.00</span><br/>
		<a href='<?php echo get_permalink( $check_page)  ?>'>Checkout</a>
		<a style ="margin-left:20px" href='' id="cart-remove-all">Reset Cart</a>
	</div>
</div>



<?php
get_footer('wt');
