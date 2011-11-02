<?php
global $wpdb;
//$wpdb->query("alter table wp_kt_orders add column `key_ext` tinyint(1)  NOT NULL default 0");
$table_heads = array('Date', 'User', 'Amount', 'Price', 'Keywords', 'Extension', 'Push keywords');
$res = $wpdb->get_results('select * from wp_kt_orders order by date desc', 'ARRAY_A');
extract($res);
?>
<div class='wrap' style="margin-top:20px">
    <table class='widefat'>
        <thead>
            <tr>
                <?php foreach ($table_heads as $head)
                    echo "<th >{$head}</th>"; ?>
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
                $key_ext = ($key_ext == 1) ? 'Yes' : 'No';
                $kp_page =admin_url("tools.php?page=wpKeywordsTable&kt-push-user=$user");
                $all = $wpdb->get_results("SELECT keyword from wp_keywords_list where id in({$key_ids})", 'ARRAY_N');
                $ar = array();
                foreach ($all as $single) {
                    $ar[] = $single[0];
                }
                $string = implode(',', $ar);

                echo "<td>{$date}</td><td>{$user}</td> <td>{$num}</td><td>{$price}</td> <td>{$string}</td><td>$key_ext</td><td><a href=\"$kp_page\">Push keywords</a></td>";
                echo '</tr>';
            }
            ?>

        </tbody>
        <tfoot>
            <tr>
                <?php foreach ($table_heads as $head)
                    echo "<th>{$head}</th>"; ?>
            </tr>	
        </tfoot>
    </table>
</div>

