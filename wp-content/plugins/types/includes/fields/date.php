<?php
/**
 * Types-field: Date
 *
 * Description: Displays a datepicker to the user.
 *
 * Rendering: Date is stored in seconds (time()) but displayed as date
 * formatted.
 * 
 * Parameters:
 * 'raw' => 'true'|'false' (display raw data stored in DB, default false)
 * 'output' => 'html' (wrap data in HTML, optional)
 * 'show_name' => 'true' (show field name before value e.g. My date: $value)
 * 'style' => 'text'|'calendar' (display text or WP calendar)
 * 'format' => defaults to WP date format settings, can be any valid date format
 *     e.g. "j/n/Y"
 *
 * Example usage:
 * With a short code use [types field="my-date"]
 * In a theme use types_render_field("my-date", $parameters)
 * 
 */