<?php
/**
 * Plugin Name: All-in-one Facebook Like Widget
 * Plugin URI: http://www.jeroen.in
 * Description: All-in-one Facebook Like Widget. Add a Like button, stream or facebox (fans) to your site.
 * Version: 1.0
 * Author: Jeroen Peters
 * Author URI: http://www.jeroen.in
 * License: GPL2
 */

add_action('widgets_init','AIO_Facebook_Like_widget_register');
function AIO_Facebook_Like_widget_register()
{
	register_widget('AIO_Facebook_Like_widget');
}

class AIO_Facebook_Like_widget extends WP_Widget
{

	/**
	 * Register WordPress Widget
	 */
	private $widget_title = "Like Us";
	private $facebook_id = "";
	private $facebook_username = "quoteshirts";
	private $facebook_width = "240";
    private $facebook_language = "en_US";
    private $facebook_colorscheme = "light";
    private	$facebook_show_border = "false";
	private $facebook_show_faces = "true";
	private	$facebook_show_stream = "false";
	private	$facebook_show_header = "true";


	public function __construct()
    {
		parent::__construct(
			'aio_facebook_like_widget',
			'AIO Facebook Like Widget',
			array(
				'classname'   => __('aio_facebook_like_widget'),
				'description' => __('All-in-one Facebook Like Widget')
				)
			);
	}

	/**
	 * Front end Display of widgets
	 * @see WP_Widget::widget()
	 *
	 * @param array $args Widget arguments
	 * @param array $instance Saved values from Database
	 */
	public function widget($args, $instance)
    {
		extract($args);

		/* Variables from the widget settings */
		$this->widget_title = apply_filters('widget_title', $instance['title']);

		$this->facebook_id = $instance['app_id'];
		$this->facebook_username = $instance['page_name'];
		$this->facebook_width = $instance['width'];
        $this->facebook_language = $instance['language'];
        $this->facebook_colorscheme = $instance['colorscheme'];
		$this->facebook_show_faces = ($instance['show_faces'] == "1"? "true" : "false");
		$this->facebook_show_stream = ($instance['show_stream'] == "1"? "true": "false");
		$this->facebook_show_header = ($instance['show_header'] == "1"? "true": "false");
		$this->facebook_show_border = ($instance['show_border'] == "1"? "true": "false");

		add_action('wp_footer', array($this, 'aio_fb_like_add_js'));
		
		/* Display the widget title if one was input (before and after defined by the theme) */
		echo $before_widget;

		if($this->widget_title)
        {
		    echo $this->widget_title;
        }

		/* Like Box */
		?>	
			<div class="fb-like-box"
				data-href="http://www.facebook.com/<?php echo $this->facebook_username;?>"
				data-width="<?php echo $this->facebook_width;?>"
				data-show-faces="<?php echo $this->facebook_show_faces;?>"
				data-stream="<?php echo $this->facebook_show_stream;?>"
				data-header="<?php echo $this->facebook_show_header;?>"
				data-show-border="<?php echo $this->facebook_show_border; ?>"
                data-colorscheme="<?php echo $this->facebook_colorscheme; ?>"
				>
			</div>

		<?php
		echo $after_widget;
	}

