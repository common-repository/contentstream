<!-- success/error messaging for the settings form -->
<div class="notice <?php echo $is_error ? esc_attr('notice-error') : esc_attr('notice-success'); ?> is-dismissible">
    <p>
        <strong>
            <?php echo esc_attr($message); ?>
        </strong>
    </p>
</div>