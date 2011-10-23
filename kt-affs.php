<?php
global $wpdb;
$table_heads = array('Select', 'User', 'Aff Earning', 'Paid', 'Unpaid', 'Paypal email');

$all_users = $wpdb -> get_col("select user_id from wp_kt_affs");
if($all_users)
    $all_users = array_unique ($all_users);

    else{
    echo 'No affiliates earning recorded yet.';
    return;
    }
    var_dump($_POST);
    
    // Paying to the selected affiliates
    if(isset($_POST['users']))
        foreach($_POST['users'] as $user_id => $none):
            
            
        endforeach;
    

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
    
    <input type="submit" value="Pay now">
    </form>
</div>
