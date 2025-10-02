<?php
/**
 * Limpieza al desinstalar el plugin
 * 
 * @package WlandChat
 */

// Si uninstall no es llamado desde WordPress, salir
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

/**
 * Eliminar todas las opciones del plugin
 */
function wland_chat_delete_plugin_options() {
    $options = array(
        'wland_chat_webhook_url',
        'wland_chat_header_title',
        'wland_chat_header_subtitle',
        'wland_chat_welcome_message',
        'wland_chat_position',
        'wland_chat_excluded_pages',
        'wland_chat_availability_enabled',
        'wland_chat_availability_start',
        'wland_chat_availability_end',
        'wland_chat_availability_timezone',
        'wland_chat_availability_message',
        'wland_chat_display_mode',
        'wland_chat_version',
    );
    
    foreach ($options as $option) {
        delete_option($option);
    }
    
    // Para sitios multisitio
    if (is_multisite()) {
        global $wpdb;
        $blog_ids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
        $original_blog_id = get_current_blog_id();
        
        foreach ($blog_ids as $blog_id) {
            switch_to_blog($blog_id);
            foreach ($options as $option) {
                delete_option($option);
            }
        }
        
        switch_to_blog($original_blog_id);
    }
}

/**
 * Eliminar archivos y directorios creados
 */
function wland_chat_delete_plugin_files() {
    $upload_dir = wp_upload_dir();
    $wland_dir = $upload_dir['basedir'] . '/wland-chat';
    
    if (file_exists($wland_dir)) {
        wland_chat_recursive_delete($wland_dir);
    }
}

/**
 * Eliminar directorio recursivamente
 */
function wland_chat_recursive_delete($dir) {
    if (!file_exists($dir)) {
        return;
    }
    
    $files = array_diff(scandir($dir), array('.', '..'));
    
    foreach ($files as $file) {
        $path = $dir . '/' . $file;
        is_dir($path) ? wland_chat_recursive_delete($path) : unlink($path);
    }
    
    return rmdir($dir);
}

/**
 * Limpiar metadatos de posts relacionados
 */
function wland_chat_delete_post_meta() {
    global $wpdb;
    
    $wpdb->query(
        "DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE 'wland_chat_%'"
    );
}

/**
 * Limpiar opciones de usuario
 */
function wland_chat_delete_user_meta() {
    global $wpdb;
    
    $wpdb->query(
        "DELETE FROM {$wpdb->usermeta} WHERE meta_key LIKE 'wland_chat_%'"
    );
}

// Ejecutar limpieza
wland_chat_delete_plugin_options();
wland_chat_delete_plugin_files();
wland_chat_delete_post_meta();
wland_chat_delete_user_meta();

// Limpiar caché
wp_cache_flush();