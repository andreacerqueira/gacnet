<?php
/*
Plugin Name: Deia YouTube Data API Integration
Description: Plugin to integrate YouTube Data API with WordPress
Version: 1.0
Author: Andrea Amado de Cerqueira
*/

// Set your API key globally
$apiKey = 'AIzaSyDvcYFrTnwWNJEfe1rTOdPmO2v6M0i1NW8';

// Common cURL options
$curlOptions = array(
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true, // Follow redirects
    CURLOPT_SSL_VERIFYPEER => false, // Disabling SSL verification (for testing purposes only)
);

// Function to retrieve YouTube playlists
function get_youtube_data($playlistId = null) {
    global $apiKey, $curlOptions;

    // Set the endpoint URL
    $endpoint = 'https://www.googleapis.com/youtube/v3/';

    // Set the parameters for the API request
    $params = array(
        'part' => 'snippet',
        'maxResults' => 50, // Maximum number of results per page
        'key' => $apiKey,
    );

    if ($playlistId) {
        $params['playlistId'] = $playlistId;
        $endpoint .= 'playlistItems';
    } else {
        $params['channelId'] = 'UCbw85HIvNBkAuyawpAmZ__A'; // Replace with your channel ID
        $endpoint .= 'playlists';
    }

    // Build the query string
    $queryString = http_build_query($params);

    // Construct the full URL with the query string
    $requestUrl = $endpoint . '?' . $queryString;

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
            return 'No playlists found.';
        } else {
            // API request was successful, return the response data
            return $responseData;
        }
    }

    // Close cURL session
    curl_close($curl);
}

// Function to display playlists and their videos/podcasts
function display_youtube_data() {
    $playlistsData = get_youtube_data();

    // Check if playlists were retrieved successfully
    if (!is_array($playlistsData) || !isset($playlistsData['items'])) {
        return 'No playlists found.';
    }

    // Process and return playlists
    $output = '<div class="deia-playlist-wrapper">';
    $output .= '<ul class="deia-playlist">';
    foreach ($playlistsData['items'] as $playlist) {
        $output .= '<li>';
        $output .= '<h3>' . $playlist['snippet']['title'] . '</h3>';

        // Get videos/podcasts for this playlist
        $videosData = get_youtube_data($playlist['id']);
        if (isset($videosData['items'])) {
            $output .= '<ul class="deia-videos">';
            foreach ($videosData['items'] as $video) {
                // thumbnail
                $thumbnail = isset($video['snippet']['thumbnails']['high']['url']) ? $video['snippet']['thumbnails']['high']['url'] : ''; // Check if high quality thumbnail is available
                
                // Fetch video statistics
                $videoStats = get_video_statistics($video['snippet']['resourceId']['videoId']);

                // Display video information
                $output .= '<li>';
                $output .= '<a href="https://www.youtube.com/watch?v=' . $video['snippet']['resourceId']['videoId'] . '">';
                $output .= '<img src="' . $thumbnail . '" alt="' . $video['snippet']['title'] . '">';
                $output .= '<span>' . $video['snippet']['title'] . '</span>';
                $output .= '<span>Views: ' . $videoStats['viewCount'] . '</span>';
                $output .= '<span>Likes: ' . $videoStats['likeCount'] . '</span>';
                $output .= '</a>';
                $output .= '</li>';
            }
            $output .= '</ul>';
        } else {
            $output .= '<p>No videos found for this playlist.</p>';
        }

        $output .= '</li>';
    }
    $output .= '</ul>';
    $output .= '</div>';
    return $output;
}

// Function to fetch video statistics
function get_video_statistics($videoId) {
    global $apiKey, $curlOptions;

    // Set the endpoint URL
    $endpoint = 'https://www.googleapis.com/youtube/v3/videos';

    // Set the parameters for the API request
    $params = array(
        'part' => 'statistics',
        'id' => $videoId,
        'key' => $apiKey,
    );

    // Build the query string
    $queryString = http_build_query($params);

    // Construct the full URL with the query string
    $requestUrl = $endpoint . '?' . $queryString;

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
            return array('viewCount' => 0, 'likeCount' => 0); // Default values if no data found
        } else {
            // Extract statistics
            $statistics = $responseData['items'][0]['statistics'];
            $viewCount = isset($statistics['viewCount']) ? $statistics['viewCount'] : 0;
            $likeCount = isset($statistics['likeCount']) ? $statistics['likeCount'] : 0;
            return array('viewCount' => $viewCount, 'likeCount' => $likeCount);
        }
    }

    // Close cURL session
    curl_close($curl);
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
?>