<?php
/**
 * Plugin Name: Formidable Forms Posts, Pages and CPT Field Type
 * Plugin Uri: 
 * Description: Adds a new field type to Formidable Forms allowing the user to choose a post, page or CPT
 * Version: 1.0.0
 * Author: PropertyHive
 * Author URI: http://wp-property-hive.com
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Formidable_Posts_Page_CPT_Field_Type' ) ) :

final class Formidable_Posts_Page_CPT_Field_Type {

    /**
     * @var string
     */
    public $version = '1.0.0';

    /**
     * @var Property Hive The single instance of the class
     */
    protected static $_instance = null;
    
    /**
     * Main plugin instance
     *
     * Ensures only one instance of plugin is loaded or can be loaded.
     *
     * @static
     * @return Main instance
     */
    public static function instance() 
    {
        if ( is_null( self::$_instance ) ) 
        {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Constructor.
     */
    public function __construct() {

        // Define constants
        //$this->define_constants();

        // Include required files
        $this->includes();

        add_filter( 'frm_available_fields', array( $this, 'frm_available_fields_ppcpt' ), 10, 1 );

        add_filter( 'frm_get_field_type_class', array( $this, 'frm_get_field_type_class_ppcpt' ), 10, 2 );

        add_action( 'frm_field_options_form', array( $this, 'frm_field_options_form_ppcpt' ), 10, 3 );

        //add_action( 'wp_enqueue_scripts', array( $this, 'load_mortgage_calculator_scripts' ) );
        //add_action( 'wp_enqueue_scripts', array( $this, 'load_mortgage_calculator_styles' ) );
    }

    /**
     * Define Constants
     */
    private function define_constants() 
    {
        //define( 'PH_MORTGAGE_CALCULATOR_PLUGIN_FILE', __FILE__ );
        //define( 'PH_MORTGAGE_CALCULATOR_VERSION', $this->version );
    }

    private function includes()
    {
        
    }

    public function frm_available_fields_ppcpt( $available_fields )
    {
        $available_fields['posts_pages_cpt'] = array(
            'name'  => __( 'Posts, Pages, CPT Dropdown', 'formidable' ),
            'icon'  => 'frm_icon_font frm_caret-square-down_icon',
        );

        return $available_fields;
    }

    public function frm_get_field_type_class_ppcpt( $class, $field_type )
    {
        if ( $field_type == 'posts_pages_cpt' )
        {
            include_once( dirname( __FILE__ ) . "/FrmFieldPostsPagesCPT.php" );
            $class = 'FrmFieldPostsPagesCPT';
        }

        return $class;
    }

    public function frm_field_options_form_ppcpt ($field, $display, $values )
    {
?>
<tr>
    <td>
        <label for="post_types_<?php echo esc_attr( $field['field_key'] ) ?>">
            <?php esc_html_e( 'Post Types', 'formidable' ) ?>
        </label>
    </td>
    <td>
        <select name="field_options[post_types_<?php echo esc_attr( $field['id'] ) ?>][]" multiple id="post_types_<?php echo esc_attr( $field['field_key'] ) ?>">
            <?php
                $post_types = get_post_types( $args, 'objects' );
                foreach ( $post_types as $post_type )
                {
                    echo '<option value="' . $post_type->name . '"';
                    if ( isset($field['post_types']) && ( (is_array($field['post_types']) && in_array($post_type->name, $field['post_types']) ) || ( !is_array($field['post_types']) && $field['post_types'] == $post_type->name ) ) ) { echo ' selected'; }
                    echo '>' . $post_type->label . '</option>';
                }
            ?>
        </select>
    </td>
</tr>
<tr>
    <td>
        <label for="value_format_<?php echo esc_attr( $field['field_key'] ) ?>">
            <?php esc_html_e( 'Value Format', 'formidable' ) ?>
        </label>
    </td>
    <td>
        <input name="field_options[value_format_<?php echo esc_attr( $field['id'] ) ?>]" id="value_format_<?php echo esc_attr( $field['field_key'] ) ?>" value="<?php echo esc_attr( $field['value_format']) ?>">
        <div class="howto">Possible Values: %id% %title% %meta_{meta_key} %taxonomy_{taxonomy}</div>
    </td>
</tr>
<tr>
    <td>
        <label for="label_format_<?php echo esc_attr( $field['field_key'] ) ?>">
            <?php esc_html_e( 'Label Format', 'formidable' ) ?>
        </label>
    </td>
    <td>
        <input name="field_options[label_format_<?php echo esc_attr( $field['id'] ) ?>]" id="label_format_<?php echo esc_attr( $field['field_key'] ) ?>" value="<?php echo esc_attr( $field['label_format']) ?>">
        <div class="howto">Possible Values: %id% %title% %meta_{meta_key} %taxonomy_{taxonomy}</div>
    </td>
</tr>
<?php
    }

}

endif;

/**
 * Returns the main instance of Formidable_Posts_Page_CPT_Field_Type to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return Formidable_Posts_Page_CPT_Field_Type
 */
function FPPCPTFT() {
    return Formidable_Posts_Page_CPT_Field_Type::instance();
}

FPPCPTFT();