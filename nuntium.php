<?php

/*

Plugin Name: Nuntium Light

Plugin URI:  http://xcedo.com/nuntium

Description: Newsletter Generator for Wordpress. Create clean, modern and awesome newsletters using these eye-catching newsletter themes dynamically fed from your Wordpress site. <strong>Get the PRO version for more themes and features.</strong>

Version:     0.1.5

Author:      xcedo studio

Author URI:  http://xcedo.com/nuntium

License:     GPL2

License URI: https://www.gnu.org/licenses/gpl-2.0.html

Text Domain: Nuntium Light

*/

// Register Nuntium Panel //
function register_nuntium() {
$labels = array( 
'name' => _x( 'Newsletter', 'nuntium' ),
'singular_name' => _x( 'Newsletter', 'nuntium' ),
'search_items' => _x( 'Search Issues', 'nuntium' ),
'all_items' => _x( 'All Issues', 'nuntium' ),
'parent_item' => _x( 'Parent Newsletter', 'nuntium' ),
'parent_item_colon' => _x( 'Parent Newsletter:', 'nuntium' ),
'edit_item' => _x( 'Edit Newsletter Issue', 'nuntium' ),
'update_item' => _x( 'Update Newsletter Issue', 'nuntium' ),
'add_new_item' => _x( 'Add Newsletter Issue', 'nuntium' ),
'new_item_name' => _x( 'New Newsletter Issue', 'nuntium' ),
'menu_name' => _x( 'Newsletter Issues', 'nuntium' ),
);

$args = array(
'hierarchical' => true,		  
'labels' => $labels,
'public' => true,
'show_ui' => true,
'show_in_nav_menus' => true,
'show_admin_column' => true, 
'rewrite' => true,
'query_var' => true
);
register_taxonomy( 'newsletter', array('post'), $args );
}
add_action( 'init', 'register_nuntium' );

// Remove old Nuntium Meta Box //
function nuntium_meta_box_remove() {
	$id = 'newsletterdiv'; // you can find it in a page source code (Ctrl+U)
	$post_type = 'post'; // remove only from post edit screen
	$position = 'side';
	remove_meta_box( $id, $post_type, $position );
}
add_action( 'admin_menu', 'nuntium_meta_box_remove');
// End Remove old Nuntium Meta Box //

// Register Nuntium Meta Box //
function add_nuntium_metabox(){
	$id = 'nuntiumdiv-post_tag';
	$heading = 'Add to Newsletter';
	$callback = 'nuntium_metabox_content';
	$post_type = 'post';
	$position = 'side';
	$pri = 'high';
	add_meta_box( $id, $heading, $callback, $post_type, $position, $pri );
}
add_action( 'admin_menu', 'add_nuntium_metabox');

function nuntium_metabox_content($post) {  
	// get all blog post tags as an array of objects
	$all_tags = get_terms( array('taxonomy' => 'newsletter', 'hide_empty' => 0) ); 
 
	// get all tags assigned to a post
	$all_tags_of_post = get_the_terms( $post->ID, 'newsletter' );  
 
	// create an array of post tags ids
	$ids = array();
	if ( $all_tags_of_post ) {
		foreach ($all_tags_of_post as $tag ) {
			$ids[] = $tag->term_id;
		}
	}

	echo '<div id="taxonomy-newsletter-box" class="wp-tab-panel">';
	echo '<input type="hidden" name="tax_input[newsletter][]" value="0" />';
	echo '<ul>';
	foreach( $all_tags as $tag ){
		// unchecked by default
		$checked = "";
		// if an ID of a tag in the loop is in the array of assigned post tags - then check the checkbox
		if ( in_array( $tag->term_id, $ids ) ) {
			$checked = " checked='checked'";
		}
		$id = 'newsletter-' . $tag->term_id;
		echo "<li id='{$id}'>";
		echo "<label><input type='checkbox' name='tax_input[newsletter][]' id='in-$id'". $checked ." value='$tag->term_id' /> $tag->name</label><br />";
		echo "</li>";
	}
	echo '</ul></div>';
}
// End Register Nuntium Meta Box //

