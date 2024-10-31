<?php 
show_admin_bar( false );
$term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
$term_id = $term->term_id;
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="viewport" content="width=device-width" />
<title><?php wp_title( '|', true, 'right' ); ?></title>
<meta name="description" content="Page generated with Nuntium Wordpress Newsletter Generator" />
<style type="text/css">
a, a:visited, a:active {color:<?php echo $linkcolor; ?>; text-decoration:none !important;}
a:hover {color:#333; text-decoration:underline;}
blockquote {font-family:Arial, sans-serif, 'Roboto';color:#333333; font-size:13px; line-height:14px; padding:10px !important; margin:0; background:#eeeeee;}
li {font-family:Arial, sans-serif, 'Roboto';color:#333333; font-size:13px; line-height:14px; margin:5px 0 5px 10px;}
</style>
</head>

<body topmargin="0" leftmargin="0" rightmargin="0" bottommargin="0">

<table border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed;">
<tbody>
<tr>
<td align="center">
<div align="center">

    <table width="600" align="center" cellpadding="0" cellspacing="0" border="0" bgcolor="#FFFFFF">
    <tr>
    <!-- Start Content -->
    <td align="left" valign="top" width="600">
    
    <table width="600" cellpadding="0" cellspacing="0" border="0">
      
        <tr>
            <td width="600" align="center" valign="middle" height="70" bgcolor="#dddddd">
                <a href="<?php echo get_permalink( $term ); ?>" target="_blank"><img src="<?php echo plugin_dir_url( __FILE__ ) ?>/images/logo.png" alt="<?php echo( get_bloginfo( 'title' ) ); ?>" /></a>
            </td>
        </tr>
        <tr>
            <td width="600" height="10"></td>
        </tr>        
        <tr>
            <td align="left" valign="top"> 
                <table width="600" align="left" cellpadding="0" cellspacing="0" border="0">
                <?php if ( have_posts() ) : ?>
                <?php
                $postquery = new WP_Query( array('meta_key' => 'meta-sortarticle', 'orderby' => 'meta_value_num', 'order' => 'ASC', 'posts_per_page' => 10, 'tax_query' => array(array('taxonomy' => 'newsletter','terms' => $term)) ));
                while ( $postquery->have_posts() ) : $postquery->the_post(); ?>

                	<!-- Start Article -->
                    <tr>
                        <td width="100%" align="left" valign="top">
                            <table cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td width="44%" align="left" valign="top">  
                                    <!-- Start Thumbnail -->
                                    <table width="100%" align="left" cellspacing="0" cellpadding="0" border="0">
                                        <tr><td align="left"><a href="<?php the_permalink(); ?>" target="_blank" border="0"><?php if ( has_post_thumbnail() ) {the_post_thumbnail( array(300, 250) );} ?></a></td></tr>
                                    </table>
                                    <!-- End Thumbnail -->
                                </td>
                                <td width="2%" height="10" align="left">&nbsp;</td>
                                <td width="54%" align="left" valign="top">    
                                    <!-- Start Article Content -->
                                    <table width="100%" align="left" cellpadding="0" cellspacing="0" border="0">
                                        <tr>
                                            <td valign="top" style="padding:0;font-size:19px;font-weight:normal;line-height:22px;font-family:Arial, sans-serif, 'Roboto';color:#333333;">
                                            <a style="text-decoration:none;" href="<?php the_permalink(); ?>" target="_blank"><?php the_title(); ?></a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align="left" valign="top" style="font-family:Arial, sans-serif, 'Roboto';font-size:12px;font-weight:normal;line-height:18px;color:#333333;"><?php the_content('Read more...'); ?></td>
                                        </tr>
                                    </table>
                                    <!-- End Article Content -->
                                    
                                    <!-- Start Article Footer -->
                                    <table align="left" cellpadding="0" cellspacing="0" border="0">
                                    	<tr><td height="10"></td></tr>
                                        <tr>
                                            <td height="10" align="left" valign="middle" style="font-family:Arial, sans-serif, 'Roboto';font-size:11px;font-weight:normal;">
												<?php nuntium_entry_meta(); ?><a style="text-decoration:none;" href="<?php the_permalink(); ?>#respond" target="_blank">Comment</a>
                                            </td>
                                        </tr>
                                    </table>
                                    <!-- End Article Footer -->     
                                </td>
                            </tr>
                            </table>
                        </td>   
                    </tr>
                    <tr><td width="100%" height="40" align="left" valign="middle"><hr color="#dddddd" size="1" style="border:1px solid #dddddd;"></td></tr>
                    <!-- End Article -->
                
				<?php endwhile; ?>   
                <?php else : ?>
                    <?php get_template_part( 'content', 'none' ); ?>
                <?php endif; ?>
                </table>
            </td>      
        </tr>
        </table>
    
    </td>
    <!-- End Content -->
    </tr>
    <tr>
        <td width="600" height="60" align="center" valign="middle" cellpadding="0" cellspacing="0" style="font-family:Arial, sans-serif, 'Roboto';font-size:11px;font-weight:normal;line-height:12px;color:#333333;text-transform:uppercase;text-align:center;" bgcolor="#dddddd">
            
            <table width="100%" align="center" style="padding:0 12px;border:none;border-bottom:1px solid #cccccc">
            <tr>
            <td style="text-align:center;padding:30px 20px 30px 20px;font-family:sans-serif;font-size:12px;line-height:18px;color:#888888;border-collapse:collapse"> 
            <div>
            	<a href="<?php echo get_permalink( $term ); ?>" target="_blank"><img src="<?php echo plugin_dir_url( __FILE__ ) ?>/images/logo.png" alt="<?php echo( get_bloginfo( 'title' ) ); ?>" /></a>
            </div> 
            </td>
            </tr>
            </table>
    
		</td>
    </tr>   
    <tr>
        <td width="600" height="60" align="center" valign="middle" cellpadding="0" cellspacing="0" style="font-family:Arial, sans-serif, 'Roboto';font-size:11px;font-weight:normal;line-height:12px;color:#333333;text-transform:uppercase;text-align:center;" bgcolor="#dddddd">
        &copy; <?php bloginfo( 'name' ); ?> | <?php bloginfo( 'description' ); ?>
        </td>
    </tr>
    </table>    
</div>
</td>
</tr>
</tbody>
</table>
</body>
</html>