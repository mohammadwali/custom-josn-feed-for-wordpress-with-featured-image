<?php
  /*
   Plugin Name: Json feed reader
   Description: a plugin to fetch custom Json feed.
   Version: 1.0
   Author: Mohammad wali
   Author URI: http://mohammadwali.com
   License: GPL2
   */
    $post_perpage = 4;
    if($_GET["json_feed"] && $_GET["json_feed"]==true || $_POST["json_feed"] && $_POST["json_feed"] == true ):
    global $wpdb;
    $json_data = array();
    $json_data["status"] = "ok";
    $json_data["count"] = wp_count_posts()->publish;
    $json_data["pages"] = intval($json_data["count"] / $post_perpage) <= 0 ? 1 :
    intval($json_data["count"] / $post_perpage);
    $json_data["posts"] = array();
    $query ="SELECT * FROM ".$wpdb->posts." WHERE ".$wpdb->posts.".post_type = 'post' AND ".$wpdb->posts.".post_status = 'publish' ORDER BY post_date DESC";
    $results = $wpdb->get_results($query);
    foreach ($results as $i => $result) {
        $feat_id = get_post_thumbnail_id($result->ID);
        $feat_image = wp_get_attachment_image_src( $feat_id, "full")[0];
        $feat_image = $feat_image == null ? array() :
        array($feat_image);
        $feat_slug = explode("/", $feat_image[0]);
        $feat_slug = $feat_slug[count($feat_slug) - 1];
        $json_data["posts"][] = array(  
        	"id" => $result->ID,
        	"type" => $result->post_type,
        	"slug" => $result->post_name,  
        	"url" => get_permalink( $result->ID ),  
        	"status" => $result->post_status,  
        	"title" => $result->post_title,  
        	"comment_count" => $result->comment_count,  
        	"attachments" => array(    "id" => $feat_id,    
        								"url" => $feat_image[0],    
        								"slug" => $feat_slug, 
								      	/* 
								        "title" => "",
								        "description" => "",
								        "caption" => "",
								        "parent" => "",
								        */
        								"mime_type" => get_post_mime_type( $feat_id ),
        								"images" => $feat_image    
        								)
            );
    }
    header("Content-type: application/json; charset=utf-8");
    echo json_encode($json_data);
    exit;
    endif;
?>