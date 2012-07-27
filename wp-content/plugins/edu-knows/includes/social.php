<?php
$if_social_on = get_option('tn_edufaq_mini_social');
if(($if_social_on == 'yes') || ($if_social_on == '')) {   ?>

<div class="post-social">

<p class="emailtofriend"><a href="mailto:your@friend.com?subject=check this post - <?php the_title(); ?>&amp;body=<?php the_permalink(); ?> " rel="nofollow">&nbsp;</a></p>

<p class="reddit"><a href="http://reddit.com/submit?url=<?php the_permalink(); ?>&amp;title=<?php the_title(); ?>" rel="external nofollow">&nbsp;</a></p>

<p class="yahooweb"><a href="http://myweb2.search.yahoo.com/myresults/bookmarklet?u=<?php the_permalink(); ?>&amp;=<?php the_title(); ?>" rel="external nofollow">&nbsp;</a></p>

<p class="facebook"><a href="http://www.facebook.com/share.php?u=<?php the_permalink(); ?>" rel="external nofollow">&nbsp;</a></p>

<p class="digg"><a href="http://digg.com/submit?phase=2&amp;url=<?php the_permalink(); ?>&amp;title=<?php the_title(); ?>" rel="external nofollow">&nbsp;</a></p>

<p class="delicious"><a href="http://del.icio.us/post?url=<?php the_permalink(); ?>&amp;title=<?php the_title(); ?>" rel="external nofollow">&nbsp;</a></p>

<p class="stumble"><a href="http://www.stumbleupon.com/submit?url=<?php the_permalink(); ?>&amp;title=<?php the_title(); ?>" rel="external nofollow">&nbsp;</a></p>

<p class="tech"><a href="http://technorati.com/faves?add=<?php the_permalink(); ?>" rel="external nofollow">&nbsp;</a></p>

</div>

<?php } ?>