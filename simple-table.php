<?php
global $wpdb, $wpKeywordsTable;
$table_heads=array('Keyword',   'Global Monthly Searches', 'Local Monthly Searches', 'CPC', 'Search Results', 'Actual Results', 'Average PR', 'Competition', 'Ads Displayed', 'Keyword Rating', 'Category','Price','.COM','.NET','.ORG'); 
		$result = $wpdb->get_results( "SELECT * FROM wp_kt_orders where  id='$id'", 'ARRAY_A' );
		extract($result[0]);
	$all_res = $wpdb->get_results("SELECT * from wp_keywords_list where id in({$key_ids})", 'ARRAY_N');

		
?>
		
<!-- Table -->
<table id='kt-main' class='widefat'>
	<thead>
		<tr>
			<?php 
			foreach($table_heads as $key => $head){			

			
			echo "<th>{$head}</th>";
			
			}
			?>
		</tr>
	</thead>
	<tbody>
	
	<?php
	
	foreach( $all_res as $result){
		echo '<tr>';
		$class = 'm' . $result[0];		
		$result = array_slice($result, 1, 18);
		$result = $wpKeywordsTable -> reverse_process_array($result);
		unset($result[10]);
        unset($result[8]);
		unset($result[6]);
		foreach ($result as $res)echo "<td class=\"{$class}\">{$res}</td>";
		echo '</tr>';
		
	}
	?>

</tbody>

	<tfoot>
		<tr>
	<?php 
			foreach($table_heads as $key => $head){			
				echo "<th>{$head}</th>"; 
			}
			?>
		</tr>
	</tfoot>

</table>
