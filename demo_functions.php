<?php
/**
 * The metabox class makes it pretty easy to setup a custom metabox with one or multiple fields.
 * The easiest way is to create some metafields like so:
 */

//include the classes
include("metabox.php");

//create a phone metafield
$contact_phone = new metafield(array(
	"id" => "contact_phone", //required, this one specifies the id you'll later use to fetch the value
	"title" => "Your phone number", //required, this one specifies the title for the metafield
	"type" => "input", //optional, currently only textarea and input (default) are implemented
	"description" => "This number will be displayd somewhere", //optional, a short description for the field, note: not yet implemented
	"description_long" => "The number will appear in the contact template at the bottom", //optional, a long description, note: not implemented yet
));

//and another one
$contact_email = new metafield(array(
	"id" => "contact_email",
	"title" => "Your email address"
));

/**
 * No that we have our fields we want to add it to a metabox:
 */

//create the metabox to contain the fields
$contact_metabox = new metabox(array(
	"id" => "contact_metabox", //required, some id for your box
	"title" => "Contact options", //required, the header text for your metabox
	"metafields" => array($contact_phone, $contact_email), //required, that's where we actually add the fields as an array of metafields or as a single metafield
	"template_file" => "demo_template.php", 	//optional, you can set this to a specific template filename to constrain the metabox to posts of this template type,
												//don't pass this parameter to show it to all template types 
												//note: currently you will have to save the post with this template type to let the box appear
	"post_type" => "page", //optional, on which type of post to display the metabox (page is default)
	"context" => "advanced", //optional, the part of the page where the edit screen section should be shown
	"priority" => "default", //optional, the priority within the context where the boxes should show (default is default)
));

/**
 * Perfect! Now the box should appear with the two fields when editing a page.
 * To fetch the values in your template files you can use the standard approach to get metas (see demo_template.php)
 * Voilà that's it.
 */
 ?>

