<?php

include 'header';

?>
<div class="announce">
    <h1>This Is Defollower</h1>
    <p>And this is the list of the tweeps you follow that have <b>not</b> said anything in at least 30 days. 
    Why does that matter? To some it may not. To others, Twitter is an peer based ecosystem in which we rely on
    our relationships to lead us to new ones. Recommendation engines may peer into our following lists and use
    that data to recommend that others follow the same users that we are already following. Imagine the confusion
    when a recommended followee appears that hasn't uttered a character, let alone 140, in months.</p>
</div>
<div class="old_friends">
    <?php echo $unfollow_list; ?>
</div>
<script src="//platform.twitter.com/widgets.js" charset="utf-8"></script>
<?php include 'views/footer_postauth.php';