// Start Nuntium Sort Order Meta Box //
function meta_box_sortarticle($object)

{

wp_nonce_field(basename(__FILE__), "meta-box-nonce");

?>

<div>

<label for="meta-sortarticle">Sort Order</label>

<select name="meta-sortarticle">

<?php 

$option_values = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10);

foreach($option_values as $key => $value) 

{

if($value == get_post_meta($object->ID, "meta-sortarticle", true)){?><option selected><?php echo $value; ?></option><?php }

else { ?><option><?php echo $value; ?></option><?php }

}

?>

</select> 

</div>

<?php  

}

function add_custom_meta_box(){add_meta_box("demo-meta-box", "Sort Newsletter Article", "meta_box_sortarticle", "post", "side", "high", null);}

add_action("add_meta_boxes", "add_custom_meta_box");

function save_meta_box_article($post_id, $post, $update)

{

if (!isset($_POST["meta-box-nonce"]) || !wp_verify_nonce($_POST["meta-box-nonce"], basename(__FILE__))) return $post_id;

if(!current_user_can("edit_post", $post_id)) return $post_id;

if(defined("DOING_AUTOSAVE") && DOING_AUTOSAVE) return $post_id;

$slug = "post";

if($slug != $post->post_type) return $post_id;

$meta_box_dropdown_value = "";

if(isset($_POST["meta-sortarticle"])){$meta_box_dropdown_value = $_POST["meta-sortarticle"];}   

update_post_meta($post_id, "meta-sortarticle", $meta_box_dropdown_value);

}

add_action("save_post", "save_meta_box_article", 10, 3);
// End Nuntium Sort Order Meta Box //

// Start Add Sort Order column to Post list //
add_filter( 'manage_edit-post_columns', 'add_sortarticle_columns');

function add_sortarticle_columns( $columns ) {

$column_meta = array( 'meta' => 'Sort Order' );

$columns = array_slice( $columns, 0, 6, true ) + $column_meta + array_slice( $columns, 2, NULL, true );

return $columns;

}

add_action( 'manage_posts_custom_column' , 'sortarticle_columns' );

function sortarticle_columns( $column ) {

global $post;

switch ( $column ) {

case 'meta':

$metaData = get_post_meta( $post->ID, 'meta-sortarticle', true );

echo $metaData;

break;

}

}

function register_sortable_columns( $columns ) {$columns['meta'] = 'Sort Order'; return $columns;}

add_filter( 'manage_edit-post_sortable_columns', 'register_sortable_columns' );

if ( ! function_exists( 'nuntium_entry_meta' ) ) : function nuntium_entry_meta() {$tag_list = get_the_tag_list( '', __( ', ', '' ) ); $utility_text = __( 'Tags: %1$s | ', '' ); printf($utility_text,$tag_list);} endif;

add_action('admin_menu', 'nuntium_menu_page');

function nuntium_menu_page() {add_menu_page( 'Newsletter', 'Newsletter', 'manage_options', 'edit-tags.php?taxonomy=newsletter', '', 'dashicons-welcome-widgets-menus', 7 );}

$term = get_term( 'newsletter' );

