<?php
function foxtail_pagination() {
    global $wp_query;
    if ( get_query_var('paged') ) {
        $paged = get_query_var('paged'); 
    } elseif ( get_query_var('page') ) { 
        $paged = get_query_var('page'); 
    } else { 
        $paged = 1; 
    }
 
    $total_pages = $wp_query->max_num_pages;

    if ($total_pages > 1){
      echo '<nav class="ft-c-pagination">';
      echo '<div class="ft-c-pagination__wrap">';
      echo paginate_links(array(
          'base' => preg_replace('/\?.*?$/', '', get_pagenum_link(1)) . '%_%',
          'format' => 'page/%#%',
          'current' => $paged,
          'total' => $total_pages,
          'prev_text' => 'previous',
          'next_text' => 'next',
      ));
      echo '</div>';
      echo '</nav>';
    }
}
?>
