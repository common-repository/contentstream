<div class="wrap cs_ar_float_left">
    <h1 class="wp-heading-inline">ContentStream Articles</h1>
    <?php if (isset($_GET['s']) && $_GET['s'] !== '') {?><span class="subtitle">Search results for “<?php echo esc_attr($_GET['s']); ?>”</span><?php }?>

    <form action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>" method="get">
        <input style="display: none;" type="text" name="page" value="<?php echo 'cs_article_listing'; ?>" />

        <h2 class="screen-reader-text">Filter contentstream article list</h2>
        <ul class="subsubsub">
            <li class="all"><a href="/wp-admin/admin.php?page=cs_article_listing">All <span class="count">(<?php echo esc_attr($data['true_total']); ?>)</span></a> |</li>
            <?php if ($data['published_total'] > 0) {?>
                <li class="publish">
                    <a href="/wp-admin/admin.php?page=cs_article_listing&post_status=publish">Published <span class="count">(<?php echo esc_attr($data['published_total']); ?>)</span></a>
                    <?php if ($data['trashed_total'] > 0) {?> |<?php }?>
                </li>
            <?php }?>
            <?php if ($data['trashed_total'] > 0) {?>
                <li class="trash">
                    <a href="/wp-admin/admin.php?page=cs_article_listing&post_status=trash">Trash <span class="count">(<?php echo esc_attr($data['trashed_total']); ?>)</span></a>
                </li>
            <?php }?>
        </ul>

        <p class="search-box">
            <label class="screen-reader-text" for="post-search-input">Search ContentStream Articles:</label>
            <input type="search" id="post-search-input" name="s" value="<?php if (isset($_GET['s']) && $_GET['s'] !== '') {echo esc_attr($_GET['s']);}?>">
            <input type="submit" id="search-submit" class="button" value="Search ContentStream Articles">
        </p>

        <div class="tablenav top">
            <div class="alignleft actions">
                <label for="filter-by-date" class="screen-reader-text">Filter by date</label>
                <select name="m" id="filter-by-date">
                    <option value="0">All dates</option>
                    <?php foreach ($data['date_dropdown'] as $date_item) {?>
                        <option <?php if ((int) $_GET['m'] === (int) $date_item['value']) {echo esc_attr('selected');}?> value="<?php echo esc_attr($date_item['value']); ?>"><?php echo esc_attr($date_item['label']); ?></option>
                    <?php }?>
                </select>
                <label class="screen-reader-text" for="cat">Filter by category</label>
                <?php wp_dropdown_categories(array(
    'show_option_all' => 'All Categories',
    'orderby' => 'label',
    'order' => 'ASC',
    'selected' => (isset($_GET['cat']) && $_GET['cat'] !== '' && $_GET['cat'] !== '0') ? $_GET['cat'] : ''));?>
                <input type="submit" name="filter_action" id="post-query-submit" class="button" value="Filter">
            </div>

            <?php $this->display_pagination($data, true);?>
        </div>

        <table class="wp-list-table widefat fixed striped posts">
            <thead>
                <tr>
                    <th class="manage-column column-title column-primary <?php echo (isset($_GET['orderby']) && $_GET['orderby'] === 'title') ? esc_attr('sorted') : esc_attr('sortable'); ?> <?php echo (isset($_GET['order']) && $_GET['order'] === 'asc') ? esc_attr('asc') : esc_attr('desc'); ?>"><a href="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>&orderby=title&order=<?php echo (isset($_GET['order']) && $_GET['order'] === 'asc') ? esc_attr('desc') : esc_attr('asc'); ?>"><span>Title</span><span class="sorting-indicator"></span></a></th>
                    <th class="manage-column column-title">Author</th>
                    <?php foreach (get_object_taxonomies('post') as $taxonomy) {?>
                        <?php if ($taxonomy !== 'post_format') {?>
                            <th class="manage-column column-title"><?php echo esc_attr(get_taxonomy($taxonomy)->label); ?></th>
                        <?php }?>
                    <?php }?>
                    <th class="manage-column column-title column-primary <?php echo (isset($_GET['orderby']) && $_GET['orderby'] === 'date') ? esc_attr('sorted') : esc_attr('sortable'); ?> <?php echo (isset($_GET['order']) && $_GET['order'] === 'asc') ? esc_attr('asc') : esc_attr('desc'); ?>"><a href="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>&orderby=date&order=<?php echo (isset($_GET['order']) && $_GET['order'] === 'asc') ? esc_attr('asc') : esc_attr('desc'); ?>"><span>Date</span><span class="sorting-indicator"></span></a></th>
                </tr>
            </thead>
            <tbody id="the-list">
                <?php if (!empty($posts)) {?>
                <?php foreach ($posts as $post) {?>
                <tr>
                    <td>
                        <strong>
                            <?php if ($post->post_status !== 'trash') {?>
                                <a class="row-title" href="/wp-admin/post.php?post=<?php echo esc_attr($post->ID); ?>&amp;action=edit" aria-label="“<?php echo esc_attr($post->post_title); ?>” (Edit)">
                            <?php }?>
                            <?php echo esc_attr($post->post_title); ?>
                            <?php if ($post->post_status !== 'trash') {?>
                                </a>
                            <?php }?>
                        </strong>
                        <?php if ($post->post_status !== 'trash') {?>
                            <div class="row-actions">
                                <span class="edit"><a href="/wp-admin/post.php?post=<?php echo esc_attr($post->ID); ?>&amp;action=edit" aria-label="Edit “<?php echo esc_attr($post->post_title); ?>”">Edit</a> | </span>
                                <span class="trash"><a href="<?php echo wp_nonce_url('/wp-admin/post.php?post=' . $post->ID . '&amp;action=trash', 'trash-post_' . $post->ID); ?>" class="submitdelete" aria-label="Move “<?php echo esc_attr($post->post_title); ?>” to the Trash">Trash</a> | </span>
                                <span class="view"><a href="<?php echo esc_url(get_permalink($post->ID)); ?>" rel="bookmark" aria-label="View “<?php echo esc_attr($post->post_title); ?>”">View</a></span>
                            </div>
                        <?php } else {?>
                            <div class="row-actions">
                                <span class="untrash"><a href="<?php echo wp_nonce_url('/wp-admin/post.php?post=' . $post->ID . '&amp;action=untrash', 'untrash-post_' . $post->ID); ?>" aria-label="Restore “<?php echo esc_attr($post->post_title); ?>” from the Trash">Restore</a> | </span>
                                <span class="delete"><a href="<?php echo wp_nonce_url('/wp-admin/post.php?post=' . $post->ID . '&amp;action=delete', 'delete-post_' . $post->ID); ?>" class="submitdelete" aria-label="<?php echo esc_attr($post->post_title); ?>” permanently">Delete Permanently</a></span>
                            </div>
                        <?php }?>
                    </td>
                    <td>
                        <a href="edit.php?post_type=post&amp;author=<?php echo esc_attr($post->post_author); ?>"><?php echo esc_attr(get_author_name($post->post_author)); ?></a>
                    </td>
                    <?php foreach (get_object_taxonomies('post') as $taxonomy) {?>
                        <?php if ($taxonomy !== 'post_format') {?>
                            <td><?php $terms = get_the_term_list($post->ID, $taxonomy, '', ', ', '');
    echo empty($terms) ? esc_attr('—') : $terms;?></td>
                        <?php }?>
                    <?php }?>
                    <td>
                        <?php echo $post->post_status === 'publish' ? esc_attr('Published') : esc_attr('Last Modified');
    echo '<br />';
    echo esc_attr($this->format_timestamp($post->post_date)); ?>
                    </td>
                </tr>
                <?php }?>
                <!-- else, show a single row with empty message -->
                <?php } else {?>
                <tr>
                    <td colspan="<?php echo (3 + (count(get_object_taxonomies('post')) - 1)); ?>">
                        <strong>No ContentStream Articles found!</strong>
                    </td>
                </tr>
                <?php }?>
            </tbody>
        </table>

        <div class="tablenav bottom">
            <?php $this->display_pagination($data);?>
        </div>
    </form>
</div>
