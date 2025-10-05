<?php

/**
 * Plugin Name: Remove Scaled Images 
 * Description: Copies images ending with "-scaled" to remove the suffix **only if the target file exists**, overwriting the target. Original -scaled files remain untouched.
 * Version:     1.0
 * License:     GPL2
 */

if (! defined('ABSPATH')) exit;

class WP_Remove_Scaled_Images
{

    public function __construct()
    {
        add_action('admin_menu', [$this, 'add_admin_page']);
    }

    public function add_admin_page()
    {
        add_management_page(
            'Remove -scaled Images',
            'Remove -scaled Images',
            'manage_options',
            'remove-scaled-images',
            [$this, 'render_admin_page']
        );
    }

    public function render_admin_page()
    {
        if (isset($_POST['remove_scaled_run'])) {
            $this->process_files();
        }

?>
        <div class="wrap">
            <h1>Remove Scaled Images</h1>
            <form method="post">
                <p>This will scan <code>wp-content/uploads</code> for files ending in <code>-scaled</code> and copy them to the non-scaled filename **only if that file already exists**, overwriting it. Original files remain untouched.</p>
                <?php submit_button('Run Cleanup', 'primary', 'remove_scaled_run'); ?>
            </form>
        </div>
<?php
    }

    private function process_files()
    {
        $base = WP_CONTENT_DIR . '/uploads';

        if (! is_dir($base)) {
            echo '<div class="error notice"><p>Directory not found: ' . esc_html($base) . '</p></div>';
            return;
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($base, RecursiveDirectoryIterator::SKIP_DOTS)
        );

        $count = 0;
        foreach ($iterator as $file) {
            $path = $file->getPathname();
            $basename = $file->getBasename();

            // Match files ending with -scaled and at least one extension
            if (preg_match('/-scaled(\.[^.]+)+$/', $basename)) {
                $newname = preg_replace('/-scaled(\.[^.]+)+$/', '$1', $basename);
                $newpath = $file->getPath() . DIRECTORY_SEPARATOR . $newname;

                // Only copy if the target exists
                if (file_exists($newpath)) {
                    if (@copy($path, $newpath)) {
                        echo '<p>Copied & replaced: ' . esc_html($basename) . ' → ' . esc_html($newname) . '</p>';
                        $count++;
                    }
                }
            }
        }

        echo '<div class="updated notice"><p>Done! Copied & replaced ' . intval($count) . ' file(s).</p></div>';
    }
}

new WP_Remove_Scaled_Images();
