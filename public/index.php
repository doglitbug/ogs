<?php
global $db;
require_once('../private/initialize.php');

$page_title = 'Online Garage Sale';
include(SHARED_PATH . '/public_header.php');
?>

<h1><?php echo $page_title; ?></h1>
<div class="cta">
    <a class="btn btn-primary action"
       href="<?php echo url_for('/garage/index.php'); ?>">Garages</a>

    <a class="btn btn-primary action"
       href="<?php echo url_for('/item/index.php'); ?>">Items</a>

    <?php if (is_logged_in()) {?>
        <a class="btn btn-primary action"
           href="<?php echo url_for('/user/show.php'); ?>">User Details</a>

    <?php } ?>
</div>
<p>Welcome to the Online Garage Sale, an idea born of necessity and disappointment!</p>

<p>Necessity and disappointment?</p>
<p>
    Well yes, we all have a pile of stuff that we have been meaning to put up on online for sale, or for a good
    old-fashioned yard sale, but we never get around to it!<br/>
    And disappointment? Well our local auction site is all drop shippers and businesses now, no hope of competing, or
    finding stuff second hand without wading through all the business listings<br/>
    Wanted to buy something local, second hand and fast. And what do I see there...23 day lead time from our factory in
    China. That's great for what was a New Zealand second hand website...<br/>
    So the online garage sale was born, a place for people to buy, sell or exchange their secondhand/homemade goods without
    battling Big International Businesses for visibility!
</p>
<p>
    Local individuals, maker spaces, and op shops are all welcome here!
</p>
<p>Any questions, comments, bugs etc, please contact onlinegaragesale1234(at)gmail.com</p>
<?php include(SHARED_PATH . '/public_footer.php'); ?>

