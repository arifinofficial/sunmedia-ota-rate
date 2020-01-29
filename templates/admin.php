<div class="wrap">
    <?php settings_errors(); ?>

    <form action="options.php" method="POST">
        <?php 
        settings_fields('cota-settings');
        do_settings_sections('cota-settings');
        submit_button();
        ?>
    </form>
</div>