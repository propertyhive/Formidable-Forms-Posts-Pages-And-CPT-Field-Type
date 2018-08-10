<?php

/**
 * @since 1.0
 */
class FrmFieldPostsPagesCPT extends FrmFieldType {

	/**
	 * @var string
	 * @since 0.0
	 */
	protected $type = 'posts_pages_cpt';

	protected function field_settings_for_type() {
		return array(
		);
	}

	/**
	 * @return array
	 */
	protected function extra_field_opts() {
		return array(
			'post_types' => array(),
			'value_format' => '%id%',
			'label_format' => '%title%',
		);
	}

	/**
	 * @param string $name
	 */
	public function show_on_form_builder( $name = '' ) {
		$size = FrmField::get_option( $this->field, 'size' );
		$size_html = $size ? ' style="width:' . esc_attr( $size . ( is_numeric( $size ) ? 'px' : '' ) ) . '";' : '';

		$max = FrmField::get_option( $this->field, 'max' );
		$default_value = FrmAppHelper::esc_textarea( force_balance_tags( $this->get_field_column( 'default_value' ) ) );

		echo '<select name="' . esc_attr( $this->html_name( $name ) ) . '" ' .
			'id="' . esc_attr( $this->html_id() ) . '" class="dyn_default_value">' .
			'<option value=""></option>'
			. '</select>';
	}

	/**
	 * @param array $args
	 * @param array $shortcode_atts
	 *
	 * @return string
	 */
	public function front_field_input( $args, $shortcode_atts ) {
		$input_html = $this->get_field_input_html_hook( $this->field );
		$this->add_aria_description( $args, $input_html );

		$options = array('<option value=""></option>');
		if ( isset($this->field['post_types']) && $this->field['post_types'] != '' )
		{
			$post_types = $this->field['post_types'];
			if ( !is_array($post_types) )
			{
				$post_types = array($this->field['post_types']);
			}

			foreach ( $post_types as $post_type )
			{
				// get all published posts of this post type
				$query_args = array(
					'post_type' => $post_type,
					'nopaging' => true 
				);

				$query_args = apply_filters('formidable_posts_dropdown_query_args', $query_args, $this->field);

				$posts_query = new WP_Query($query_args);

				if ( $posts_query->have_posts() )
				{
					while ( $posts_query->have_posts() )
					{
						$posts_query->the_post();

						$value = $this->field['value_format'];
						$label = $this->field['label_format'];

						// value replacements
						$value = apply_filters('formidable_posts_dropdown_value_pre_replace', $value);

						$value = str_replace("%id%", get_the_ID(), $value);
						$value = str_replace("%title%", get_the_title(), $value);

						if ( preg_match("/%meta_.*?%/", $value, $matches) )
						{
							if ( !empty($matches) )
							{
								foreach ( $matches as $match )
								{
									$meta_key = str_replace("%meta_", '', $match);
									$meta_key = str_replace("%", '', $meta_key);
									$meta_value = get_post_meta(get_the_ID(), $meta_key, TRUE);
									if ( $meta_value == '' )
									{
										$meta_value = get_post_meta(get_the_ID(), '_' . $meta_key, TRUE);
									}
									$value = str_replace($match, $meta_value, $value);
								}
							}
						}

						if ( preg_match("/%taxonomy_.*?%/", $value, $matches) )
						{
							if ( !empty($matches) )
							{
								foreach ( $matches as $match )
								{
									$taxonomy = str_replace("%taxonomy_", '', $match);
									$taxonomy = str_replace("%", '', $taxonomy);
									$terms = wp_get_post_terms( get_the_ID(), $taxonomy, array( 'fields' => 'names' ) );
									$value = str_replace($match, implode(", ", $terms), $value);
								}
							}
						}

						$value = apply_filters('formidable_posts_dropdown_value_post_replace', $value);

						// label replacements
						$label = apply_filters('formidable_posts_dropdown_label_pre_replace', $label);

						$label = str_replace("%id%", get_the_ID(), $label);
						$label = str_replace("%title%", get_the_title(), $label);

						if ( preg_match("/%meta_.*?%/", $label, $matches) )
						{
							if ( !empty($matches) )
							{
								foreach ( $matches as $match )
								{
									$meta_key = str_replace("%meta_", '', $match);
									$meta_key = str_replace("%", '', $meta_key);
									$meta_value = get_post_meta(get_the_ID(), $meta_key, TRUE);
									if ( $meta_value == '' )
									{
										$meta_value = get_post_meta(get_the_ID(), '_' . $meta_key, TRUE);
									}
									$label = str_replace($match, $meta_value, $label);
								}
							}
						}

						if ( preg_match("/%taxonomy_.*?%/", $label, $matches) )
						{
							if ( !empty($matches) )
							{
								foreach ( $matches as $match )
								{
									$taxonomy = str_replace("%taxonomy_", '', $match);
									$taxonomy = str_replace("%", '', $taxonomy);
									$terms = wp_get_post_terms( get_the_ID(), $taxonomy, array( 'fields' => 'names' ) );
									$label = str_replace($match, implode(", ", $terms), $label);
								}
							}
						}

						$label = apply_filters('formidable_posts_dropdown_label_post_replace', $label);

						$option = '<option value="' . $value . '">' . $label . '</option>';

						$options[] = $option;
					}
				}
				wp_reset_postdata();
			}
		}

		return '<select name="' . esc_attr( $args['field_name'] ) . '" id="' . esc_attr( $args['html_id'] ) . '" ' . $input_html . '>' .
			implode("", $options) .
			'</select>';
	}
}
