<?php
/**
 * In functions.php, you don't need a closing PHP tag (and also in other PHP files that are purely PHP)
 */
defined( 'ABSPATH' ) || die( "Can't access directly" );

add_theme_support( 'title-tag' );

function tania_scripts() {
    wp_enqueue_style( 'bootstrap', get_template_directory_uri() . '/css/bootstrap.min.css', array(), '3.3.6' );
    wp_enqueue_style( 'blog', get_template_directory_uri() . '/css/blog.css' );
    wp_enqueue_script( 'bootstrap', get_template_directory_uri() . '/js/bootstrap.min.js', array( 'jquery' ), '3.3.6', true );
}

function tania_google_fonts() {
    wp_register_style('OpenSans', 'http://fonts.googleapis.com/css?family=Open+Sans:400,600,700,800');
    wp_enqueue_style( 'OpenSans' );
}

function custom_settings_add_menu() {
    add_menu_page( 'Custom Settings', 'Custom Settings', 'manage_options', 'custom-settings', 'custom_settings_page', null, 99 );
}

add_action( 'wp_enqueue_scripts', 'tania_scripts' );
add_action('wp_print_styles', 'tania_google_fonts');
add_action( 'admin_menu', 'custom_settings_add_menu' );

function custom_settings_page() { ?>
    <div class="wrap">
        <h1>Custom Settings</h1>
        <form method="post" action="options.php">
            <?php 
                settings_fields( 'section' );
                do_settings_sections( 'theme-options' );
                submit_button();
            ?>
        </form>
    </div>
<?php }

function setting_twitter() { ?>
    <input type="text" name="twitter" id="twitter" value="<?php echo get_option( 'twitter' ); ?>" />
<?php }

function setting_github() { ?>
    <input type="text" name="github" id="github" value="<?php echo get_option( 'github' ); ?>" />
<?php }

function setting_facebook() { ?>
    <input type="text" name="facebook" id="facebook" value="<?php echo get_option( 'facebook' ); ?>" />
<?php }

function custom_settings_page_setup() {
    add_settings_section( 'section', 'All Settings', null, 'theme-options' );

    add_settings_field( 'twitter', 'Twitter URL', 'setting_twitter', 'theme-options', 'section' );
    add_settings_field( 'github', 'GitHub URL', 'setting_github', 'theme-options', 'section' );
    add_settings_field( 'facebook', 'Facebook URL', 'setting_facebook', 'theme-options', 'section' );

    register_setting('section', 'twitter');
    register_setting('section', 'github');
    register_setting('section', 'facebook');
}
add_action( 'admin_init', 'custom_settings_page_setup' );

add_theme_support( 'post-thumbnails' );

function create_my_custom_post() {
    register_post_type( 'my-custom-post',
        array(
            'labels' => array(
                'name' => __( 'My Custom Post' ),
                'singular_name' => __( 'My Custom Post' ),
            ),
            'public'       => true,
            'hierarchical' => true,
            'has_archive'  => true,
            'supports'     => array(
                'title',
                'editor',
                'excerpt',
                'thumbnail',
                'custom-fields'
            ),
            'taxonomies' => array(
                'post_tag',
                'category'
            )
        )
    );
    register_taxonomy_for_object_type( 'category', 'my-custom-post' );
    register_taxonomy_for_object_type( 'post_tag', 'my-custom-post' );
}
add_action( 'init', 'create_my_custom_post' );

function my_fields_meta_box() {
    add_meta_box(
        'my_fields_meta_box', // $id
        'Your Fields', // $title
        'show_my_fields_meta_box', // $callback
        'my-custom-post', // $screen
        'normal', // $context
        'high' // $priority
    );
}
add_action( 'add_meta_boxes', 'my_fields_meta_box' );