	/**
     * Add Facebook Javascripts to the webpage
     */
	public function aio_fb_like_add_js()
    {
        echo '
        <div id="fb-root"></div>
        <script>(function(d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) return;
            js = d.createElement(s); js.id = id;
            js.src = "//connect.facebook.net/' . $this->facebook_language . '/all.js#xfbml=1&appId=' . $this->facebook_id . '";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, \'script\', \'facebook-jssdk\'));</script>';
	}

	/**
	 * Sanitize data from values as they are saved
	 * @see WP_Widget::update();
	 */
	public function update($new_instance, $old_instance)
    {
        $facebook_strips = array(
            "facebook.com/",
            "http://facebook.com/",
            "https://facebook.com/",
            "http://www.facebook.com/",
            "https://www.facebook.com/",
        );

		$instance = $old_instance;

		/* Strip tags for title and name to remove HTML (important for text inputs) */
		$instance['title'] = strip_tags($new_instance['title']);
		//$instance['app_id'] = strip_tags($new_instance['app_id']);
        $instance['page_name'] = str_replace($facebook_strips, array(), strip_tags($new_instance['page_name']));

		$instance['width'] = strip_tags($new_instance['width']);
        $instance['language'] = strip_tags($new_instance['language']);
        $instance['colorscheme'] = strip_tags($new_instance['colorscheme']);
        $instance['show_border'] = (bool)$new_instance['show_border'];
		$instance['show_faces'] = (bool)$new_instance['show_faces'];
		$instance['show_stream'] = (bool)$new_instance['show_stream'];
		$instance['show_header'] = (bool)$new_instance['show_header'];

		return $instance;
	}

	/**
	 * Back end widget Form
     * This displays the configuration form for the widget
	 */
	public function form($instance)
    {
        $defaults = array(
            'app_id' => $this->facebook_id,
            'page_name' => $this->facebook_username,
            'width' => $this->facebook_width,
            'language' => $this->facebook_language,
            'colorscheme' => $this->facebook_colorscheme,
            'show_border' => $this->facebook_show_border,
            'show_faces' => $this->facebook_show_faces,
            'show_stream' => $this->facebook_show_stream,
            'show_header' => $this->facebook_show_header,
        );

        $available_languages = array(
            'en_US' => __('English', 'aio-facebook-like-widget'),
            'nl_NL' => __('Dutch', 'aio-facebook-like-widget'),
            'fr_FR' => __('French', 'aio-facebook-like-widget'),
            'ru_RU' => __('Russian', 'aio-facebook-like-widget'),
            'it_IT' => __('Italian', 'aio-facebook-like-widget'),
        );

		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<!-- Widget Title: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>" title="<?php _e('This will be displayed above the Like box', 'aio-facebook-like-widget') ?>"><?php _e('Title', 'aio-facebook-like-widget') ?>:</label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $instance['title']; ?>" />
		</p>

		<!-- App id: Text Input
		Don't need that for now!
		<p>
			<label for="<?php echo $this->get_field_id('app_id'); ?>"><?php _e('App Id', 'aio-facebook-like-widget') ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('app_id'); ?>" name="<?php echo $this->get_field_name('app_id'); ?>" value="<?php echo $instance['app_id']; ?>" />
		</p>
		-->

		<!-- Facebook pagename or id: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id('page_name'); ?>" title="<?php _e('This is the name of your page (the part after http://facebook.com/)', 'aio-facebook-like-widget') ?>"><?php _e('Facebook Page Name (or Id)', 'aio-facebook-like-widget') ?>:</label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('page_name'); ?>" name="<?php echo $this->get_field_name('page_name'); ?>" value="<?php echo $instance['page_name']; ?>" />
		</p>

		<!-- Width: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id('width'); ?>"><?php _e('Width', 'aio-facebook-like-widget') ?>:</label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('width'); ?>" name="<?php echo $this->get_field_name('width'); ?>" value="<?php echo $instance['width']; ?>" />
		</p>

        <!-- Languages: Selectbox input -->
        <p>
            <label for="<?php echo $this->get_field_id('language'); ?>"><?php _e('Language', 'aio-facebook-like-widget') ?>:</label>
            <select id="<?php echo $this->get_field_id('language'); ?>" name="<?php echo $this->get_field_name('language'); ?>">
                <?php
                foreach($available_languages as $lang_code => $lang_name)
                {
                    echo '<option value="' . $lang_code . '"' . ($instance['language'] == $lang_code ? "selected" : "") . '>' . $lang_name . '</option>';
                }
                ?>
            </select>
        </p>

        <!-- Colorscheme: Selectbox input -->
        <p>
            <label for="<?php echo $this->get_field_id('colorscheme'); ?>"><?php _e('Colorscheme', 'aio-facebook-like-widget') ?>:</label>
            <select id="<?php echo $this->get_field_id('colorscheme'); ?>" name="<?php echo $this->get_field_name('colorscheme'); ?>">
                <option value="light" <?php echo ($instance['colorscheme'] == "light" ? "selected" : ""); ?>><?php _e('Light', 'aio-facebook-like-widget') ?></option>
                <option value="dark" <?php echo ($instance['colorscheme'] == "dark" ? "selected" : ""); ?>><?php _e('Dark', 'aio-facebook-like-widget') ?></option>
            </select>
        </p>

        <!-- Show Border: Checkbox Input -->
        <p>
            <input type="checkbox" class="widefat" id="<?php echo $this->get_field_id('show_border'); ?>" name="<?php echo $this->get_field_name('show_border'); ?>" value="1" <?php echo ($instance['show_border'] == "true" ? "checked='checked'" : ""); ?> />
            <label for="<?php echo $this->get_field_id('show_border'); ?>"><?php _e('Border', 'aio-facebook-like-widget') ?></label>
        </p>

		<!-- Show Faces: Checkbox Input -->
		<p>
			<input type="checkbox" class="widefat" id="<?php echo $this->get_field_id('show_faces'); ?>" name="<?php echo $this->get_field_name('show_faces'); ?>" value="1" <?php echo ($instance['show_faces'] == "true" ? "checked='checked'" : ""); ?> />
            <label for="<?php echo $this->get_field_id('show_faces'); ?>"><?php _e('Faces', 'aio-facebook-like-widget') ?></label>
		</p>

		<!-- Show Stream: Checkbox Input -->
		<p>
			<input type="checkbox" class="widefat" id="<?php echo $this->get_field_id('show_stream'); ?>" name="<?php echo $this->get_field_name('show_stream'); ?>" value="1" <?php echo ($instance['show_stream'] == "true" ? "checked='checked'" : ""); ?> />
            <label for="<?php echo $this->get_field_id('show_stream'); ?>"><?php _e('Stream', 'aio-facebook-like-widget') ?></label>
		</p>

		<!-- Show Header: Checkbox Input -->
		<p>
			<input type="checkbox" class="widefat" id="<?php echo $this->get_field_id('show_header'); ?>" name="<?php echo $this->get_field_name('show_header'); ?>" value="1" <?php echo ($instance['show_header'] == "true" ? "checked='checked'" : ""); ?> />
            <label for="<?php echo $this->get_field_id('show_header'); ?>"><?php _e('Header', 'aio-facebook-like-widget') ?></label>
		</p>

		<?php
	}
}