function fc_newsletter_edit_field( $term ){

$term_id = $term->term_id;

$currentTheme = get_option( "taxonomy_".$term_id );	

$directory = plugin_dir_path( __FILE__ ).'/templates/';

$files = glob($directory . 'nuntium*.php');

if ( $files !== false ){$themescount = count( $files );} else{$themescount = 10;}	

?>

<tr class="form-field">

<th scope="row">

<label for="NewsletterTheme"><?php echo esc_html_e('Newsletter Theme') ?></label>

<td align="center" valign="top">

<div style="width:100%; float:left; margin:0 0 20px 0;">	

<div style="width:100px; float:left; margin:5px; padding:5px; box-sizing:border-box; text-align:center; border:1px solid #ccc;">
<p style="text-transform:capitalize;">Free Theme</p>
<img style="max-width:100%; max-height:150px; overflow:hidden;" src="<?php echo plugin_dir_url( __FILE__ );?>/templates/images/nuntium.jpg" /><br />
<input type="radio" name="newsletterTheme" id="newsletterTheme" value="nuntium" checked >
</div>             	

<div style="width:100px; float:left; margin:5px; padding:5px; box-sizing:border-box; text-align:center; border:1px solid #ccc;">

<p style="text-transform:capitalize;">Premium Theme</p>

<a href="http://enews.xcedo.com/newsletter/newsletter-1" target="_blank"><img style="max-width:100%; max-height:150px; overflow:hidden;" src="<?php echo plugin_dir_url( __FILE__ );?>/templates/images/nuntium1.jpg" alt="See theme demo" title="See theme demo" /></a>

</div>                	

<div style="width:100px; float:left; margin:5px; padding:5px; box-sizing:border-box; text-align:center; border:1px solid #ccc;">

<p style="text-transform:capitalize;">Premium Theme</p>

<a href="http://enews.xcedo.com/newsletter/newsletter-2" target="_blank"><img style="max-width:100%; max-height:150px; overflow:hidden;" src="<?php echo plugin_dir_url( __FILE__ );?>/templates/images/nuntium2.jpg" alt="See theme demo" title="See theme demo" /></a>

</div>                	

<div style="width:100px; float:left; margin:5px; padding:5px; box-sizing:border-box; text-align:center; border:1px solid #ccc;">

<p style="text-transform:capitalize;">Premium Theme</p>

<a href="http://enews.xcedo.com/newsletter/newsletter-3" target="_blank"><img style="max-width:100%; max-height:150px; overflow:hidden;" src="<?php echo plugin_dir_url( __FILE__ );?>/templates/images/nuntium3.jpg" alt="See theme demo" title="See theme demo" /></a>

</div>

<div style="width:100px; float:left; margin:5px; padding:5px; box-sizing:border-box; text-align:center; border:1px solid #ccc;">

<p style="text-transform:capitalize;">Premium Theme</p>

<a href="http://enews.xcedo.com/newsletter/newsletter-5" target="_blank"><img style="max-width:100%; max-height:150px; overflow:hidden;" src="<?php echo plugin_dir_url( __FILE__ );?>/templates/images/nuntium5.jpg" alt="See theme demo" title="See theme demo" /></a>

</div>

<div style="width:100px; float:left; margin:5px; padding:5px; box-sizing:border-box; text-align:center; border:1px solid #ccc;">

<p style="text-transform:capitalize;">Premium Theme</p>

<a href="http://enews.xcedo.com/newsletter/newsletter-6" target="_blank"><img style="max-width:100%; max-height:150px; overflow:hidden;" src="<?php echo plugin_dir_url( __FILE__ );?>/templates/images/nuntium6.jpg" alt="See theme demo" title="See theme demo" /></a>

</div>

<div style="width:100px; float:left; margin:5px; padding:5px; box-sizing:border-box; text-align:center; border:1px solid #ccc;">

<p style="text-transform:capitalize;">Premium Theme</p>

<a href="http://enews.xcedo.com/newsletter/newsletter-7" target="_blank"><img style="max-width:100%; max-height:150px; overflow:hidden;" src="<?php echo plugin_dir_url( __FILE__ );?>/templates/images/nuntium7.jpg" alt="See theme demo" title="See theme demo" /></a>

</div>

<div style="width:100px; float:left; margin:5px; padding:5px; box-sizing:border-box; text-align:center; border:1px solid #ccc;">

<p style="text-transform:capitalize;">Premium Theme</p>

<a href="http://enews.xcedo.com/newsletter/newsletter-8" target="_blank"><img style="max-width:100%; max-height:150px; overflow:hidden;" src="<?php echo plugin_dir_url( __FILE__ );?>/templates/images/nuntium8.jpg" alt="See theme demo" title="See theme demo" /></a>

</div>

<div style="width:100px; float:left; margin:5px; padding:5px; box-sizing:border-box; text-align:center; border:1px solid #ccc;">

<p style="text-transform:capitalize;">Premium Theme</p>

<a href="http://enews.xcedo.com/newsletter/newsletter-9" target="_blank"><img style="max-width:100%; max-height:150px; overflow:hidden;" src="<?php echo plugin_dir_url( __FILE__ );?>/templates/images/nuntium9.jpg" alt="See theme demo" title="See theme demo" /></a>

</div>

<div style="width:100px; float:left; margin:5px; padding:5px; box-sizing:border-box; text-align:center; border:1px solid #ccc;">

<p style="text-transform:capitalize;">Premium Theme</p>

<a href="http://enews.xcedo.com/newsletter/newsletter-10" target="_blank"><img style="max-width:100%; max-height:150px; overflow:hidden;" src="<?php echo plugin_dir_url( __FILE__ );?>/templates/images/nuntium10.jpg" alt="See theme demo" title="See theme demo" /></a>

</div>

<div style="width:100px; float:left; margin:5px; padding:5px; box-sizing:border-box; text-align:center; border:1px solid #ccc;">

<p style="text-transform:capitalize;">Premium Theme</p>

<a href="http://enews.xcedo.com/newsletter/newsletter-11" target="_blank"><img style="max-width:100%; max-height:150px; overflow:hidden;" src="<?php echo plugin_dir_url( __FILE__ );?>/templates/images/nuntium11.jpg" alt="See theme demo" title="See theme demo" /></a>

</div>

<div style="width:100px; float:left; margin:5px; padding:5px; box-sizing:border-box; text-align:center; border:1px solid #ccc;">

<p style="text-transform:capitalize;">Premium Theme</p>

<a href="http://enews.xcedo.com/newsletter/newsletter-12" target="_blank"><img style="max-width:100%; max-height:150px; overflow:hidden;" src="<?php echo plugin_dir_url( __FILE__ );?>/templates/images/nuntium12.jpg" alt="See theme demo" title="See theme demo" /></a>

</div>

<div style="width:100px; float:left; margin:5px; padding:5px; box-sizing:border-box; text-align:center; border:1px solid #ccc;">

<p style="text-transform:capitalize;">Premium Theme</p>

<a href="http://enews.xcedo.com/newsletter/newsletter-13" target="_blank"><img style="max-width:100%; max-height:150px; overflow:hidden;" src="<?php echo plugin_dir_url( __FILE__ );?>/templates/images/nuntium13.jpg" alt="See theme demo" title="See theme demo" /></a>

</div>

<div style="width:100px; float:left; margin:5px; padding:5px; box-sizing:border-box; text-align:center; border:1px solid #ccc;">

<p style="text-transform:capitalize;">Premium Theme</p>

<a href="http://enews.xcedo.com/newsletter/newsletter-14" target="_blank"><img style="max-width:100%; max-height:150px; overflow:hidden;" src="<?php echo plugin_dir_url( __FILE__ );?>/templates/images/nuntium14.jpg" alt="See theme demo" title="See theme demo" /></a>

</div>

<div style="width:100px; float:left; margin:5px; padding:5px; box-sizing:border-box; text-align:center; border:1px solid #ccc;">

<p style="text-transform:capitalize;">Premium Theme</p>

<a href="http://enews.xcedo.com/newsletter/newsletter-15" target="_blank"><img style="max-width:100%; max-height:150px; overflow:hidden;" src="<?php echo plugin_dir_url( __FILE__ );?>/templates/images/nuntium15.jpg" alt="See theme demo" title="See theme demo" /></a>

</div>

<div style="width:100px; float:left; margin:5px; padding:5px; box-sizing:border-box; text-align:center; border:1px solid #ccc;">
<p style="text-transform:capitalize;">Premium Theme</p>
<a href="http://enews.xcedo.com/newsletter/newsletter-16" target="_blank"><img style="max-width:100%; max-height:150px; overflow:hidden;" src="<?php echo plugin_dir_url( __FILE__ );?>/templates/images/nuntium16.jpg" alt="See theme demo" title="See theme demo" /></a>
</div>

<div style="width:100px; float:left; margin:5px; padding:5px; box-sizing:border-box; text-align:center; border:1px solid #ccc;">
<p style="text-transform:capitalize;">Premium Theme</p>
<a href="http://enews.xcedo.com/newsletter/newsletter-17" target="_blank"><img style="max-width:100%; max-height:150px; overflow:hidden;" src="<?php echo plugin_dir_url( __FILE__ );?>/templates/images/nuntium17.jpg" alt="See theme demo" title="See theme demo" /></a>
</div>

<div style="width:100px; float:left; margin:5px; padding:5px; box-sizing:border-box; text-align:center; border:1px solid #ccc;">
<p style="text-transform:capitalize;">Premium Theme</p>
<a href="http://enews.xcedo.com/newsletter/newsletter-18" target="_blank"><img style="max-width:100%; max-height:150px; overflow:hidden;" src="<?php echo plugin_dir_url( __FILE__ );?>/templates/images/nuntium18.jpg" alt="See theme demo" title="See theme demo" /></a>
</div>

<div style="width:100px; float:left; margin:5px; padding:5px; box-sizing:border-box; text-align:center; border:1px solid #ccc;">

<p style="text-transform:capitalize;"><a href="http://www.xcedo.com/nuntium" target="_blank">PRO Version<br />Buy Now!</a></p>

<a href="http://www.xcedo.com/nuntium" target="_blank"><img style="max-width:100%; max-height:150px; overflow:hidden;" src="<?php echo plugin_dir_url( __FILE__ );?>/templates/images/nuntiumMore.jpg" alt="Get the PRO version for more Themes and Features" title="Get the PRO version for more Themes and Features" /></a>

</div>      

</div>

</td>

</th>

</tr>

<?php

}

