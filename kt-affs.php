<?php
global $wpdb;
$table_heads = array('Select', 'User', 'Aff_earning', 'Paid', 'Unpaid');

$all_users = $wpdb -> get_col("select user_id from wp_kt_affs");
if($all_users)
    $all_users = array_unique ($all_users);

    else{
    echo 'No affiliates earning recorded yet.';
    return;
    }

?>

<div class='wrap' style="margin-top:20px">
    <table id='kt-main' class='widefat'>
        <thead>
            <tr>
                <?php foreach($table_heads as $head): ?>
                <th><?php  echo $a = ($head=='Select')?'<input type="checkbox" id="aff-select-all"/>' :$head ; ?></th>
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
                     ?>
                <tr>
                    <td><input type="checkbox" name="<?php ?>"/></td>
                    <td><?php echo $user_login ?></td>
                    <td><?php echo $tot_income ?></td>
                    <td><?php echo $paid ?></td>
                    <td><?php echo $tot_income - $paid ?></td>
                </tr>
                <?php endforeach;  ?>
        </tbody>
    </table>
</div>
