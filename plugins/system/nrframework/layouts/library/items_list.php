<?php

/**
 * @package         Convert Forms
 * @version         3.2.8 Free
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2022 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('_JEXEC') or die('Restricted access');

extract($displayData);

if (!$templates)
{
    return;
}

use Joomla\CMS\Language\Text;

if (!$main_category_label)
{
	$main_category_label = Text::_('NR_CATEGORY');
}

$j_version = JVERSION;

$capabilities_solution_label = Text::_('NR_SOLUTION');
$capabilities_content_label = Text::_('NR_GOAL');
$capabilities_joomla_label = Text::_('NR_UPDATE_JOOMLA');
$capabilities_joomla_url = \JURI::base() . 'index.php?option=com_joomlaupdate';
$capabilities_project_label = sprintf(Text::_('NR_UPDATE_EXTENSION_X'), $project_name);
$capabilities_project_url = $update_url = \JURI::base() . 'index.php?option=com_installer&view=update';
$install_url = \JURI::base() . 'index.php?option=com_installer&view=install';

$install_svg_icon = '<svg class="icon" width="16" height="14" viewBox="0 0 16 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M2 10L2 13L14 13L14 10" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/><path d="M8.5 1C8.5 0.723858 8.27614 0.5 8 0.5C7.72386 0.5 7.5 0.723858 7.5 1L8.5 1ZM7.64645 10.3536C7.84171 10.5488 8.15829 10.5488 8.35355 10.3536L11.5355 7.17157C11.7308 6.97631 11.7308 6.65973 11.5355 6.46447C11.3403 6.2692 11.0237 6.2692 10.8284 6.46447L8 9.29289L5.17157 6.46447C4.97631 6.2692 4.65973 6.2692 4.46447 6.46447C4.2692 6.65973 4.2692 6.97631 4.46447 7.17157L7.64645 10.3536ZM7.5 1L7.5 10L8.5 10L8.5 1L7.5 1Z" fill="currentColor"/></svg>';
$activate_svg_icon = '<svg class="icon" width="16" height="14" viewBox="0 0 16 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M2 10L2 13L14 13L14 10" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/><path d="M8.5 1C8.5 0.723858 8.27614 0.5 8 0.5C7.72386 0.5 7.5 0.723858 7.5 1L8.5 1ZM7.64645 10.3536C7.84171 10.5488 8.15829 10.5488 8.35355 10.3536L11.5355 7.17157C11.7308 6.97631 11.7308 6.65973 11.5355 6.46447C11.3403 6.2692 11.0237 6.2692 10.8284 6.46447L8 9.29289L5.17157 6.46447C4.97631 6.2692 4.65973 6.2692 4.46447 6.46447C4.2692 6.65973 4.2692 6.97631 4.46447 7.17157L7.64645 10.3536ZM7.5 1L7.5 10L8.5 10L8.5 1L7.5 1Z" fill="currentColor"/></svg>';
$update_svg_icon = '<svg class="icon" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9.50006 4.6001L7.70013 2.80017L9.50006 1.00024" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/><path d="M8.60022 2.80029C11.5824 2.80029 14 5.21786 14 8.20008C14 9.79931 13.3048 11.2362 12.2001 12.2249" stroke="currentColor" stroke-linecap="round"/><path d="M6.5 11.7993L8.29993 13.5992L6.5 15.3992" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/><path d="M7.39979 13.5989C4.41757 13.5989 2 11.1814 2 8.19915C2 6.59991 2.69522 5.16305 3.79993 4.17432" stroke="currentColor" stroke-linecap="round"/></svg>';
$pro_svg_icon = '<svg class="icon" width="16" height="14" viewBox="0 0 16 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M7.5 10C7.5 10.2761 7.72386 10.5 8 10.5C8.27614 10.5 8.5 10.2761 8.5 10L7.5 10ZM8.35355 3.64645C8.15829 3.45118 7.84171 3.45118 7.64645 3.64645L4.46447 6.82843C4.2692 7.02369 4.2692 7.34027 4.46447 7.53553C4.65973 7.7308 4.97631 7.7308 5.17157 7.53553L8 4.70711L10.8284 7.53553C11.0237 7.7308 11.3403 7.7308 11.5355 7.53553C11.7308 7.34027 11.7308 7.02369 11.5355 6.82843L8.35355 3.64645ZM8.5 10L8.5 4L7.5 4L7.5 10L8.5 10Z" fill="currentColor"/><path d="M14 7C14 10.3137 11.3137 13 8 13C4.68629 13 2 10.3137 2 7C2 3.68629 4.68629 1 8 1C11.3137 1 14 3.68629 14 7Z" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/></svg>';
$key_svg_icon = '<svg class="icon" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="4.81803" cy="11.3135" r="3" transform="rotate(-45 4.81803 11.3135)" stroke="currentColor"/><line x1="6.93933" y1="9.19231" x2="13.3033" y2="2.82835" stroke="currentColor" stroke-linecap="round"/><path d="M12.5962 4.24219L14.0104 5.6564" stroke="currentColor" stroke-linecap="round"/><path d="M10.4749 6.36377L11.182 7.07088" stroke="currentColor" stroke-linecap="round"/></svg>';
$insert_svg_icon = '<svg class="icon" width="16" height="14" viewBox="0 0 16 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M3 4L3 1L14 1L14 13L3 13L3 10" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/><path d="M2 6.5C1.72386 6.5 1.5 6.72386 1.5 7C1.5 7.27614 1.72386 7.5 2 7.5V6.5ZM11.3536 7.35355C11.5488 7.15829 11.5488 6.84171 11.3536 6.64645L8.17157 3.46447C7.97631 3.2692 7.65973 3.2692 7.46447 3.46447C7.2692 3.65973 7.2692 3.97631 7.46447 4.17157L10.2929 7L7.46447 9.82843C7.2692 10.0237 7.2692 10.3403 7.46447 10.5355C7.65973 10.7308 7.97631 10.7308 8.17157 10.5355L11.3536 7.35355ZM2 7.5L11 7.5V6.5L2 6.5V7.5Z" fill="currentColor"/></svg>';
$error_svg_icon = '<svg class="icon" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M14.3351 6.19586L14.8335 6.1558C14.8198 5.98487 14.7194 5.8329 14.5676 5.75318C14.4157 5.67346 14.2336 5.67711 14.0851 5.76285L14.3351 6.19586ZM9.26069 9.12581L9.54456 8.7142C9.38565 8.60461 9.17786 8.59628 9.01069 8.6928L9.26069 9.12581ZM7.8715 6.71947L8.1215 7.15248C8.28866 7.05597 8.38534 6.87187 8.3699 6.67947L7.8715 6.71947ZM12.9456 3.78971L13.1956 4.22272C13.3441 4.13697 13.4383 3.98108 13.4452 3.80971C13.4521 3.63834 13.3706 3.47541 13.2294 3.37807L12.9456 3.78971ZM10.8097 5.02286L10.5597 4.58984C10.415 4.67343 10.3215 4.82385 10.3108 4.99068L10.8097 5.02286ZM10.7023 6.68909L10.2033 6.65691C10.1903 6.85835 10.2997 7.04783 10.4807 7.1373L10.7023 6.68909ZM12.199 7.42915L11.9774 7.87735C12.1273 7.95145 12.3042 7.94575 12.449 7.86216L12.199 7.42915ZM12.9741 9.69824C14.2675 8.9515 14.9456 7.54965 14.8335 6.1558L13.8367 6.23593C13.919 7.25948 13.4207 8.2857 12.4741 8.83221L12.9741 9.69824ZM8.97683 9.53742C10.1279 10.3313 11.6808 10.4449 12.9741 9.69824L12.4741 8.83221C11.5276 9.37869 10.3898 9.29714 9.54456 8.7142L8.97683 9.53742ZM4.02698 12.7248L9.51069 9.55882L9.01069 8.6928L3.52698 11.8588L4.02698 12.7248ZM1.44618 12.0333C1.96789 12.937 3.12335 13.2466 4.02698 12.7248L3.52698 11.8588C3.10164 12.1044 2.55777 11.9587 2.3122 11.5333L1.44618 12.0333ZM2.1377 9.45253C1.23407 9.97424 0.924469 11.1297 1.44618 12.0333L2.3122 11.5333C2.06664 11.108 2.21237 10.5641 2.6377 10.3186L2.1377 9.45253ZM7.6215 6.28646L2.1377 9.45253L2.6377 10.3186L8.1215 7.15248L7.6215 6.28646ZM9.23251 3.21753C7.93928 3.96418 7.26126 5.36578 7.3731 6.75946L8.3699 6.67947C8.28777 5.65605 8.78604 4.63 9.73251 4.08356L9.23251 3.21753ZM13.2294 3.37807C12.0784 2.58449 10.5257 2.47093 9.23251 3.21753L9.73251 4.08356C10.6789 3.53714 11.8166 3.61861 12.6618 4.20136L13.2294 3.37807ZM11.0597 5.45587L13.1956 4.22272L12.6956 3.3567L10.5597 4.58984L11.0597 5.45587ZM11.2012 6.72127L11.3087 5.05504L10.3108 4.99068L10.2033 6.65691L11.2012 6.72127ZM12.4206 6.98094L10.9239 6.24089L10.4807 7.1373L11.9774 7.87735L12.4206 6.98094ZM14.0851 5.76285L11.949 6.99614L12.449 7.86216L14.5851 6.62888L14.0851 5.76285Z" fill="currentColor"/></svg>';
$loading_svg_icon = '<svg class="icon loading" width="16" height="16" xmlns="http://www.w3.org/2000/svg" style="shape-rendering: auto;" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid"><circle cx="50" cy="50" fill="none" stroke="currentColor" stroke-width="10" r="40" stroke-dasharray="160.22122533307947 55.40707511102649"><animateTransform attributeName="transform" type="rotate" repeatCount="indefinite" dur="1s" values="0 50 50;360 50 50" keyTimes="0;1"></animateTransform></circle></svg>';

$info_item_atts = defined('nrJ4') ? '' : ' data-bs-toggle="modal" data-toggle="modal" data-bs-target="#tf-library-item-info-popup" href="#tf-library-item-info-popup"';
$info_item_class = defined('nrJ4') ? ' tf-library-template-item-info-popup-trigger' : '';

foreach ($templates as $template_key => $template)
{
    $is_favorite = array_key_exists($template->id, $favorites);
    $image = isset($template->image) ? $template->image : '';

    $external_extensions = $template->fields->{'templates-external-extensions'};

    $item_class = '';

    $valid_j_version = $valid_item_version = $valid_third_party_extension_version = false;

    $required_j_version = isset($template->fields->{'templates-minimum-joomla-version'}) && !empty($template->fields->{'templates-minimum-joomla-version'}) ? $template->fields->{'templates-minimum-joomla-version'} : '';
    $required_item_version = isset($template->fields->{'templates-minimum-extension-version'}) && !empty($template->fields->{'templates-minimum-extension-version'}) ? $template->fields->{'templates-minimum-extension-version'} : '';

    $errors = [];

    $capabilities = [
        'pro' => [
            'requirement' => $template->is_pro ? 'pro' : 'lite',
            'detected' => $project_license_type === 'lite' ? 'lite' : 'pro'
        ],
        'category' => [
            'value' => $template->category,
            'label' => $main_category_label
        ],
        'solution' => [
            'value' => $template->filters->solution,
            'label' => $capabilities_solution_label
        ],
        'goal' => [
            'value' => $template->filters->goal,
            'label' => $capabilities_content_label
        ],
        'joomla' => [
            'value' => $required_j_version,
            'label' => $capabilities_joomla_label,
            'icon' => '',
            'url' => $capabilities_joomla_url,
            'detected' => $j_version
        ],
        'project' => [
            'value' => $required_item_version,
            'label' => $capabilities_project_label,
            'icon' => '',
            'url' => $capabilities_project_url,
            'detected' => $project_version
        ],
        'third_party_dependencies' => [
            'value' => $external_extensions,
            'errors' => []
        ],
        'license_error' => [
            'value' => ''
        ]
    ];

    /**
     * A template may not be available for the following reasons:
     * - User has an older version than the one specified in the template
     * - Extension version may be outdated
     * - 3rd-party extensions are required and missing (not installed and/or not activated)
     */

    // Joomla Version Check
    if ($required_j_version)
    {
        $valid_j_version = !empty(trim($required_j_version)) ? version_compare($j_version, $required_j_version, '>=') : false;
        if (!$valid_j_version)
        {
            $capabilities['joomla']['icon'] = 'update';
            $errors['joomla'] = $capabilities['joomla'];
            $errors['joomla']['full_label'] = Text::_('NR_UPDATE_JOOMLA_TO_INSERT_TEMPLATE');
        }
    }

    // Item Version Check
    if ($required_item_version)
    {
        $valid_item_version = !empty(trim($required_item_version)) ? version_compare(strstr($project_version, '-', true), $required_item_version, '>=') : false;
        if (!$valid_item_version)
        {
            $capabilities['project']['icon'] = 'update';
            $errors['project'] = $capabilities['project'];
            $errors['project']['full_label'] = sprintf(Text::_('NR_UPDATE_EXTENSION_X_TO_INSERT_TEMPLATE'), $project_name);
        }
    }

    // 3rd party extensions Check
    if (is_array($external_extensions) && count($external_extensions))
    {
        foreach ($external_extensions as $key => $external_extension)
        {
            $valid_third_party_extension_version = false;

            $icon = $label = $full_label = $detected = 'none';

            if (!$external_extension->slug || !$external_extension->name || !$external_extension->version)
            {
                continue;
            }

            // 3rd-party extension is not installed
            if (!\NRFramework\Extension::isInstalled($external_extension->slug))
            {
                $icon = 'install';
                $label = sprintf(Text::_('NR_INSTALL_EXTENSION_X'), $external_extension->name);
                $full_label = sprintf(Text::_('NR_INSTALL_EXTENSION_X_TO_INSERT_TEMPLATE'), $external_extension->name);
            }
            // 3rd-party extension is installed but not active
            else if (!\NRFramework\Extension::isEnabled($external_extension->slug))
            {
                $icon = 'activate';
                $label = sprintf(Text::_('NR_ACTIVATE_EXTENSION_X'), $external_extension->name);
                $full_label = sprintf(Text::_('NR_ACTIVATE_EXTENSION_X_TO_INSERT_TEMPLATE'), $external_extension->name);
            }
            // 3rd-party extension is installed, active but we need to check whether its version is valid
            else
            {
                $third_party_extension_installed_version = \NRFramework\Extension::getVersion($external_extension->slug);
                $valid_third_party_extension_version = !empty(trim($external_extension->version)) ? version_compare(strstr($third_party_extension_installed_version, '-', true), $external_extension->version, '>=') : false;
                
                $icon = 'update';
                $label = sprintf(Text::_('NR_UPDATE_EXTENSION_X'), $external_extension->name);
                $full_label = sprintf(Text::_('NR_UPDATE_EXTENSION_X_TO_INSERT_TEMPLATE'), $external_extension->name);
                $detected = $third_party_extension_installed_version;
            }

            // Set third party item information
            $capabilities['third_party_dependencies']['value'][$key]->icon = $icon;
            $capabilities['third_party_dependencies']['value'][$key]->label = $label;
            $capabilities['third_party_dependencies']['value'][$key]->full_label = $full_label;
            $capabilities['third_party_dependencies']['value'][$key]->detected = $detected;
            $capabilities['third_party_dependencies']['value'][$key]->url = $icon === 'update' ? $update_url : $install_url;
            $capabilities['third_party_dependencies']['value'][$key]->valid = $valid_third_party_extension_version;
            
            if (!$valid_third_party_extension_version)
            {
                // Add the external extensions we are having issues with
                $capabilities['third_party_dependencies']['errors'][] = $external_extension->name;
                
                // Set error index used to retrieve this error message action
                $capabilities['third_party_dependencies']['error_index'] = $key;

                // Add the error
                $errors['third_party_dependencies_' . $key] = $capabilities['third_party_dependencies'];
            }
        }
    }

    /**
     * Check other cases where a template may not be available:
     * - Is Pro but we have the Free version
     * - Is Pro and template is Pro
     *   - We have not entered a license key OR We have entered a license key but it's not valid
     */

    // Is Pro but we have the Free version
    if ($project_license_type === 'lite' && $template->is_pro)
    {
        $errors = [
            'pro' => [
                'icon' => 'pro',
                'class' => 'red',
                'data_attributes' => 'data-pro-only="' . $template->title . '"',
                'label' => Text::_('NR_UPGRADE_TO_UC_PRO'),
                'full_label' => Text::_('NR_UPGRADE_TO_PRO_TO_UNLOCK_TEMPLATE')
            ]
        ] + $errors;
    }
    // Is Pro and template is Pro
    else if ($project_license_type === 'pro' && $template->is_pro && (empty($license_key) || $license_key_status !== 'valid'))
    {
        // We have not entered a license key
        if (empty($license_key))
        {
            $errors['license'] = [
                'icon' => 'key',
                'url' => $product_license_settings_url,
                'label' => Text::_('NR_SET_LICENSE_KEY'),
                'full_label' => Text::_('NR_NO_LICENSE_KEY_DETECTED')
            ];
            
            $capabilities['license_error']['value'] = 'missing';
        }
        // We have entered a license key but it's invalid/expired
        else if ($license_key_status !== 'valid')
        {
            $errors['license'] = [
                'icon' => 'key',
                'url' => 'https://tassos.gr/subscriptions',
                'label' => Text::_('NR_INVALID_EXPIRED_LICENSE_KEY'),
                'full_label' => Text::_('NR_INVALID_LICENSE_KEY_ENTERED')
            ];

            $capabilities['license_error']['value'] = 'invalid_expired';
        }
    }

    if ($errors)
    {
        $item_class .= ' has-errors';

        // If its a PRO template and we are not a PRO user, add a "is-pro" CSS class
        if ($template->is_pro && $project_license_type === 'lite')
        {
            $item_class .= ' is-pro';
        }
    }
    ?>
    <div
        class="tf-library-item<?php echo $item_class; ?>"
        data-id="<?php echo $template->id; ?>"
        data-note="<?php echo $template->fields->{'templates-note'}; ?>"

        <?php
        foreach ($template->sort as $sort_key => $sort_value)
        {
            ?>data-sort-<?php echo $sort_key; ?>="<?php echo $sort_value; ?>"<?php
        }
        ?>

        data-filter-category="<?php echo $template->category; ?>"
        <?php foreach ($template->filters as $filter_key => $filter_value): ?>
            data-filter-<?php echo $filter_key; ?>="<?php echo $filter_value; ?>"
        <?php endforeach; ?>

        <?php if ($project_license_type === 'lite'): ?>
        data-filter-compatibility="<?php echo $template->is_pro ? 'Pro' : 'Free'; ?>"
        <?php endif; ?>

        data-title="<?php echo $template->title; ?>"
        data-capabilities="<?php echo htmlspecialchars(json_encode($capabilities), ENT_QUOTES, 'UTF-8'); ?>">
        <div class="tf-library-item-wrap">
            <div class="tf-template-item-message is-hidden">
                <span class="tf-template-item-message-text">This is a message</span>
                <svg class="fpf-library-messages-hide-btn" height="14" viewBox="0 0 14 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect x="14" y="12.5933" width="2.47487" height="17.3241" transform="rotate(135 14 12.5933)" fill="currentColor"></rect>
                    <rect width="2.47487" height="17.3241" transform="matrix(-0.707109 -0.707105 0.707109 -0.707105 1.75 14.3433)" fill="currentColor"></rect>
                </svg>
            </div>
            <div class="tf-library-item-image-wrapper">
                <div class="tf-library-item-image-inner">
                    <img loading="lazy" src="<?php echo $image; ?>" alt="<?php echo $template->title; ?>" />

                    <div class="tf-library-item-hover">
                        <a href="#templates-library-previewer" class="tf-button outline tf-library-preview-item" title="<?php echo Text::_('NR_PREVIEW_TEMPLATE'); ?>">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="11.5" cy="11.5" r="6" stroke="currentColor"/>
                                <line x1="15.7071" y1="16" x2="19" y2="19.2929" stroke="currentColor" stroke-linecap="round"/>
                                <line x1="11.5" y1="9" x2="11.5" y2="14" stroke="currentColor"/>
                                <line x1="14" y1="11.5" x2="9" y2="11.5" stroke="currentColor"/>
                            </svg>
                            <span><?php echo Text::_('NR_PREVIEW'); ?></span>
                        </a>
                        <?php if (isset($errors['joomla']) || isset($errors['project']) || isset($errors['third_party_dependencies_0'])): ?>
                        <div class="dependencies-wrapper">
                            <div class="title"><?php echo Text::_('NR_REQUIREMENTS'); ?></div>
                            <div class="dependencies">
                                <?php
                                if (array_key_exists('joomla', $errors))
                                {
                                    ?><span class="error<?php echo $info_item_class; ?>"<?php echo $info_item_atts; ?>><?php echo Text::_('NR_JOOMLA') . ' ' . ($required_j_version ? $required_j_version : $j_version); ?></span><?php
                                }
                                if (array_key_exists('project', $errors))
                                {
                                    ?><span class="error<?php echo $info_item_class; ?>"<?php echo $info_item_atts; ?>><?php echo $project_name . ' ' . ($required_item_version ? $required_item_version : $project_version); ?></span><?php
                                }
                                if (is_array($external_extensions) && count($external_extensions))
                                {
                                    foreach ($external_extensions as $external_extension_key => $external_extension)
                                    {
                                        if (!array_key_exists('third_party_dependencies_' . $external_extension_key, $errors) || !array_key_exists('errors', $errors['third_party_dependencies_' . $external_extension_key]) || !in_array($external_extension->name, $errors['third_party_dependencies_' . $external_extension_key]['errors']))
                                        {
                                            continue;
                                        }
                                        
                                        ?><span class="error<?php echo $info_item_class; ?>"<?php echo $info_item_atts; ?>><?php echo $external_extension->name . ' ' . $external_extension->version; ?></span><?php
                                    }
                                }
                                ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <?php
                if ($template->is_pro && $project_license_type === 'lite')
                {
                    ?>
                    <span class="ribbon"><?php echo Text::_('NR_PRO'); ?></span>
                    <?php
                }
                ?>
            </div>
            <div class="tf-library-item-bottom">
                <div class="template-label"><?php echo $template->title; ?></div>
                <div class="tf-library-item-bottom-buttons">
                    <a data-template-id="<?php echo $template->id; ?>" class="info tf-library-template-item-info<?php echo $info_item_class; ?>" title="<?php echo Text::_('NR_TEMPLATE_INFORMATION'); ?>"<?php echo $info_item_atts; ?> href="#tf-library-item-info-popup">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="8" cy="8" r="7" stroke="currentColor"/>
                            <rect x="7" y="7" width="2" height="5" fill="currentColor"/>
                            <rect x="7" y="4" width="2" height="2" fill="currentColor"/>
                        </svg>
                    </a>
                    <a href="#" class="tf-library-favorite-icon tf-library-favorite-item<?php echo $is_favorite ? ' active' : ''; ?>" title="<?php echo Text::_('NR_LIBRARY_SAVE_TEMPLATE_FAVORITES'); ?>">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M14.902 6.62124C14.3943 9.04222 11.0187 11.1197 7.99845 14C4.97819 11.1197 1.60265 9.04223 1.09492 6.62125C0.231957 2.50649 5.47086 -0.0322558 7.99845 4.12617C10.7204 -0.0322523 15.7649 2.50648 14.902 6.62124Z" stroke="currentColor" stroke-linejoin="round"/>
                        </svg>
                    </a>
                </div>
                <div class="tf-library-item-actions">
                    <?php
                    // We have errors with this template
                    if (!empty($errors))
                    {
                        // Multiple errors, but do not show this message when we still need to Upgrade to Pro.
                        if (count($errors) > 1 && !isset($errors['pro']))
                        {
                            ?>
                            <a class="<?php echo $info_item_class; ?>"<?php echo $info_item_atts; ?> href="#tf-library-item-info-popup" data-template-id="<?php echo $template->id; ?>">
                                <?php
                                echo $error_svg_icon;
                                echo Text::_('NR_MULTIPLE_ISSUES_DETECTED');
                                ?>
                            </a>
                            <?php
                        }
                        // One error
                        else
                        {
                            $error_keys = array_keys($errors);
                            $error_values = array_values($errors);
                            $error_items = [$error_values[0]];

                            foreach ($error_items as $error_item)
                            {
                                // 3rd-party dependency has an array of extensions
                                if (isset($error_item['value']) && is_array($error_item['value']) && isset($error_item['error_index']))
                                {
                                    $error_item = (array) $error_item['value'][$error_item['error_index']];
                                }
                                
                                $class = isset($error_item['class']) ? $error_item['class'] : '';
                                $data_atts = isset($error_item['data_attributes']) ? ' ' . $error_item['data_attributes'] : '';
                                $url = isset($error_item['url']) ? $error_item['url'] : '#';
                                if ($url !== '#')
                                {
                                    $data_atts .= ' target="_blank"';
                                }
                                ?>
                                <a href="<?php echo $url; ?>" class="<?php echo $class; ?>"<?php echo $data_atts; ?>>
                                    <?php echo ${$error_item['icon'] . '_svg_icon'}; ?>
                                    <?php if (isset($error_item['full_label'])): ?>
                                        <span class="full-label"><?php echo $error_item['full_label']; ?></span>
                                    <?php endif; ?>
                                    <span class="short-label"><?php echo $error_item['label']; ?></span>
                                </a>
                                <?php
                            }
                        }
                    }
                    // No errors, we can use the template
                    else
                    {
                        ?>
                        <a href="#" class="tf-library-item-insert-btn" data-template-id="<?php echo $template->id; ?>">
                            <?php
                            echo $insert_svg_icon;
                            echo $loading_svg_icon;
                            ?>
                            <span class="full-label"><?php echo Text::_('NR_INSERT_TEMPLATE'); ?></span>
                            <span class="short-label"><?php echo Text::_('NR_INSERT'); ?></span>
                        </a>
                        <?php
                    }
                    ?>
                </div>
            </div>
            <div class="info-popup-actions">
                <?php
                // Show errors
                if ($errors)
                {
                    foreach ($errors as $error_key => $error)
                    {
                        // 3rd-party dependency has an array of extensions
                        if (isset($error['value']) && is_array($error['value']) && isset($error['error_index']))
                        {
                            $error = (array) $error['value'][$error['error_index']];
                        }
                        
                        $url = isset($error['url']) ? $error['url'] : '#';
                        $class = isset($error['class']) ? $error['class'] : '';
                        $data_atts = isset($error['data_attributes']) ? ' ' . $error['data_attributes'] : '';
                        if ($error['icon'] !== 'pro')
                        {
                            $class .= ' orange';
                        }

                        // Add error key (which capability this error corresponds to, i.e. joomla, pro, etc...)
                        $class .= ' ' . $error_key;
                        ?>
                        <a href="<?php echo $url; ?>" target="_blank" class="tf-button outline <?php echo $class; ?>"<?php echo $data_atts; ?>>
                            <?php echo ${$error['icon'] . '_svg_icon'}; ?>
                            <span class="short-label"><?php echo $error['label']; ?></span>
                        </a>
                        <?php
                    }
                }
                // Show insert button
                else
                {
                    ?>
                    <a href="#" class="tf-button outline blue tf-library-item-insert-btn" data-template-id="<?php echo $template->id; ?>">
                        <?php
                        echo $insert_svg_icon;
                        echo $loading_svg_icon;
                        echo Text::_('NR_INSERT_TEMPLATE_NOW');
                        ?>
                    </a>
                    <?php
                }
                ?>
            </div>
        </div>
    </div>
    <?php
}