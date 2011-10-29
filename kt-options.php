<?php
global $wpdb;
$table_heads = array('Keyword', 'Global Monthly Searches', 'Local Monthly Searches', 'CPC', 'Comp. Pages', 'Real Comp. Pages', 'CP Strength', 'Average PR', 'PR Strength', 'Competition', 'Commercial', 'Ads Count', 'Keyword Rating', 'Category','Price','.com','.net','.org','Count'); 
$columns = array ('keyword', 'global_searches_month', 'local_searches_month', 'cpc', 'comp_pages', 'real_comp_pages', 'cp_strength', 'average_pr', 'pr_strength', 'competition', 'commercial', 'ads_count', 'keyword_rating', 'price','com','net','org');

if(isset($_POST['submit'])):
		if ($_FILES["file"]["error"] > 0){
		  echo "An Error Occurred. Check Your Directory Permission". "<br />";
		 }
		else{
			  echo '<div class="updated">File Uploaded Successfully</div><br/>';

		  // Create directory for uplods and move the file to the upload direcotry
		  $file =  $_FILES["file"]["tmp_name"];
		  $uploads = wp_upload_dir();
		  $dir = $uploads['basedir'];
		  if( !is_dir( $dir.'/kt-csv' ) )mkdir( $dir.'/kt-csv' );
		  $t = time();
		  $name = $_FILES["file"]["name"];
		  $s = preg_replace( '/([^.]+)/', "\${1}--$t", $name, 1 );
		  move_uploaded_file($file,$dir.'/kt-csv/'.$s);
		  $file = $dir.'/kt-csv/'.$s;
		  
		  //Uploading to database
		$row = 1;		
		if (($handle = fopen($file, "r")) !== FALSE) {
		while (($data = fgetcsv($handle, 10000, ",")) !== FALSE) {
		if($row++==1)continue;
        $num = count($data);
        
     
        $data = $this -> process_array($data);
        $data_real = $data;
        $data = array_map( create_function('$a', 'return "\"" . mysql_real_escape_string( trim($a) ) . "\"";'), $data);
       
        $value = implode(',', $data);
        if(! $this -> exists_in_table($data_real[0]))
         $query = "insert into wp_keywords_list(keyword, global_searches_month, local_searches_month, cpc, comp_pages, real_comp_pages, cp_strength, average_pr, pr_strength, competition, commercial, ads_count, keyword_rating, category, price, com, net, org) 
         values(${value})";
        else{
			$str = '';
			$id = $wpdb -> get_var("select id from wp_keywords_list where keyword='$data_real[0]'");
			foreach($columns as $key => $val){
				$str .= $val . '=' . $data[$key]. ',';
			}
         $query = "update wp_keywords_list set ". trim($str,',')." where id = '$id'";
	 }
       
         //var_dump($query);      
        ///exit;
      mysql_query($query) or die(mysql_error());
        }
         fclose($handle);
    }
 }
 
 
 // If Adding Data Manually
	elseif( isset($_POST['add-submit']) ):
	$data = $_POST['man_sub'];
	 $data = $this -> process_array($data);
        $data_real = $data;
        $data = array_map( create_function('$a', 'return "\"" . mysql_real_escape_string( trim($a) ) . "\"";'), $data);
       
        $value = implode(',', $data);
        if(! $this -> exists_in_table($data_real[0]))
         $query = "insert into wp_keywords_list(keyword, global_searches_month, local_searches_month, cpc, comp_pages, real_comp_pages, cp_strength, average_pr, pr_strength, competition, commercial, ads_count, keyword_rating, category, price, com, net, org) 
         values(${value})";
        else{
			$str = '';
			$id = $wpdb -> get_var("select id from wp_keywords_list where keyword='$data_real[0]'");
			foreach($columns as $key => $val){
				$str .= $val . '=' . $data[$key]. ',';
				}
         $query = "update wp_keywords_list set ". trim($str,',')." where id = '$id'";
	 }
	 
	 
       
            
        // exit;
    $query_res = mysql_query($query) or die(mysql_error());
	
  endif; // End of condition if csv File Posted or Other POST Condition
  

// Pagenavi vars

