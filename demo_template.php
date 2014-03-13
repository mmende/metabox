<?php /* Template Name: Demo template */ ?>

<?php get_header(); ?>

<?php
	//get the value
	$mail = get_post_meta( get_the_ID(), 'contact_email', true );
	//take care value is not empty
	if(empty($mail))
		$mail = "";
	//show value
	echo "<a href='$mail'>$mail</a>";
?>

<?php get_footer(); ?>