add_action( 'newsletter_edit_form_fields', 'fc_newsletter_edit_field' );  

function save_my_tax_meta( $term_id ){if ( isset( $_POST['newsletterTheme'] ) ) {$newsletterTheme = sanitize_text_field($_POST["newsletterTheme"]); update_option( "taxonomy_$term_id", $newsletterTheme );}}	

add_action( 'edited_newsletter', 'save_my_tax_meta', 10, 2 );

add_action( 'newsletter_add_form_fields', 'fc_newsletter_edit_field' );

add_action( 'create_newsletter', 'save_my_tax_meta', 10, 2 );

function enews_category_columns($columns){return array_merge($columns, array('enews' =>  __('Newsletter Theme')));}

add_filter('manage_edit-newsletter_columns' , 'enews_category_columns');

function enews_category_columns_values( $deprecated, $column_name, $term_id) {if($column_name === 'enews'){$newsletterTheme = get_option( "taxonomy_$term_id" ); echo $newsletterTheme;}}

add_action( 'manage_newsletter_custom_column' , 'enews_category_columns_values', 10, 3 );

add_filter('template_include', 'newsletter_set_template');

function newsletter_set_template( $template ) {

$termSlug = get_query_var('term');

$term = get_term_by('slug', $termSlug, 'newsletter');

$id = $term->term_id;

$currentTheme = get_option( "taxonomy_".$id );

if( is_tax('newsletter', $termSlug) ) $template = plugin_dir_path(__FILE__ ).'templates/'.$currentTheme.'.php'; return $template;

}

register_activation_hook( __FILE__, 'register_nuntium' );

?>