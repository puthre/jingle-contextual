<?php
/*  Copyright 2014  jingle.ro  (email : office@jingle.ro)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
 * @package Jingle Contextual
 */
/*
Plugin Name: Jingle Contextual
Plugin URI: https://www.jingle.ro/plugins/wordpress/jinglecontextual.zip
Description: Adds Jingle Contextual widget so you can display contextual relevant products from 2parale.ro affiliate network on your blog. To get started: 1) Activate the plugin. 2) Configure the plugin at Settings -> Jingle Contextual. 3) Enable the widget at Appearance -> Widgets
Version: 1.8
Author: Jingle.ro
Author URI: https://www.jingle.ro/
License: GPLv3  or later
*/

defined('ABSPATH') or die("No script kiddies please!");

require "2Performant-php/TPerformant.php";


// Creating the widget 
class jingle_widget extends WP_Widget {

function __construct() {
    parent::__construct(
    // Base ID of your widget
    'jingle_widget', 
    
    // Widget name will appear in UI
    __('Jingle Contextual', 'jingle_widget_domain'), 
    
    // Widget description
    array( 'description' => __( 'Afișează contextual produse din magazinele afiliate 2parale folosind Jingle.ro', 'jingle_widget_domain' ), ) 
    );
}

// Creating widget front-end
// This is where the action happens
    public function widget( $args, $instance ) {
        $title = apply_filters( 'widget_title', $instance['title'] );
        // before and after widget arguments are defined by themes
        echo $args['before_widget'];
        if ( ! empty( $title ) )
           echo $args['before_title'] . $title . $args['after_title'];
        
        
        $shoplist_s = get_option( 'jc_shoplist' );
        if ( empty( $shoplist_s ) ){
            echo "Widget neconfigurat. Configureaza widgetul Jingle Contextual din panoul Settings -> Jingle Contextual";
            echo $args['after_widget'];
            return;
        }
        $shoplist = unserialize( $shoplist_s );
        $uc = get_option( 'jc_2p_uc' );
        $backgroundcolor = get_option( 'jc_bc' );
        $backgroundcolor = str_replace('#', '',$backgroundcolor);
        $color = get_option( 'jc_color' );
        $color = str_replace('#', '',$color);
        $nproducts = get_option( 'jc_nproducts' );
        if( empty($nproducts))
            $nproducts = 2;
        $mwidth = get_option( 'jc_mwidth' );
        if( empty($mwidth))
            $mwidth = 150;
        ?>
        
        <div id='jingle_contextual_<?php echo $this->id;?>' style='margin:auto;'></div>
        <script type="text/javascript">
        var query="shop:<?php echo implode( ",", $shoplist );?> ";
        var juid="";
        var jnumResults = 32;
        var jwidth = "100%";
        var aff_code = "<?php echo $uc;?> ";
        var aff_tag = document.URL.split('/').join('_');
        var scroll_speed = "11";
        var scroll_step = "20";
        var product_pause = "10000";
        var custom_css = "";
        var custom_js = "";
        var availwidth = document.getElementById('jingle_contextual_<?php echo $this->id;?>').clientWidth;
        //availwidth = availwidth.replace('px','');
        var margin = 5;
        if (availwidth>(<?php echo $mwidth;?>+margin)*2){
            availwidth=Math.floor(availwidth/(<?php echo $mwidth;?>+margin))*(<?php echo $mwidth;?>+margin);
            var np = Math.floor(availwidth/(<?php echo $mwidth;?>+margin));
            document.getElementById('jingle_contextual_<?php echo $this->id;?>').style.maxWidth = availwidth+'px';
            var jscroll_direction ="up";
            var product_width = availwidth/np;
            var product_height = product_width*2;
            var jheight = product_height;
            var image_width = product_width-margin;
            var image_height = image_width*4/3;
            var img_css = "a img {max-width:"+image_width+"px;width:100%;max-height:none;}";
        } else {
            availwidth = Math.min(<?php echo $mwidth;?>,availwidth);
            document.getElementById('jingle_contextual_<?php echo $this->id;?>').style.maxWidth='<?php echo $mwidth;?>px';
            var jscroll_direction ="up";
            var product_width = availwidth;
            var product_height = product_width*4/3+80;
            var jheight = product_height*<?php echo $nproducts;?>;
            var image_width = product_width;
            var image_height = image_width*4/3;
            var img_css = "a img {max-width:100%;width:100%;max-height:none;}";
        }
        var bg_color = "<?php echo $backgroundcolor;?>";
        var jbrand_fsize = "12";
        var jproduct_nfsize ="14";
        var jproduct_nheight ="32";
        var jproduct_ncolor ="<?php echo $color;?>";
        var jproduct_nalign ="center";
        var jprice_ofsize ="12";
        var jprice_ocolor ="<?php echo $backgroundcolor;?>";
        var jprice_oalign ="center";
        var jprice_nfsize ="22";
        var jprice_ncolor ="9F9FFF";
        var jprice_nalign ="center";
        var jprice_offfsize ="25";
        var jprice_offcolor ="<?php echo $backgroundcolor;?>";
        var jprice_offalign ="center";
        var jshop_fsize ="10";
        var jshop_color ="999999";
        var jtarget_type ="_blank";
        var jdisplay_order ="NAME,IMAGEFULL,BRAND,CLEAR,OPRICE,SALEPRICE,OFFPRICE";
        
        var jrepeat_num ="4";
        var jcustom_css_text =
            //"body {background:none transparent;}"+
            "body {background-color:<?php echo $backgroundcolor;?>;}"
            +".product_item {border-bottom:1px solid #eee;}"+
            ".product_name {font-family:Arial;}.product_oldprice { position:relative;top:-100px;color:#999;background-color:#<?php echo $color;?>;opacity:0.8;filter: alpha(opacity=80);}"+
            ".product_saleprice {position:relative;top:-100px;background-color:#<?php echo $color;?>;opacity:0.8;filter: alpha(opacity=80);}"+
            ".product_saleprice a{color:#<?php echo $backgroundcolor;?>;}"+
            ".product_off {position:relative;top:-100px;color:#<?php echo $backgroundcolor;?>;background-color:#<?php echo $color;?>;opacity:0.8;filter: alpha(opacity=80);}"+
            //".product_image_outer {height:auto !important}.page {};"+
            ".product_brand{min-height:18px;}"+
            ".product_image_middle{}.product_image_overflow{}.product_image_inner{}.product_brand a {}.product_shop a {}.product_name a {}"+
            img_css;
        var jnoproducts ="<a href='https://www.jingle.ro'>Jingle.ro - Motorul tău de căutare pentru produse</a>";
        var jingle_iframe = "<iframe width=\""+jwidth+"\" "+"scrolling=\"no\" "+"height=\""+jheight+"px\" allowtransparency=\"true\" "+"style=\"overflow: hidden; border: medium none;\" frameBorder=\"0\" "+"src=\"https://www.jingle.ro/product/contextual/?q="+encodeURIComponent(query)+"&num="+jnumResults+"&juid="+encodeURIComponent(juid)+"&custom_js="+encodeURIComponent(custom_js)+"&custom_css="+encodeURIComponent(custom_css)+"&aff_code="+encodeURIComponent(aff_code)+"&aff_tag="+encodeURIComponent(aff_tag)+"&scroll_speed="+encodeURIComponent(scroll_speed)+"&scroll_step="+encodeURIComponent(scroll_step)+"&product_pause="+encodeURIComponent(product_pause)+"&product_height="+encodeURIComponent(product_height)+"&product_width="+encodeURIComponent(product_width)+"&image_height="+encodeURIComponent(image_height)+"&image_width="+encodeURIComponent(image_width)+"&bg_color="+encodeURIComponent(bg_color)+"&brand_fsize="+encodeURIComponent(jbrand_fsize)+"&product_nfsize="+encodeURIComponent(jproduct_nfsize)+"&product_nheight="+encodeURIComponent(jproduct_nheight)+"&product_ncolor="+encodeURIComponent(jproduct_ncolor)+"&product_nalign="+encodeURIComponent(jproduct_nalign)+"&price_ofsize="+encodeURIComponent(jprice_ofsize)+"&price_ocolor="+encodeURIComponent(jprice_ocolor)+"&price_oalign="+encodeURIComponent(jprice_oalign)+"&price_nfsize="+encodeURIComponent(jprice_nfsize)+"&price_ncolor="+encodeURIComponent(jprice_ncolor)+"&price_nalign="+encodeURIComponent(jprice_nalign)+"&price_offfsize="+encodeURIComponent(jprice_offfsize)+"&price_offcolor="+encodeURIComponent(jprice_offcolor)+"&price_offalign="+encodeURIComponent(jprice_offalign)+"&shop_fsize="+encodeURIComponent(jshop_fsize)+"&shop_color="+encodeURIComponent(jshop_color)+"&target_type="+encodeURIComponent(jtarget_type)+"&display_order="+encodeURIComponent(jdisplay_order)+"&scroll_direction="+encodeURIComponent(jscroll_direction)+"&repeat_num="+encodeURIComponent(jrepeat_num)+"&custom_css_text="+encodeURIComponent(jcustom_css_text)+"&noproducts="+encodeURIComponent(jnoproducts)+"\" ></iframe>";
        document.getElementById('jingle_contextual_<?php echo $this->id;?>').innerHTML = jingle_iframe;
        
        //document.write();
        </script>
        <?php
        echo $args['after_widget'];
    }
        
