<?php
function foxtail_pagination() {
    global $wp_query;

    $total_pages = $wp_query->max_num_pages;

    if ($total_pages > 1){
      $current_page = max(1, get_query_var('paged'));

      echo '<nav class="ft-c-pagination">';
      echo '<div class="ft-c-pagination__wrap">';
      echo paginate_links(array(
        'base' => preg_replace('/\?.*?$/', '', get_pagenum_link(1)) . '%_%',
        'format' => 'page/%#%',
        // 'current' => $current_page,
        'current' => max(1, get_query_var('paged')),
        'total' => $total_pages,
        'prev_text' => 'previous',
        'next_text' => 'next',
      ));
      echo '</div>';
      echo '</nav>';
    }
}
?>