$results = $wpdb->get_results("SELECT * from wp_keywords_list", 'ARRAY_N');
$all_res =  $results;
$kpp=(isset($_REQUEST['kpp'])) ? $_REQUEST['kpp']: 20;
$total_pages = ceil ( count($results)/$kpp );
$current_page= isset($_REQUEST['paged'])? $_REQUEST['paged']:1;


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

	
	// If Searching 
	if( isset($_POST['auto-search']) ){
		$total_pages = 1;
		$as = $_POST['kt-ajaxfield'];
		if(preg_match('/\S/', $as))$results = array();
		foreach($all_res as $res){
			if(stripos($res[1], $as ) !== false)$results[] = $res;			
			
			}
		//$total_pages = ceil(count($resuls)/$kpp);
		
		}

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

 
			<div class="wrap"><h3>Upload CSV file</h3>
			<form action=" " method="post" enctype="multipart/form-data">
				<label for="file">Filename:</label>
				<input type="file" name="file" id="file" /> 
				<br />
				<input class='button-primary' type="submit" name="submit" value="Submit" />
				</form>
			</div>
			
 <!-- Form for adding manaully -->
		<div class="wrap">
		<h3>Add manually/Edit</h3>
		<?php 
		
		//var_dump($query_res);
		if($query_res)echo "<div class='updated'>Data Added To Table Successfully</div>" ?>
		<form action='' method='post'>
		<table class='widefat' id='kt-man'>
			<thead>
				<tr>
					<?php
					$table_heads_minus = array_slice($table_heads,0,18);
					foreach($table_heads_minus as $head)echo "<th  style=\"text-align:center;\">{$head}</th>"; 
					?>
				</tr>
			</thead>
			<tbody>
			<tr>
				<?php
					for($i=0;$i<18;$i++)echo "<td ><input style=\"width:100%\" type='text' name=\"man_sub[]\"/></td>";
				?>
			</tr>
			</tbody>
			</table>
		
		<input class='button-primary' type="submit" name="add-submit" value="Submit" />
		<button class='button-primary'  id="add-clear" value="Clear" >Clear</button>
		</form>
		</div>
		
		<!-- Ajax Autocomplete Search -->
		<div class="ajax-search">
		<form action='' method='post'>
		    <label for='kt-ajaxfield'>Search Keyword: </label>
			<input type="text" id="kt-ajax" name="kt-ajaxfield" <?php if(isset($as)) echo "value=\"$as\"" ?>/>
		
			<input type="submit" class='button-primary' name='auto-search' value="Search"/>
			<label for='kt-ajaxfield'><?php if( isset($_POST['auto-search'] ) && preg_match('/\S/', $_POST['kt-ajaxfield']) ) echo "Showing results For \"{$as}\"";  ?></label>

			</form>
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
<label style="margin-left:30px;" for="kt-pagenum">Page No:(max <?php echo $total_pages; ?>)</label>
<input style="width:50px" type='text' value="<?php echo $current_page; ?>" name='kt-pagenum'/>
<button id="kt-pagenumb" >Go</button>

<!-- selecting keywords per page -->

<label style="margin-left:15px;" for="kpp">Keywords Per page</label>
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
<br/>
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


 </div>	<!-- End of Pagenavi-->	
 
 <!-- Main Table To show -->		
		<table id='kt-main' class='widefat'>
			<thead>
				<tr>
			
					<?php 
					echo '<th id="dif-th"><a href="" id="delete-all-kt"><img style="margin:7px ;" src="'. plugins_url( 'images/b_drop.png', __FILE__). '"/></a><input type="checkbox" id="check-all"/></th>';
					echo '<th>Delete</th>';
					foreach($table_heads as $head)echo "<th><a class='th-toggle' href=''>{$head}</a></th>"; 
					?>
				</tr>
			</thead>
			<tbody>
			
					<?php
					if($current_page > $total_pages)
						echo "<tr><td colspan=9 ><div class='updated'>Not too many Pages available. There are total {$total_pages} pages</div></td></tr>"; 
					foreach( $results as $result){
						echo '<tr>';
						echo '<td><input type="checkbox" class="checky"/></td>';
						echo '<td><a href="" class="delete-kt"><img src="'.plugins_url( 'images/b_drop.png', __FILE__).'"/></a></td>';
						$resultA = array_slice($result, 1, 18);
						$resultA= $this ->reverse_process_array($resultA);
						foreach ($resultA as $res)echo "<td>{$res}</td>";
						echo "<td>{$result[21]}</td>";
						echo '</tr>';
						
					}
					?>
		
			</tbody>

		</table>
