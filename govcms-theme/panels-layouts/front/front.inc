<?php

/**
 * @file
 * One column stacked panel layout.
 */

/**
 * Implements hook_panels_layouts().
 */
// Plugin definition.
$plugin = array(
  'title' => t('govCMS Front Page'),
  'category' => t('Columns: 1'),
  'icon' => 'front.png',
  'theme' => 'govcms-layouts-front',
  'admin css' => 'front.admin.css',
  'regions' => array(
    'intro' => t('Introduction'),
    'clients' => t('Clients / Trusted by'),
    'whos_using' => t('Usage stats'),
    'testimonial_split' => t('Testimonial Split'),
    'why_use' => t('Why use govCMS?'),
    'more_information_split' => t('More Information / Calculate Costs'),
    'distro_split' => t('Split Distribution'),
  ),
);
