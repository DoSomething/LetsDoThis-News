<?php
/**
 * @package LetsDoThis
 * @version 1.0
 */

/*
Plugin Name: Let's Do This
Description: Custom plugin for the Let's Do This app.
Author: DoSomething.org
Version: 1.0
*/

// Adds Category Pod fields to the response provided by WP JSON API plugin
// @see https://github.com/dphiffer/wp-json-api#filter-json_api_encode

add_filter('json_api_encode', 'ldt_encode_response');

function ldt_encode_response($response) {
  if (isset($response['posts'])) {
    foreach ($response['posts'] as $post) {
      ldt_encode_post($post);
    }
  } else if (isset($response['post'])) {
    ldt_encode_post($response['post']);
  }

  if (isset($response['categories'])) {
    foreach ($response['categories'] as $category) {
      ldt_encode_category($category, 'full');
    }
  } else if (isset($response['category'])) {
    ldt_encode_category($response['category'], 'full');
  }

  return $response;
}

function ldt_encode_post(&$post) {
  // Remove extra data.
  unset($post->author);
  unset($post->comment_count);
  unset($post->comment_status);
  unset($post->comments);
  unset($post->content);
  unset($post->excerpt);
  unset($post->modified);
  unset($post->slug);
  unset($post->status);
  unset($post->tags);
  unset($post->title_plain);
  unset($post->type);

  $post->image_url = "";
  if (!empty($post->attachments)) {
    $post->image_url = $post->attachments[0]->url;
  }

  $custom_fields = ['campaign_id', 'full_article_url', 'photo_credit', 'summary_1', 'summary_2', 'summary_3'];
  foreach ($custom_fields as $field_name) {
    ldt_add_custom_field_as_property($post, $field_name);
  }
  
  foreach ($post->categories as $category) {
    ldt_encode_category($category);
  }
}

function ldt_add_custom_field_as_property(&$post, $field_name) {
  $post->{$field_name} = "";
  if (!empty($post->custom_fields->{$field_name})) {
    $post->{$field_name} = $post->custom_fields->{$field_name}[0];
  };
}

function ldt_encode_category(&$category, $view_mode = 'teaser') {
  // Remove extra data.
  unset($category->parent);
  unset($category->slug);

  $pod = pods('category');
  $pod->fetch($category->id);
  $category->hex = $pod->get_field('hex');
  if ($view_mode === 'teaser') {
  	return;
  }

  $category->image_url = NULL;
  $image = $pod->get_field('image');
  if ($image) {
    $category->image_url = $image[0]['guid'];
  }
}

?>
