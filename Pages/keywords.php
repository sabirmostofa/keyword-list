<?php
/*
Template Name: keywords
*/

get_header(); 
//Variables Needed for the page

$current_page = 1;
$total_pages =1;
$table_heads = array('Keyword', 'Add/Remove','Global Monthly Searches', 'Local Monthly Searches', 'CPC', 'Comp. Pages', 'Real Comp. Pages', 'Average PR', 'Competition', 'Ads Count', 'Keyword Rating', 'Category','Price'); 
$all_res = $wpdb->get_results("SELECT * from wp_keywords_list", 'ARRAY_N');

// Pagenavi vars

$results = $wpdb->get_results("SELECT * from wp_keywords_list", 'ARRAY_N');
$all_res = $results;
$kpp=(isset($_REQUEST['kpp'])) ? $_REQUEST['kpp']: 21;
$total_pages = ceil ( count($results)/$kpp );
if ( isset( $_REQUEST['paged'] ) )
	$current_page= isset($_REQUEST['paged'])? $_REQUEST['paged']:1;
else
	$current_page= get_query_var('paged') ? get_query_var('paged') : 1;


$lower_limit= ($current_page-1)*$kpp;
$higher_limit = $lower_limit + $kpp;


	$d_query = "SELECT * from wp_keywords_list"; 
	
	if( isset( $_REQUEST['cat'] ) ){
		
		$catg = $_REQUEST['cat'];
		$d_query .= " where category='$catg'";
		
		$total_pages = ceil(count($wpdb->get_results("select * from wp_keywords_list where category='$catg'", 'ARRAY_N'))/$kpp);
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

?>
<!-- POST Content-->
<div class="col col_16">
          	<div class="indent-right">
	<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
				<?php the_content(); ?>
	<?php endwhile; ?>
    </div>
</div>


	<!-- Page Navigation: variables declared previously -->	

	
 <div class='kt-pagenavi'>
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

foreach($cats as $cat){
	$str='';
	if( isset($_REQUEST['cat']))
		if($cat[0] == $_REQUEST['cat']) $str=" selected=\"selected\" ";
	
	echo "<option". $str .">{$cat[0]}</option>";
}
?>
</select>


 </div>	<!-- End of Pagenavi-->	

<!-- Table -->
<table id='kt-main' class='widefat'>
	<thead>
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
	</thead>
	<tbody>
	
	<?php
	if($current_page > $total_pages)
		echo "<tr><td colspan=9 ><div class='updated'>Not too many Pages available. There are total {$total_pages} pages</div></td></tr>"; 
	foreach( $all_res as $result){
		echo '<tr>';
		$class = 'm' . $result[0];		
		$result = array_slice($result, 1, 14);
		$result = $wpKeywordsTable -> reverse_process_array($result);
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
		<a href=''>Checkout</a>
	</div>
</div>



<?php
get_footer();
