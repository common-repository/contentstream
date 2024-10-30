<!-- if results are empty, don't show pagination -->
<?php if (!empty($data['results'])) {?>
<div class="tablenav-pages">
    <!-- show user how many results there are -->
    <span class="displaying-num">
        <?php echo esc_attr($data['total']) . ' ';
    echo ((int) $data['total'] === 1) ? esc_attr('item') : esc_attr('items'); ?></span>
    <!-- if the total number of results is greater then 20, show rest of pagination functionality -->
    <?php if ($data['total'] > 20) {?>
    <span class="pagination-links">
        <!-- if user is on page 1 or 2, don't allow them to use this link -->
        <?php if ($data['page_num'] !== 1 && $data['page_num'] !== 2) {?>
        <a class="first-page" href="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>&pagenum=1">
            <?php }?>
            <!-- if user is on page 1 or 2, show disactive state -->
            <span class="<?php if ($data['page_num'] === 1 || $data['page_num'] === 2) {echo esc_attr('tablenav-pages-navspan');}?>" aria-hidden="true">«</span>
            <!-- if user is on page 1 or 2, don't allow them to use this link -->
            <?php if ($data['page_num'] !== 1 && $data['page_num'] !== 2) {?>
        </a>
        <?php }?>
        <!-- if user is on page 1, don't allow them to use this link -->
        <?php if ($data['page_num'] !== 1) {?>
        <a class="prev-page" href="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>&pagenum=<?php echo esc_attr(($data['page_num'] - 1)); ?>">
            <?php }?>
            <!-- if user is on page 1, show disactive state -->
            <span class="<?php if ($data['page_num'] === 1) {echo 'tablenav-pages-navspan';}?>" aria-hidden="true">‹</span>
            <!-- if user is on page 1, don't allow them to use this link -->
            <?php if ($data['page_num'] !== 1) {?>
        </a>
        <?php }?>
        <!-- if add_input is set to true, build the middle section of the pagination with an input for the user to use to change what page they are on -->
        <?php if ($add_input) {?>
        <input class="current-page" id="current-page-selector" type="text" name="pagenum" value="<?php echo esc_attr($data['page_num']); ?>"
            size="2" aria-describedby="table-paging">
        <span class="tablenav-paging-text"> of
            <span class="total-pages">
                <?php echo esc_attr($data['num_of_pages']); ?>
            </span>
        </span>
        <!-- else, build a static middle section that just displays what page the user is on -->
        <?php } else {?>
        <span id="table-paging" class="paging-input">
            <span class="tablenav-paging-text">
                <?php echo esc_attr($data['page_num']); ?> of
                <span class="total-pages">
                    <?php echo esc_attr($data['num_of_pages']); ?>
                </span>
            </span>
        </span>
        <?php }?>
        <!-- if user is on the last page, don't allow them to use this link -->
        <?php if ($data['page_num'] !== $data['num_of_pages']) {?>
        <a class="next-page" href="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>&pagenum=<?php echo esc_attr(($data['page_num'] + 1)); ?>">
            <?php }?>
            <!-- if user is on the last page, show disactive state -->
            <span class="<?php if ($data['page_num'] === $data['num_of_pages']) {echo esc_attr('tablenav-pages-navspan');}?>" aria-hidden="true">›</span>
            <!-- if user is on the last page, don't allow them to use this link -->
            <?php if ($data['page_num'] !== $data['num_of_pages']) {?>
        </a>
        <?php }?>
        <!-- if user is on the last page or second to last page, don't allow them to use this link -->
        <?php if ($data['page_num'] !== $data['num_of_pages'] && $data['page_num'] !== ($data['num_of_pages'] - 1)) {?>
        <a class="last-page" href="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>&pagenum=<?php echo esc_attr($data['num_of_pages']); ?>">
            <?php }?>
            <!-- if user is on the last page or second to last page, show disactive state -->
            <span class="<?php if ($data['page_num'] === $data['num_of_pages'] || $data['page_num'] === ($data['num_of_pages'] - 1)) {echo esc_attr('tablenav-pages-navspan');}?>"
                aria-hidden="true">»</span>
            <!-- if user is on the last page or second to last page, don't allow them to use this link -->
            <?php if ($data['page_num'] !== $data['num_of_pages'] && $data['page_num'] !== ($data['num_of_pages'] - 1)) {?>
        </a>
        <?php }?>
    </span>
    <?php }?>
</div>
<?php }?>