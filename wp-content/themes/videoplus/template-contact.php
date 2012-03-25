<?php
/*
Template Name: Contact
*/
?>

<?php 
if(isset($_POST['submitted'])) {
		if(trim($_POST['contactName']) === '') {
			$nameError = 'Please enter your name.';
			$hasError = true;
		} else {
			$name = trim($_POST['contactName']);
		}
		
		if(trim($_POST['email']) === '')  {
			$emailError = 'Please enter your email address.';
			$hasError = true;
		} else if (!eregi("^[A-Z0-9._%-]+@[A-Z0-9._%-]+\.[A-Z]{2,4}$", trim($_POST['email']))) {
			$emailError = 'You entered an invalid email address.';
			$hasError = true;
		} else {
			$email = trim($_POST['email']);
		}
			
		if(trim($_POST['comments']) === '') {
			$commentError = 'Please enter a message.';
			$hasError = true;
		} else {
			if(function_exists('stripslashes')) {
				$comments = stripslashes(trim($_POST['comments']));
			} else {
				$comments = trim($_POST['comments']);
			}
		}
			
		if(!isset($hasError)) {
			$emailTo = get_option('workspace_email');
			if (!isset($emailTo) || ($emailTo == '') ){
				$emailTo = get_option('admin_email');
			}
			$subject = '[Contact Form] From '.$name;
			$body = "Name: $name \n\nEmail: $email \n\nComments: $comments";
			$headers = 'From: '.$name.' <'.$emailTo.'>' . "\r\n" . 'Reply-To: ' . $email;
			
			mail($emailTo, $subject, $body, $headers);
			$emailSent = true;
		}
	
} ?>

<?php get_header(); ?>

        <div id="content">
			<h1 class="page-title"><?php the_title(); ?></h1>        
		    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
			    <div <?php post_class() ?> id="post-<?php the_ID(); ?>">
				<div class="entry-content">
					<?php the_content(); ?>
					<?php if(isset($emailSent) && $emailSent == true) { ?>
					
		                <div class="thanks">
		                    <p><?php _e('Thanks, your email was sent successfully. ', 'themejunkie') ?></p>
		                </div><!-- .thanks -->
		                
		            <?php } else { ?>
		
		                <?php if(isset($hasError) || isset($captchaError)) { ?>
		                    <p class="error"><?php _e('Sorry, an error occurred.', 'themejunkie') ?><p>
		                <?php } ?>
		
		                <form action="<?php the_permalink(); ?>" id="contact-form" method="post" class="clear">
		                    <ul class="contact-form">
		                        <li><label for="contactName"><?php _e('Name:', 'themejunkie') ?></label>
		                            <input class="txt" type="text" name="contactName" id="contactName" value="<?php if(isset($_POST['contactName'])) echo $_POST['contactName'];?>" class="required requiredField" />
		                            <?php if($nameError != '') { ?>
		                                <span class="error"><?php echo $nameError; ?></span>
		                            <?php } ?>
		                        </li>
		
		                        <li><label for="email"><?php _e('Email:', 'themejunkie') ?></label>
		                            <input class="txt" type="text" name="email" id="email" value="<?php if(isset($_POST['email']))  echo $_POST['email'];?>" class="required requiredField email" />
		                            <?php if($emailError != '') { ?>
		                                <span class="error"><?php echo $emailError; ?></span>
		                            <?php } ?>
		                        </li>
		
		                        <li class="textarea"><label for="commentsText"><?php _e('Message:', 'themejunkie') ?></label>
		                            <textarea name="comments" id="commentsText" rows="20" cols="30" class="required requiredField"><?php if(isset($_POST['comments'])) { if(function_exists('stripslashes')) { echo stripslashes($_POST['comments']); } else { echo $_POST['comments']; } } ?></textarea>
		                            <?php if($commentError != '') { ?>
		                                <p><span class="error"><?php echo $commentError; ?></span></p>
		                            <?php } ?>
		                        </li>
		
		                        <li>
		                            <input type="hidden" name="submitted" id="submitted" value="true" />
		                            <input name="submit" type="submit" id="submit" class="submit-button" value="<?php _e('Send Email', 'themejunkie') ?>" />
		                        </li>
		                    </ul>
		                </form>
		            <?php } ?>
				</div><!-- .entry-content -->
			</div><!-- #post-<?php the_ID(); ?> -->
			<?php endwhile; endif; ?>
		</div><!-- #content -->
		
<?php get_sidebar(); ?>   		
<?php get_footer(); ?>