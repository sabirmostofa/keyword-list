<?php
global $wpdb;
$table_heads = array('Select', 'User', 'Aff Earning', 'Paid', 'Unpaid', 'Paypal email');
if( get_option('kt-settings-var') )extract( get_option('kt-settings-var'));

$all_users = $wpdb -> get_col("select user_id from wp_kt_affs");
if($all_users)
    $all_users = array_unique ($all_users);

    else{
    echo 'No affiliates earning recorded yet.';
    return;
    }
    var_dump($_POST);
    
    // Paying to the selected affiliates
    if(isset($_POST['users'])){
        $user_emails=array();
        $nvpstr = '';
        $common = array( 
                'METHOD' => 'MassPay',
                 'VERSION' => '65.1',
	'USER' => $api_user,
	'PWD' => $api_pas,
	'SIGNATURE' => $api_sig
	);
        
                $str = '';
	foreach ($common as $key=> $value)
		$str .= "&{$key}={$value}";
	$nvpstr = trim($str, '&');
        $j = 0;
        
        foreach($_POST['users'] as $user_id => $none):           
            $paypal_email = get_user_meta($user_id, 'kt-aff-paypal-email',true);        
             $incomes= $wpdb -> get_col("select aff_income from wp_kt_affs where user_id='$user_id'");
             $tot_income = array_sum($incomes);
             $paid = get_user_meta($user_id, 'kt_aff_paid',true);
             $paid = $paid?$paid :0;
             $unpaid = $tot_income - $paid;
             $note = urlencode( str_replace('[payment_amount]', '$'.$unpaid, $aff_mail_body));
             $uniqueID='';
             $nvpstr.="&L_EMAIL$j=$paypal_email&L_Amt$j=$unpaid&L_UNIQUEID$j=$uniqueID&L_NOTE$j=$note";
             $j++;
                       
        endforeach;
        
        $emailSubject=  urlencode($aff_mail_sub);
        $receiverType ='EmailAddress';
        $currency='USD';
        $nvpstr.="&EMAILSUBJECT=$emailSubject&RECEIVERTYPE=$receiverType&CURRENCYCODE=$currency" ;
    
        $response = $this -> hash_call($nvpstr);
        if($response['ACK'] == 'Success'){
                foreach($_POST['users'] as $user_id => $none):           
               
             $incomes= $wpdb -> get_col("select aff_income from wp_kt_affs where user_id='$user_id'");
             $tot_income = array_sum($incomes);
             $paid = get_user_meta($user_id, 'kt_aff_paid',true);
             $paid = $paid?$paid :0;
             $unpaid = $tot_income - $paid;
             $paid =update_user_meta($user_id, 'kt_aff_paid',$unpaid);
   
                       
        endforeach;
            
        }
     
    }
?>

<div class='wrap' style="margin-top:20px">
    <form action="" method="post">

    <table id='kt-main' class='widefat'>
        <thead>
            <tr>
                <?php foreach($table_heads as $head): ?>
                <th><?php  echo $a = ($head=='Select')?'<input type="checkbox" id="check-all"/>' :$head ; ?></th>
                <?php endforeach;  ?>
            </tr>
        </thead>
        <tbody>
                 <?php foreach($all_users as $user): 
                     $user_data = get_userdata($user);
                    $user_login = $user_data -> user_login;
                    $incomes= $wpdb -> get_col("select aff_income from wp_kt_affs where user_id='$user'");
                    $tot_income = array_sum($incomes);
                    $paid = get_user_meta($user, 'kt_aff_paid',true);
                    $paid = $paid?$paid :0;
                    $paypal_email = get_user_meta($user, 'kt-aff-paypal-email',true);
                    if(!$paypal_email)continue;
                     ?>
                <tr>
                    <td><input class="checky" type="checkbox" name="users[<?php echo $user  ?>]"/></td>
                    <td><?php echo $user_login ?></td>
                    <td><?php echo $tot_income ?></td>
                    <td><?php echo $paid ?></td>
                    <td><?php echo $tot_income - $paid ?></td>
                    <td><?php echo $paypal_email ?></td>
                </tr>
                <?php endforeach;  ?>
        </tbody>
    </table>
    <br/>
    <br/>
    <input class="button-primary" type="submit" value="Pay now">
    </form>
</div>
