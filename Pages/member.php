<?php
global $wpdb;
$table_heads = array('Date', 'User', 'Amount', 'Price', 'Keywords', 'Export CSV', 'View in Table');
extract(get_option('kt-settings-var'));
$current = wp_get_current_user();
$user_id = $current->ID;
if ($user_id == 0) {
    ?>
    <h4> You need to be registered to view this page</h4>
    <a style ='float:left;margin-left:20px;' href='<?php echo get_permalink($check_page); ?>'>Register here</a>
    <?php
    return;
}
$res = $wpdb->get_results("select * from wp_kt_orders where member_id ='$user_id' order  by date desc", 'ARRAY_A');
extract($res);
?>
<h1>Welcome To Your Member's Page</h1>
<br>

<a href='' id='changeP' style="clear:both;margin:15px;">Change Password</a>
<div id='changeIt' style="display:none">

    <form action ='' method='post'>
        Enter New Password:
        <input type="password" name="pass1"/>
        Repeat Password:
        <input type="password" name="pass2"/>
        <input type="submit" name='change-pass'/>
    </form>
</div>
<h3 style='float:left;font-size:18px'>Previous Transactions:</h3>
<a style ='float:left;margin-left:20px;' href='<?php echo get_permalink($key_page); ?>'>Buy More</a>
<a style ='float:left;margin-left:20px;' href='<?php echo get_permalink($check_page); ?>'>Checkout Page</a>
<div class='wrap' style="margin-top:20px;clear:both;">
    <div id="hideShow" style="display:none;margin:20px 0"></div>

    <table class='widefat'>
        <thead>
            <tr>
<?php foreach ($table_heads as $head)
    echo "<th style='text-align:left;padding:3px !important'>{$head}</th>"; ?>
            </tr>	
        </thead>
        <tbody>

<?php
foreach ($res as $single) {
    echo '<tr>';
    extract($single);
    $info = get_userdata($member_id);
    $user = $info->user_login;
    $keys = explode(',', $key_ids);
    $num = count($keys);
    $all = $wpdb->get_results("SELECT keyword from wp_keywords_list where id in({$key_ids})", 'ARRAY_N');
    $ar = array();
    foreach ($all as $single) {
        $ar[] = $single[0];
    }
    $string = implode(',', $ar);

    echo "<td>{$date}</td><td>{$user}</td> <td>{$num}</td><td>{$price}</td> <td>{$string}</td>
					<td class='{$id}'><a class='export-csv' href=\"\">Export csv</a></td><td class='{$id}'><a class='view-table' href=''>view in table</a></td>";
    echo '</tr>';
}
?>

        </tbody>
        <tfoot>
            <tr>
<?php foreach ($table_heads as $head)
    echo "<th style='text-align:left;padding:3px !important'>{$head}</th>"; ?>
            </tr>	
        </tfoot>
    </table>
    
    <!-- Pushed Keywords -->
    <h2>Bonus keywords:</h2>
    <?php 
    $keys = get_user_meta($user_id, 'kt-pushed-keys', true);
    if($keys)include 'simple-table-pushed.php';
    else echo 'None';
    
    ?>
    
<?php
// Genreating aff vars


$ref_user_count = (get_user_meta($user_id, 'kt-aff-users', true)) ? count(get_user_meta($user_id, 'kt-aff-users', true)) : 0;
$incomes = $wpdb->get_col("SELECT aff_income FROM wp_kt_affs where  user_id='$user_id'");

$total_income = ($incomes) ? array_sum($incomes) : 0;
if (isset($_POST['kt_paypal_email']))
    update_user_meta($user_id, 'kt-aff-paypal-email', $_POST['kt_paypal_email']);
$paypal_email = get_user_meta($user_id, 'kt-aff-paypal-email', true);
$user_paypal_email = $paypal_email ? $paypal_email : '';
$paid = get_user_meta($user_id, 'kt_aff_paid', true) ? get_user_meta($user_id, 'kt_aff_paid', true) : 0;

$now = getdate();
$months = array('January', 'Ferbruary', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
$this_month = $now['month'];

$last_month = (($now['mon'] - 2) < 0) ? $months[10 - ($now['mon'] - 2)] : $months [$now['mon'] - 2];
$last_last_month = (($now['mon'] - 3) < 0) ? $months[10 - ($now['mon'] - 3)] : $months [$now['mon'] - 3];

$this_month_ts = time() - ($now['mday'] * 3600 * 24 + $now['hours'] * 3600 + $now['minutes'] * 60 + $now['seconds']);
$last_month_ts = $this_month_ts - (30 * 24 * 3600);
$last_last_month_ts = $this_month_ts - (2 * 30 * 24 * 3600);

$this_month_income = $wpdb -> get_results("SELECT wp_kt_affs.aff_income from wp_kt_affs inner join wp_kt_orders on wp_kt_affs.order_id=wp_kt_orders.id  where wp_kt_affs.user_id =$user_id and wp_kt_orders.date >$this_month_ts",'ARRAY_N');
$this_month_income=(empty ($this_month_income))?0:array_sum( $this_month_income[0]);;

$last_month_income = $wpdb -> get_results("SELECT wp_kt_affs.aff_income from wp_kt_affs inner join 
        wp_kt_orders on wp_kt_affs.order_id=wp_kt_orders.id  
        where wp_kt_affs.user_id =$user_id  and wp_kt_orders.date >$last_month_ts and 
        wp_kt_orders.date <$this_month_ts",
        'ARRAY_N');
        
$last_month_income = (empty ($last_month_income))?0:array_sum( $last_month_income[0]);;


$last_last_month_income = $wpdb -> get_results("SELECT wp_kt_affs.aff_income from wp_kt_affs inner join 
        wp_kt_orders on wp_kt_affs.order_id=wp_kt_orders.id  where wp_kt_affs.user_id =$user_id 
        and wp_kt_orders.date >$last_month_ts and wp_kt_orders.date <$last_last_month_ts",
        'ARRAY_N');

$last_last_month_income = (empty ($last_last_month_income))?0:array_sum( $last_last_month_income[0]);;
?>
    <h2>Affiliate Status:</h2>
    <p> Your affiliate link is <span> <?php echo site_url() . '/affiliates/' . $current->user_login ?></span></p>

    <h2>Referred Users:</h2>
    You have Referred <?php echo $ref_user_count ?> Users;

    <h2>Affiliate Earning:</h2>
    Total Amount:  $<?php echo $total_income ?><br/> 
    Paid:$<?php echo $paid; ?>
    <br/>
   <h2> Earning by Month:</h2>
    <b><?php echo $this_month; ?>:</b> <?php echo '$',$this_month_income; ?><br/>
    <b><?php echo $last_month; ?>:</b> <?php echo '$',$last_month_income; ?><br/>
    <b><?php echo $last_last_month; ?>:</b> <?php echo '$',$last_last_month_income; ?>
    
    <h2>Paypal Account Email:</h2>
    <form method="post" action="">
        <input type="text" name="kt_paypal_email" value="<?php echo $user_paypal_email ?>"/>
        <input type="submit" value="submit"/>
    </form>
    


</div>

