<?php

/**
 * @Author: suifengtec
 * @Date:   2018-01-24 00:06:33
 * @Last Modified by:   suifengtec
 * @Last Modified time: 2018-01-24 00:44:46
 **/

if (!defined('ABSPATH')) {
	exit;
}

/*

 */
class WP_Orphanage_Plus_Settings {

	public function __construct() {

		add_action('admin_menu', array($this, 'add_options_page'));

	}

	public function add_options_page() {

		add_options_page(__('WP Orphanage Plus', WPOP_SLUG), __('WP Orphanage Plus', WPOP_SLUG), 'manage_options', 'wp-orphanage-plus', array($this, 'settings_page'));

	}

	public function get_roles() {

		global $wpdb;
		$option = $wpdb->prefix . 'user_roles';
		return get_option($option);

	}

	public function settings_page() {

		if (!current_user_can('manage_options')) {
			wp_die(__('You do not have sufficient permissions to access this page.', WPOP_SLUG));
		}

		// Update options
		if (isset($_POST['action']) && $_POST['action'] == 'update') {
			update_option('wporphanageplus_role', $_POST['wporphanageplus_role']);
			if (isset($_POST['wporphanageplus_prefixes']) && is_array($_POST['wporphanageplus_prefixes'])) {
				$prefixes = array();
				foreach ($_POST['wporphanageplus_prefixes'] as $prefix) {
					if (!empty($prefix)) {
						$prefixes[] = $prefix;
					}
				}

				update_option('wporphanageplus_prefixes', $prefixes);
			}

			echo '<div class="updated"><p><strong>' . __('Settings saved', WPOP_SLUG) . '</strong></p></div>';
		}

		$roles = $this->get_roles();
		$wp_orphanageex_role = get_option('wporphanageplus_role');
		$prefixes = get_option('wporphanageplus_prefixes');

		?>
<div class="wrap">
    <h2><?php _e('WP Orphanage', WPOP_SLUG);?></h2>
    <p><?php _e('Set default user role for Adoptees.', WPOP_SLUG);?></p>
    <form method="post" action="" id="wporphanageplus-settings">
        <input type="hidden" name="action" value="update" />
        <table class="form-table">
            <tr valign="top">
                <th scope="row"><label for="wporphanageplus_role"><?php _e('Choose Default Role:', WPOP_SLUG);?></label></th>
                <td>
                    <select name="wporphanageplus_role" id="wporphanageplus_role">
                    <?php wp_dropdown_roles($wp_orphanageex_role);?>
                    </select><br />
                    <small><?php _e('Choose the default role orphan users should be promoted to (if no role to copy from other table was found).', WPOP_SLUG);?></small>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wporphanageplus_prefixes"><?php _e('Add WP Prefixes:', WPOP_SLUG);?></label></th>
                <td>
                    <?php if ($prefixes): ?>
                        <?php $i = 1;?>
                        <?php foreach ($prefixes as $prefix): ?>
                            <?php _e('Prefix', WPOP_SLUG);?> <?php echo $i; ?>: <input name="wporphanageplus_prefixes[]" id="wporphanageplus_prefixes_<?php echo $i; ?>" class="regular-text" type="text" value="<?php echo $prefix; ?>" /><br />
                            <?php $i++;?>
                        <?php endforeach;?>
                    <?php endif;?>
                    <br /><?php _e('Add new:', WPOP_SLUG);?> <input name="wporphanageplus_prefixes[]" id="wporphanageplus_prefixes" class="regular-text" type="text" value="" /><br />
                    <small><?php _e('Add prefixes of all WP installs where to search for user role. To remove field, leave it empty. Default WP prefix is <code>wp_</code> ', WPOP_SLUG);?></small>
                </td>
            </tr>
        </table>
        <?php submit_button();?>
    </form>
</div>

<?php

	}
}
/*EOF*/
