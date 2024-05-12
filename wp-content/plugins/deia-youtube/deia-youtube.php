<?php
/*
Plugin Name: Deia YouTube Data API Integration
Description: Plugin to integrate YouTube Data API with WordPress
Version: 1.0
Author: Andrea Amado de Cerqueira
*/

// Global data
$apiKey = 'AIzaSyDvcYFrTnwWNJEfe1rTOdPmO2v6M0i1NW8';
$playlistId = 'PL11CWRUEY2g5TcnY-aUBD_cGCAJ2ebNGI';

// Common cURL options
$curlOptions = array(
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true, // Follow redirects
    CURLOPT_SSL_VERIFYPEER => false, // Disabling SSL verification (for testing purposes only)
);

// Function to retrieve YouTube playlists with pagination support
function get_youtube_data($pageToken = '') {
    global $apiKey, $playlistId, $curlOptions;

    // Set the endpoint URL
    $endpoint = 'https://www.googleapis.com/youtube/v3/playlistItems';

    // Set the parameters for the API request
    $params = array(
        'part' => 'snippet',
        'playlistId' => $playlistId, // Specify the playlist ID
        'maxResults' => 10, // Maximum number of results per page
        'key' => $apiKey
    );

    // Add pageToken if provided
    if (!empty($pageToken)) {
        $params['pageToken'] = $pageToken;
    }

    // Build the query string
    $queryString = http_build_query($params);

    // Construct the full URL with the query string
    $requestUrl = $endpoint . '?' . $queryString;

    // echo "<pre>" . $_REQUEST['pageToken'] . "</pre>";
    // echo "<pre>" . $pageToken . "</pre>";
    // echo $requestUrl;

    // Initialize cURL session
    $curl = curl_init();

    // Set cURL options
    curl_setopt_array($curl, $curlOptions + array(
        CURLOPT_URL => $requestUrl,
    ));

    // Execute the API request
    $response = curl_exec($curl);

    // Check for errors
    if ($response === false) {
        $error = curl_error($curl);
        return 'Error making API request: ' . $error;
    } else {
        // Decode the JSON response
        $responseData = json_decode($response, true);

        // Check if API request was successful
        if (isset($responseData['error'])) {
            return 'API request failed: ' . $responseData['error']['message'];
        } elseif (!isset($responseData['items']) || empty($responseData['items'])) {
            return 'No videos found for the playlist.';
        } else {
            // API request was successful, return the response data
            return $responseData;
        }
    }

    // Close cURL session
    curl_close($curl);
}

// Function to display all videos from the playlist with pagination support
function display_youtube_data() {
    // Check if the current page is the Podcast page
    if (is_page('Podcast')) {
        // Get the page token from the GET parameters
        $pageToken = isset($_GET['pageToken']) ? $_GET['pageToken'] : '';

        // Fetch videos data from YouTube API
        $videosData = get_youtube_data($pageToken);

        // Check if videos were retrieved successfully
        if (!is_array($videosData) || !isset($videosData['items'])) {
            return 'No videos found for the playlist.';
        }

        // Process and return all videos
        $output = '<div class="deia-playlist-wrapper">';
        $output .= '<ul class="deia-videos">';

        // Loop through each video item
        foreach ($videosData['items'] as $video) {
            // Check if snippet is set
            if (isset($video['snippet'])) {
                // Check if snippet contains required data
                if (isset($video['snippet']['resourceId']['videoId'])) {
                    $videoId = $video['snippet']['resourceId']['videoId'];
                    $title = $video['snippet']['title'];
                    $description = $video['snippet']['description'];
                    $thumbnail = isset($video['snippet']['thumbnails']['medium']['url']) ? $video['snippet']['thumbnails']['medium']['url'] : ''; // Check if thumbnail is available

                    // Display video information
                    $output .= '<li>';
                    $output .= '    <a href="https://www.youtube.com/watch?v=' . $videoId . '" target="_blank">';
                    $output .= '        <div class="thumbnail" style="background-image:url(' . $thumbnail . ')"></div>';
                    $output .= '        <div>';
                    $output .= '            <h3>' . $title . '</h3>';
                    $output .= '            <div>' . $description . '</div>';
                    $output .= '        </div>';
                    $output .= '    </a>';
                    // Add share icons
                    $output .= '    <div class="share-icons">';
                    $output .= '        <a href="https://twitter.com/intent/tweet?url=' . urlencode('https://www.youtube.com/watch?v=' . $videoId) . '&text=' . urlencode($title) . '" target="_blank"><img src="' . plugin_dir_url(__FILE__) . 'img/icon-twitter.svg"/></a>';
                    $output .= '        <a href="https://www.facebook.com/sharer/sharer.php?u=' . urlencode('https://www.youtube.com/watch?v=' . $videoId) . '" target="_blank"><img src="' . plugin_dir_url(__FILE__) . 'img/icon-facebook.svg"/></a>';
                    $output .= '        <a href="https://www.tiktok.com/share/video/' . $videoId . '" target="_blank"><img src="' . plugin_dir_url(__FILE__) . 'img/icon-tiktok.svg"/></a>';
                    $output .= '        <a href="https://wa.me/?text=' . urlencode('Check out this video: https://www.youtube.com/watch?v=' . $videoId) . '" target="_blank"><img src="' . plugin_dir_url(__FILE__) . 'img/icon-whatsapp.svg"/></a>';
                    // $output .= '     <a href="instagram' . urlencode('https://www.youtube.com/watch?v=' . $videoId) . '" target="_blank"><img src="' . plugin_dir_url(__FILE__) . 'img/icon-instagram.svg"/></a>';
                    $output .= '    </div>';
                    // End share code
                    $output .= '</li>';
                }
            }
        }

        $output .= '</ul>';
        $output .= '</div>';

        // Add pagination links based on nextPageToken and prevPageToken
        $output .= '<div class="pagination">';
        if (isset($videosData['prevPageToken'])) {
            $output .= '<a href="?pageToken=' . $videosData['prevPageToken'] . '"><< Previous Page</a>';
        }
        if (isset($videosData['nextPageToken'])) {
            $output .= '<a href="?pageToken=' . $videosData['nextPageToken'] . '">Next Page >></a>';
        }
        $output .= '</div>';

        return $output;
    } else {
        return ''; // Return empty string if not on the Podcast page
    }
}

// Shortcode to display playlists and their videos/podcasts
add_shortcode('youtube_data', 'display_youtube_data');

// Enqueue CSS styles
function enqueue_plugin_styles() {
    // Path to your CSS file
    $css_url = plugins_url('deia-youtube.css', __FILE__);
    
    // Enqueue the CSS file
    wp_enqueue_style('deia-youtube', $css_url, array(), '1.0', 'all');
}

// Hook into WordPress enqueueing system
add_action('wp_enqueue_scripts', 'enqueue_plugin_styles');