function show_my_fields_meta_box() {
    global $post;
    $meta = get_post_meta( $post->ID, 'my_fields' ); ?>
        <input type="hidden" name="my_meta_box_nonce" value="<?php echo wp_create_nonce( basename(__FILE__) ); ?>" />
        <!-- All fields will go here -->
        <p>
            <label for="my_fields[text]">Input Text</label>
            <br>
            <input type="text" name="my_fields[text]" class="regular-text" value="<?php echo $meta['text']; ?>">
        </p>
        <p>
            <label for="my_fields[textarea]">Textarea</label>
            <br>
            <textarea name="my_fields[textarea]" id="my_fields[textarea]" rows="5" cols="30" style="width:500px;"><?php if (is_array($meta) && isset($meta['textarea'])){ echo $meta['textarea']; } ?></textarea>
        </p>
        <p>
            <label for="my_fields[checkbox]">Checkbox
                <input type="checkbox" name="my_fields[checkbox]" value="checkbox" <?php if ( $meta['checkbox'] === 'checkbox' ) echo 'checked'; ?>>
            </label>
        </p>
        <p>
            <label for="my_fields[select]">Select Menu</label>
            <br>
            <select name="my_fields[select]" id="my_fields[select]">
                <option value="option-one" <?php selected( $meta['select'], 'option-one' ); ?>>Option One</option>
                <option value="option-two" <?php selected( $meta['select'], 'option-two' ); ?>>Option Two</option>
            </select>
        </p>
        <p>
            <label for="my_fields[image]">Image Upload</label><br>
            <input type="text" name="my_fields[image]" id="my_fields[image] class="meta-image regular-text" value="<?php echo $meta['image']; ?>">
            <input type="button" class="button image-upload" value="Browse">
        </p>
        <div class="image-preview"><img src="<?php echo $meta['image']; ?>" style="max-width: 250px;"></div>
        <script>
            jQuery(document).ready(function ($) {
                // Instantiates the variable that holds the media library frame
                let meta_image_frame
                // Runs when the image button is clicked
                $('.image-upload').click(function (e) {
                    // Get preview pane
                    let meta_image_preview = $(this)
                        .parent()
                        .parent()
                        .children('.image-preview')
                    // Prevents the default action from occurring
                    e.preventDefault()
                    let meta_image = $(this).parent().children('.meta-image')
                    // If the frame already exists, re-open it
                    if(meta_image_frame) {
                        meta_image_frame.open()
                        return
                    }
                    // Sets up the media library frame
                    meta_image_frame = wp.media.frames.meta_image_frame = wp.media({
                        title: meta_image.title,
                        button: {
                            text: meta_image.button,
                        },
                    })
                    // Runs when an image is selected
                    meta_image_frame.on('select', function () {
                        // Grabs the attachment selection and creates a JSON representation of the model
                        let media_attachment = meta_image_frame
                            .state()
                            .get('selection')
                            .first()
                            .toJSON()
                        // Sends the attachment URL to our custom image input field
                        meta_image.val(media_attachment.url)
                        meta_image_preview.children('img').attr('src', media_attachment.url)
                    })
                    // Opens the media library frame
                    meta_image_frame.open()
                })
            })
        </script>
<?php }

function save_my_fields_meta( $post_id ) {
    // verify nonce
    if ( !wp_verify_nonce( $_POST['my_meta_box_nonce'], basename(__FILE__) ) ) {
        return $post_id;
    }
    // check autosave
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return $post_id;
    }
    // check permissions
    if ( 'page' === $_POST['post_type'] ) {
        if ( !current_user_can( 'edit_post', $post_id ) ) {
            return $post_id;
        } elseif ( !current_user_can( 'edit_post', $post_id ) ) {
            return $post_id;
        }
    }

    $old = get_post_meta( $post_id, 'my_fields', true );
    $new = $_POST['my_fields'];

    if ( $new && $new !== $old ) {
        update_post_meta( $post_id, 'my_fields', $new );
    } elseif ( '' === $new && $old ) {
        delete_post_meta( $post_id, 'my_fields', $old );
    }
}
add_action( 'save_post', 'save_my_fields_meta' );