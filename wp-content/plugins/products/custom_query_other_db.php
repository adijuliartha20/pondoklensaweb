<?php 
global $wpdb;

// Define the database credentials for the other database
$other_db_name = 'other_database_name';
$other_db_user = 'other_database_user';
$other_db_password = 'other_database_password';
$other_db_host = 'other_database_host';

// Connect to the other database
$other_db = new wpdb($other_db_user, $other_db_password, $other_db_name, $other_db_host);

// Define the tables to access
$posts_table = $other_db->prefix . 'posts';
$postmeta_table = $other_db->prefix . 'postmeta';

// Query the data
$posts = $other_db->get_results("SELECT * FROM $posts_table");
$postmeta = $other_db->get_results("SELECT * FROM $postmeta_table");

?>