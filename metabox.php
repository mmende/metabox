<?php
/**
 * @brief Adds a metabox to the editing area of wordpress
 * @details Adds a  metabox with one or multiple metafields to the editing area of wordpress
 * 
 * @param  key value array with the parameters
 * 
 * @author Martin Mende, martin.mende(at)aristech.de
 * @version 1.0.0
 * @copyright GNU Public License
 */
class metabox{
	//required
	var $id;
	var $title;
	var $metafields;
	//optional
	var $template_file;
	var $post_type;
	var $context;
	var $priority;

	//some extra stuff
	var $nonce;

	public function __construct(array $arguments = array()){
		//write default values
		$this->template_file = false;
		$this->post_type = "page";
		$this->context = 'advanced';
		$this->priority = 'default';
		//write arguments to member vars
		foreach($arguments as $key => $value){
			$this->$key = $value;
		}
		$this->nonce = $this->id."_nonce";
		//if metafields is a metafield object move it into an array - this simplifies the access of this parameter later
		$this->metafields = (is_array($this->metafields)) ? $this->metafields : array($this->metafields);
		//hook to load-page and load-page-new
		add_action('load-page.php', array($this, '_setup_meta_box'));
		add_action('load-page-new.php', array($this, '_setup_meta_box'));
	}//-__construct

	public function _setup_meta_box(){
		//check if metabox should only appear at specific template_file
		if($this->template_file!==false){
			$post_id = $_GET['post'] ? $_GET['post'] : $_POST['post_ID'] ;
			$template_file = get_post_meta($post_id,'_wp_page_template',TRUE);
			if ($template_file == $this->template_file) {
				add_meta_box($this->id, $this->title, array($this, '_meta_box'), $this->post_type, $this->context, $this->priority);
				add_action( 'save_post', array($this, '_save_metabox'), 10, 2);
			}
		}else{
			add_meta_box($this->id, $this->title, array($this, '_meta_box'), $this->post_type, $this->context, $this->priority);
			add_action( 'save_post', array($this, '_save_metabox'), 10, 2);
		}
	}//-_setup_meta_box

	public function _meta_box($object, $box){
		wp_nonce_field( basename( __FILE__ ), $this->nonce);

		//add the fields
		foreach($this->metafields as $metafield){
			$metafield->get_html($object, $box);
		}
	}//-_meta_box

	public function _save_metabox($post_id, $post){
		//Verify the nonce before proceeding
		if (!isset( $_POST[$this->nonce] ) || !wp_verify_nonce($_POST[$this->nonce], basename( __FILE__ )))
			return $post_id;

		//get the post type object
		$post_type = get_post_type_object( $post->post_type );

		//check current user permission
		if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
			return $post_id;

		//handle save for each field
		foreach($this->metafields as $metafield){
			//get the posted data //and sanitize it for use as an HTML class
			$new_meta_value = ( isset( $_POST[$metafield->getId()] ) ? $_POST[$metafield->getId()] : '');//sanitize_html_class( $_POST[$this->id] ) : '' );

			//get the meta value of the custom field key
			$meta_value = get_post_meta($post_id, $metafield->getId(), true);

			//if a new meta value was added and there was no previous value, add it
			if ( $new_meta_value && '' == $meta_value )
				add_post_meta( $post_id, $metafield->getId(), $new_meta_value, true );

			//if the new meta value does not match the old value, update it
			elseif ( $new_meta_value && $new_meta_value != $meta_value )
				update_post_meta( $post_id, $metafield->getId(), $new_meta_value );

			//if there is no new meta value but an old value exists, delete it
			elseif ( '' == $new_meta_value && $meta_value )
				delete_post_meta( $post_id, $metafield->getId(), $meta_value );
		}
	}//-_save_metabox
};//-metabox class

/**
 * @brief Creates a metafield to use in the metabox class
 * 
 * @param  key value array with the parameters
 * 
 * @author Martin Mende, martin.mende(at)aristech.de
 * @version 1.0.0
 * @copyright GNU Public License
 */
class metafield{
	//required
	var $id;
	var $title;
	//optional
	var $type;
	var $description;
	var $description_long;

	public function __construct(array $arguments = array()){
		//write default values
		$this->type = 'input';
		$this->description = '';
		$this->description_long = '';
		//write arguments to member vars
		foreach($arguments as $key => $value){
			$this->$key = $value;
		}
	}//-__construct

	public function get_html($object, $box){
		?>
		<p><strong><?php echo $this->title; ?></strong></p>
		<label class="screen-reader-text" for="<?php echo $this->id; ?>"><?php echo $this->title; ?></label>
		<?php
		switch ($this->type):
			case "input":
		?>
		<input type="text" name="<?php echo $this->id; ?>" id="<?php echo $this->id; ?>" value="<?php echo esc_attr(get_post_meta($object->ID, $this->id, true)); ?>" size="30" />
		<?php
				break;
			case "textarea":
		?>
		<textarea class="widefat" name="<?php echo $this->id; ?>" id="<?php echo $this->id; ?>" size="30" rows="7"><?php echo esc_attr(get_post_meta($object->ID, $this->id, true)); ?></textarea>
		<?php
				break;
		endswitch;
	}//-get_html

	public function getId(){
		return $this->id;
	}//-getId
};//-metafield class

?>