<?php
if(isset($_POST['kt-coupon-submit'])){
    if(isset($_POST['kt-coupon']) && isset($_POST['kt-coupon-percen'])){
        $coupons = get_option('kt_all_coupons');
        if(!$coupons)$coupons = array();
        $coupons[]=array( trim($_POST['kt-coupon']), trim($_POST['kt-coupon-percen']) );
        update_option('kt_all_coupons', $coupons);
    }
}
$table_heads = array('Coupon', 'Percentage');

$coupons = get_option('kt_all_coupons');
?>
<div class="wrap">
    <br/>
    <br/>
    <form action="" method="post">
    Coupon Code:
<input type="text" name="kt-coupon"/>
    <br/>
    <br/>
    Percentage:
<input type="text" name="kt-coupon-percen"/>
  <br/>
    <br/>
<input class ="button-primary" type="submit" value="Submit" name="kt-coupon-submit"/>
    </form> 
      <br/>
    <br/>
      <br/>
    <br/>
    <table class="widefat">
        <thead>
            <tr>
                <?php
                echo '<th id="dif-th"><a href="" id="delete-all-coupons"><img style="margin:7px ;" src="' . plugins_url('images/b_drop.png', __FILE__) . '"/></a><br/><input type="checkbox" id="check-all"/></th>';
                echo '<th>Delete</th>';
                foreach ($table_heads as $head)
                    echo "<th>{$head}</th>";
                ?>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($coupons):
                foreach ($coupons as $coupon):
                    echo '<tr>';
                    echo '<td><input type="checkbox" class="checky"/></td>';
                    echo '<td><a href="" class="delete-kt-coupon"><img src="' . plugins_url('images/b_drop.png', __FILE__) . '"/></a></td>';
                  echo "<td>$coupon[0]</td>";
                  echo "<td>$coupon[1]</td>";
           

                endforeach;
            endif;
            ?>
        </tbody>
    </table>
</div>