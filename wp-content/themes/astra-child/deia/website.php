<?php
// Retrieves the musician_bio custom field, removes HTML tags, and limits its length to the specified number of characters (EXCERPTS)
function get_limited_musician_bio($post_id, $limit = 400) {
    $musician_bio = get_post_meta($post_id, 'musician_bio', true);
    if (!empty($musician_bio)) {
        $musician_bio = wp_strip_all_tags($musician_bio); // Remove HTML tags
        if (strlen($musician_bio) > $limit) {
            $musician_bio = substr($musician_bio, 0, $limit) . '...';
        }
    }
    return $musician_bio;
}


// Add a filter to modify the search query and only search for title when on Musicians category
function deia_search_by_title_only($search, $wp_query) {
    global $wpdb;
    if (!empty($search) && !is_admin()) {
        $search = '';
        $search_terms = $wp_query->query_vars['s'];
        if (!empty($search_terms)) {
            $search_terms = is_array($search_terms) ? $search_terms : array($search_terms);
            foreach ($search_terms as $search_term) {
                $search_term = esc_sql($wpdb->esc_like($search_term));
                $search .= " AND ({$wpdb->posts}.post_title LIKE '%{$search_term}%')";
            }
        }
    }
    return $search;
}


// Modify the main query on search to exclude pages
function deia_modify_search_query($query) {
    if ($query->is_search && !is_admin()) {
        // Set post type to 'post' only
        $query->set('post_type', 'post');
    }
}
add_action('pre_get_posts', 'deia_modify_search_query');


// Filter search query for musicians category to search by title only
function deia_filter_musician_category_search($query) {
    if ($query->is_search && !is_admin() && $query->is_main_query()) {
        $musician_cat_id = get_cat_ID('musicians');
        $current_cat_id = (isset($_GET['cat'])) ? (int)$_GET['cat'] : 0;
        if ($current_cat_id === $musician_cat_id) {
            add_filter('posts_search', 'deia_search_by_title_only', 10, 2);
        }
    }
}
add_action('pre_get_posts', 'deia_filter_musician_category_search');


// // Add a filter to modify the search query and only search for title when on Musicians cat
// function deia_search_by_title_only($search, $wp_query) {
//     global $wpdb;
//     if (!empty($search) && !is_admin()) {
//         $search = '';
//         $search_terms = $wp_query->query_vars['s'];
//         if (!empty($search_terms)) {
//             $search_terms = is_array($search_terms) ? $search_terms : array($search_terms);
//             foreach ($search_terms as $search_term) {
//                 $search_term = esc_sql($wpdb->esc_like($search_term));
//                 $search .= " AND ({$wpdb->posts}.post_title LIKE '%{$search_term}%')";
//             }
//         }
//     }
//     return $search;
// }


// // Modify the main query on search to exclude pages
// function deia_modify_search_query($query) {
//     if ($query->is_search && !is_admin()) {
//         // Set post type to 'post' only
//         $query->set('post_type', 'post');
//     }
// }
// add_action('pre_get_posts', 'deia_modify_search_query');