    // Widget Backend 
    public function form( $instance ) {
        if ( isset( $instance['title'] ) ) {
            $title = $instance['title'];
        }
        else {
            $title = __( 'Recomandări', 'jingle_widget_domain' );
        }
        // Widget admin form
        ?>
        <p>
        <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
        <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
        </p>
        <?php 
    }
    
// Updating widget replacing old instances with new
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        return $instance;
    }
} // Class wpb_widget ends here

// Register and load the widget
function jingle_load_widget() {
    register_widget( 'jingle_widget' );
}
add_action( 'widgets_init', 'jingle_load_widget' );
/** Step 2 (from text above). */
add_action( 'admin_menu', 'jingle_plugin_menu' );

function jingle_scripts_method() {
    wp_enqueue_script( 'iris' );
}

add_action( 'admin_enqueue_scripts', 'jingle_scripts_method' ); // wp_enqueue_scripts action hook to link only on the front-end

/** Step 1. */
function jingle_plugin_menu() {
    add_options_page( 'Setări Jingle Contextual', 'Jingle Contextual', 'manage_options', 'jingle-contextual', 'jingle_contextual_options' );
}

/** Step 3. */
function jingle_contextual_options() {
    if ( !current_user_can( 'manage_options' ) )  {
    wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    }
    
    echo '<div class="wrap">';

    // header

    echo "<h2>" . __( 'Setări Jingle Contextual', 'menu-jingle' ) . "</h2>";

    // settings form
    $user = get_option( 'jc_2p_user' );
    $password = get_option( 'jc_2p_password');
    $unique_code = get_option( 'jc_2p_uc' );
    $backgroundcolor = get_option( 'jc_bc' );
    $nproducts = get_option( 'nproducts' );
    $mwidth = get_option( 'mwidth' );
    $color = get_option( 'jc_color' );
    if ( empty( $color ) ){
        $color = '800000';
    }
    if ( empty( $backgroundcolor ) ){
        $backgroundcolor = 'ffffff';
    }
    if ( empty( $nproducts ) ){
        $nproducts = '2';
    }
    if ( empty( $mwidth ) ){
        $mwidth = '150';
    }
    $updated = false;
    if( isset( $_POST[ 'user_2p' ] ) ){
        $user = $_POST[ 'user_2p' ];
        update_option( 'jc_2p_user', $user );
        $updated = true;
    }
    if( isset($_POST[ 'textcolor' ])){
        $color = $_POST[ 'textcolor' ];
        update_option( 'jc_color', $color );
        $updated = true;
    }
    if( isset( $_POST[ 'backgroundcolor' ] ) ){
        $backgroundcolor = $_POST[ 'backgroundcolor' ];
        update_option( 'jc_bc', $backgroundcolor );
        $updated = true;
    }
    if( isset( $_POST[ 'nproducts' ] ) ){
        $nproducts = $_POST[ 'nproducts' ];
        update_option( 'jc_nproducts', $nproducts );
        $updated = true;
    }
    if( isset( $_POST[ 'mwidth' ] ) ){
        $mwidth = $_POST[ 'mwidth' ];
        update_option( 'jc_mwidth', $mwidth );
        $updated = true;
    }
    if( isset($_POST[ 'password_2p' ])){
        $password = $_POST[ 'password_2p' ];
        update_option( 'jc_2p_password', $password );
        $updated = true;
        $session = new TPerformant( "simple", array( "user" => $user, "pass" => $password ), 'http://api.2parale.ro' );
        try {
            $page = 1;
            $simplecl = array();
            do {
                $cl = $session->campaigns_listforaffiliate( $page++ );
                foreach ( $cl as $c ){
                    if ( $c->products_count > 0 && $c->status == 'active' )
                        $simplecl[] = $c->base_url;
                }
            } while ( !empty( $cl ) );
            $user2p = $session->user_loggedin();
            $unique_code = $user2p->unique_code;
            update_option( 'jc_2p_uc', $unique_code );
        } catch ( Exception $e ) {
            echo "<div class='error'>Eroare: " . $e->getMessage() . '</div>';
        }
        
        if ( ! empty( $simplecl ) ){
            update_option( 'jc_shoplist', serialize( $simplecl ) );
        ?>
            <div class="updated"><p><strong><?php _e( 'Lista de magazine importată. Nu uita să adaugi widgetul Jingle Contextual în pagina Appearance -> Widgets și să reimporți lista de campanii de fiecare dată când ești aprobat într-o campanie nouă ', 'menu-jingle' ); ?></strong></p></div>
        <?php
        }
    }
    if ( $updated ){ ?>
    <div class="updated"><p><strong><?php _e( 'Setări salvate', 'menu-jingle' ); ?></strong></p></div>
    <?php 
    }
    ?>

<form name="formjc" method="post" action="">


<p><?php _e("User 2parale.ro:", 'menu-jingle' ); ?> 
<input type="text" name="user_2p" value="<?php echo $user; ?>" size="20">
</p>
<p><?php _e("Parola 2parale.ro:", 'menu-jingle' ); ?> 
<input type="password" name="password_2p" value="<?php echo $password; ?>" size="20">
</p>
<p>Atentie: Datele de conectare la sistemul de afiliere 2Parale sunt folosite doar pentru a descărca lista de campanii aprobate precum și codul unic de afiliat. Aceste date NU ajung pe serverele Jingle.ro</p>
<hr />

<p><?php _e("Culoare text:", 'menu-jingle' ); ?> 
<input type="text" name="textcolor" class='jcolor-picker' value="<?php echo $color; ?>" size="20">
</p>
<p><?php _e("Culoare fundal:", 'menu-jingle' ); ?> 
<input type="text" name="backgroundcolor" class='jcolor-picker' value="<?php echo $backgroundcolor; ?>" size="20">
</p>
<p><?php _e("Număr produse:", 'menu-jingle' ); ?> 
<input type="text" name="nproducts" value="<?php echo $nproducts; ?>" size="20">
</p>
<p><?php _e("Lățime maximă (px):", 'menu-jingle' ); ?> 
<input type="text" name="mwidth" value="<?php echo $mwidth; ?>" size="20">
</p>

<hr />

<p class="submit">
<input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e( 'Salvează schimbările și importă lista de magazine' ) ?>" />
</p>
<hr />

<p><?php _e("Cod unic 2parale.ro:", 'menu-jingle' ); ?> 
<input type="text" name="codunic" readonly value="<?php echo $unique_code; ?>" size="20">
</p><hr />
</form>
</div>
<script>
jQuery(document).ready(function($){
    $('.jcolor-picker').iris();
});
</script>
<?php
}
