<?php
/*
Plugin Name: RSS Aggregator with Automated Summarization & Dynamic Taglines
Description: This WordPress plugin automatically fetches the latest headlines from RSS feeds and generates article summaries using the Diffbot API. It also updates the WordPress tagline every 2 hours, showing different taglines on various pages. Summaries are displayed on the "Latest Headlines" page, along with the last update time. Two cron jobs manage the process, running every 2 hours to keep content fresh and engaging.
Version: 1.0
Author: Ansar
*/

function girlnews_get_all_links_from_item_feed(){
    $args = array(
        'post_type'      => 'wprss_feed_item',
        'posts_per_page' => 10,
        'orderby'        => 'date',
        'order'          => 'DESC',
        'fields'         => 'ids'
    );

    $posts_data = [];

    $feed_query = new WP_Query( $args );
    if ( $feed_query->have_posts() ) {
        while ( $feed_query->have_posts() ) {
            $post_id = get_the_ID();
            $feed_query->the_post();
            $posts_data[] = get_permalink();

        }
        wp_reset_postdata();
    }

    return $posts_data;
}

function girlnews_fetch_article_summaries_from_diffbot($article_urls) {
    $api_token = 'e19976c5cac5093e8073586862090006'; 
    $summaries = [];

    foreach ($article_urls as $url) {
        $curl = curl_init();
        $encoded_url = urlencode($url);
        $api_url = "https://api.diffbot.com/v3/article?url=$encoded_url&discussion=false&naturalLanguage=summary&summaryNumSentences=5&token=$api_token";

        curl_setopt_array($curl, [
            CURLOPT_URL => $api_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 40,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => [
                "accept: application/json"
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            $summaries[] = "cURL Error: " . $err;
        } else {
            $data = json_decode($response, true);

            if (!empty($data['objects'][0])) {
                $article = $data['objects'][0];
                $headline = $article['title'];
                $summary = wp_trim_words($article['text'], 50, '...');
                $summaries[] = [
                    'headline' => $headline,
                    'summary' => $summary
                ];
            } else {
                $summaries[] = "No article data found for: " . $url;
            }
        }
    }

    return $summaries;
}


add_action('diffbot_cron_event', 'girlnews_handle_diffbot_cron_job');
function girlnews_handle_diffbot_cron_job() {

    $links = girlnews_get_all_links_from_item_feed();
    $summaries = girlnews_fetch_article_summaries_from_diffbot($links);
    set_transient('diffbot_article_summaries', $summaries);

    error_log(print_r($summaries, true));
}

add_shortcode('diffbot_summaries', 'girlnews_display_summaries');
function girlnews_display_summaries() {

    $summaries = get_transient('diffbot_article_summaries');
   
    $output = '<ul class="cus_summary_ul">';
    foreach ($summaries as $summary => $value) {
        if (is_array($summaries)) {
            $output .= '<li><strong>' . esc_html($value['headline']) . ':</strong><label> '. esc_html($value['summary']) . '</label></li>';
        } 
    }
    $output .= '</ul>';


    return $output;
}

add_filter('pre_option_blogdescription', 'custom_girl_tagline_for_specific_page');
function custom_girl_tagline_for_specific_page($default_description) {
    if (is_page('latest-headlines')) {
        return 'The day’s headlines about girls.';
    }
    return $default_description;
}

function print_pa($arrays){
    echo "<pre>";
    print_r($arrays);
    echo "</pre>";
}


// Update WordPress Tagline Cron Job
add_action('update_tagline_event', 'girlnews_update_tagline');
function girlnews_update_tagline() {
    $current_time = current_time('timestamp');
    $updated_tagline = 'The day’s headlines about girls. Last updated ' . date('l, F j, Y h:i A', $current_time) . ' (PT).';
    update_option('blogdescription', $updated_tagline);
}

register_activation_hook(__FILE__, 'girlnews_activate_plugin');
function girlnews_activate_plugin() {
    
    if (!wp_next_scheduled('diffbot_cron_event')) {
        wp_schedule_event(time(), 'two_hours', 'diffbot_cron_event');
    }

    if (!wp_next_scheduled('update_tagline_event')) {
        wp_schedule_event(time(), 'two_hours', 'update_tagline_event');
    }
}

register_deactivation_hook(__FILE__, 'girlnews_deactivate');
function girlnews_deactivate() {
    $timestamp = wp_next_scheduled('update_tagline_event');
    if ($timestamp) {
        wp_unschedule_event($timestamp, 'update_tagline_event');
    }

    $timestamp = wp_next_scheduled('diffbot_cron_event');
    if ($timestamp) {
        wp_unschedule_event($timestamp, 'diffbot_cron_event');

    }
}