<div class="wrap cs_ar_float_left">
    <form action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>" method="get">
        <!-- hidden inputs for pagination to functionn properly -->
        <input class="cs_ar_no_show" type="text" name="post_type" value="<?php echo esc_attr(csARConfig::CS_ARTICLE_POST_TYPE); ?>" />
        <input class="cs_ar_no_show" type="text" name="page" value="<?php echo esc_attr(csARConfig::CS_SETTINGS_SLUG); ?>" />

        <!-- table header and pagination -->
        <div class="tablenav">
            <h3 class="cs_ar_table_header">Action Log</h3>
            <?php $this->display_pagination($data, true);?>
        </div>

        <!-- table -->
        <table class="widefat fixed striped">
            <thead>
                <tr>
                    <th>Timestamp</th>
                    <th>Action</th>
                    <th>Status</th>
                    <th>Message</th>
                </tr>
            </thead>
            <tbody>
                <!-- if results are not empty, loop through them and create table rows with data in them -->
                <?php if (!empty($data['results'])) {?>
                <?php foreach ($data['results'] as $row) {?>
                <tr>
                    <td>
                        <?php echo esc_attr($this->format_timestamp($row->cs_ar_log_timestamp)); ?>
                    </td>
                    <td>
                        <?php echo esc_attr($this->get_display_name_for_action($row->cs_ar_log_action)); ?>
                    </td>
                    <td>
                        <?php echo esc_attr($row->cs_ar_log_status); ?>
                    </td>
                    <td>
                        <?php echo esc_attr($row->cs_ar_log_results); ?>
                    </td>
                </tr>
                <?php }?>
                <!-- else, show a single row with empty message -->
                <?php } else {?>
                <tr>
                    <td colspan="4">
                        <strong>No records found!</strong>
                    </td>
                </tr>
                <?php }?>
            </tbody>
        </table>
        <!-- lower pagination -->
        <div class="tablenav">
            <?php $this->display_pagination($data);?>
        </div>
        <!-- hidden button for pagination, allows user to hit enter when typing in a page number -->
        <input class="cs_ar_no_show" type="submit" value="Submit" />
    </form>
</div>