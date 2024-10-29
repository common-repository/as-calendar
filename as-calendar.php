<?php

/**
 * Plugin Name: AS Calendar Buttons
 * Description: A calender button Plugin for Wordpres.
 * Version: 1.0.0
 * Author: Akshar Soft Solutions
 * Author URI: http://aksharsoftsolutions.com/
 * Text Domain: as_calendar
 * Domain Path: /languages
 */

function as_calendar_script()
{
    wp_enqueue_style('as-calendar-css', plugin_dir_url('__DIR__ ') . 'as-calendar/assets/css/atcb.min.css', array(), rand());
    wp_enqueue_script('as-calendae-js', plugin_dir_url('__DIR__ ') . 'as-calendar/assets/js/atcb.min.js', array(), rand());
}
add_action('wp_enqueue_scripts', 'as_calendar_script');

function as_calendar_load_textdomain()
{
    load_plugin_textdomain('as_calendar', false, dirname(plugin_basename(__FILE__)) . '/languages');
}
add_action('init', 'as_calendar_load_textdomain');

function as_calendar_admin_script()
{
    wp_enqueue_style('as-calendar-custom-css', plugin_dir_url('__DIR__ ') . 'as-calendar/assets/css/as_calendar_style.css', array(), rand());
}
add_action('admin_enqueue_scripts', 'as_calendar_admin_script');

add_action('init', 'as_calendae_post_type');
function as_calendae_post_type()
{

    $labels = array(

        'name'                     => __('Calendar Buttons', 'as_calendar'),
        'singular_name'            => __('Button', 'as_calendar'),
        'add_new'                  => __('Add New', 'as_calendar'),
        'add_new_item'             => __('Add New', 'as_calendar'),
        'edit_item'                => __('Edit Button', 'as_calendar'),
        'new_item'                 => __('New Button', 'as_calendar'),
        'view_item'                => __('View Calendar Button', 'as_calendar'),
        'view_items'               => __('View Calendar Buttons', 'as_calendar'),
        'search_items'             => __('Search Calendars', 'as_calendar'),

    );
    $args = array(

        'labels'                => $labels,
        'description'           => __('organize and manage Calendars', 'as_calendar'),
        'public'             => true,
        'publicly_queryable' => false,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'capability_type'    => 'post',
        'has_archive'        => true,

    );

    register_post_type('as_calendar', $args);
}

add_filter('manage_as_calendar_posts_columns', 'as_calendar_shortcode_column');
function as_calendar_shortcode_column($column)
{
    $column['shortcode_data'] = __('Short-Code');
    return $column;
}

add_filter('manage_as_calendar_posts_columns', 'as_calendar_shortcode_column_priority');
function as_calendar_shortcode_column_priority($columns)
{
    $columns = array(
        'cb' => $columns['cb'],
        'title' => __('Title'),
        'shortcode_data' => __('Short-Code'),
        'date' => __('Date')
    );
    return $columns;
}
add_action('manage_as_calendar_posts_custom_column', 'as_calendar_shortcode_column_data', 10, 2);
function as_calendar_shortcode_column_data($column, $post_id)
{
    // Image column
    if ($post_id) {
        _e('<pre>', 'as_calendar');
        _e('[as_calendar id=' . $post_id . ']', 'as_calendar');
        _e('</pre>', 'as_calendar');
    }
}

add_shortcode('as_calendar', 'as_calendar_shortcode_func');
function as_calendar_shortcode_func($atts)
{
    if (isset($atts['id'])) {
        $post_id = $atts['id'];
        $name = get_post_meta($post_id, 'as_calendar_name', true);
        $description = get_post_meta($post_id, 'as_calendar_description', true);
        $startDate = get_post_meta($post_id, 'as_calendar_start_date', true);
        $endDate = get_post_meta($post_id, 'as_calendar_end_date', true);
        $startTime = get_post_meta($post_id, 'as_calendar_start_time', true);
        $endTime = get_post_meta($post_id, 'as_calendar_end_time', true);
        $location = get_post_meta($post_id, 'as_calendar_location', true);
        $button_label = get_post_meta($post_id, 'as_calendar_button_label', true);
        $options = get_post_meta($post_id, 'as_calendar_options', true);
        $timeZone = get_post_meta($post_id, 'as_calendar_timezone', true);
        $list_style = get_post_meta($post_id, 'as_calendar_list_style', true);
        $iCalFileName = get_post_meta($post_id, 'as_calendar_button_ical_file_name', true);

        $selected_option = array();

        foreach ($options as $key => $val) {
            if (isset($val['is_checked'])) {
                if (isset($val['label']) && !empty($val['label'])) {
                    $selected_option[] = $key . "|" . $val['label'];
                } else {
                    $selected_option[] = $key;
                }
            }
        }

        //Checkbox Input Box Meta
        ob_start();

?>
        <div class="atcb" style="display:none;">
            {
            "name":"<?php echo esc_html($name); ?>",
            "description":"<?php echo esc_html($description); ?>",
            "startDate":"<?php echo esc_html($startDate); ?>",
            "endDate":"<?php echo esc_html($endDate); ?>",
            "startTime":"<?php echo esc_html($startTime); ?>",
            "endTime":"<?php echo esc_html($endTime); ?>",
            "location":"<?php echo esc_html($location); ?>",
            "label":"<?php echo esc_html($button_label); ?>",
            "options":<?php echo esc_html(json_encode($selected_option)); ?>,
            "timeZone":"<?php echo esc_html($timeZone); ?>",
            "inline":true,
            "listStyle":"<?php echo esc_html($list_style); ?>",
            "iCalFileName":"<?php echo esc_html($iCalFileName); ?>"
            }
        </div>
    <?php
        return ob_get_clean();
    } else {
        return "Shrotcode invalid!!!";
    }
}

function as_calendar_register_meta_boxes()
{
    add_meta_box('meta-box-id', __('AS Calendar', 'textdomain'), 'as_calendar_display_callback', 'as_calendar');
}
add_action('add_meta_boxes', 'as_calendar_register_meta_boxes');


function as_calendar_display_callback($post)
{

    $name = get_post_meta($post->ID, 'as_calendar_name', true);
    $description = get_post_meta($post->ID, 'as_calendar_description', true);
    $startDate = get_post_meta($post->ID, 'as_calendar_start_date', true);
    $endDate = get_post_meta($post->ID, 'as_calendar_end_date', true);
    $startTime = get_post_meta($post->ID, 'as_calendar_start_time', true);
    $endTime = get_post_meta($post->ID, 'as_calendar_end_time', true);
    $location = get_post_meta($post->ID, 'as_calendar_location', true);
    $button_label = get_post_meta($post->ID, 'as_calendar_button_label', true);
    $options = get_post_meta($post->ID, 'as_calendar_options', true);
    $timeZone = get_post_meta($post->ID, 'as_calendar_timezone', true);
    $list_style = get_post_meta($post->ID, 'as_calendar_list_style', true);
    $iCalFileName = get_post_meta($post->ID, 'as_calendar_button_ical_file_name', true);


    $timezone_data = array(
        "Africa/Abidjan" => "Africa/Abidjan GMT+0:00",
        "Africa/Accra" => "Africa/Accra GMT+0:00",
        "Africa/Addis_Ababa" => "Africa/Addis_Ababa GMT+3:00",
        "Africa/Algiers" => "Africa/Algiers GMT+1:00",
        "Africa/Asmara" => "Africa/Asmara GMT+3:00",
        "Africa/Asmera" => "Africa/Asmera GMT+3:00",
        "Africa/Bamako" => "Africa/Bamako GMT+0:00",
        "Africa/Bangui" => "Africa/Bangui GMT+1:00",
        "Africa/Banjul" => "Africa/Banjul GMT+0:00",
        "Africa/Bissau" => "Africa/Bissau GMT+0:00",
        "Africa/Blantyre" => "Africa/Blantyre GMT+2:00",
        "Africa/Brazzaville" => "Africa/Brazzaville GMT+1:00",
        "Africa/Bujumbura" => "Africa/Bujumbura GMT+2:00",
        "Africa/Cairo" => "Africa/Cairo GMT+2:00",
        "Africa/Casablanca" => "Africa/Casablanca GMT+0:00",
        "Africa/Ceuta" => "Africa/Ceuta GMT+1:00",
        "Africa/Conakry" => "Africa/Conakry GMT+0:00",
        "Africa/Dakar" => "Africa/Dakar GMT+0:00",
        "Africa/Dar_es_Salaam" => "Africa/Dar_es_Salaam GMT+3:00",
        "Africa/Djibouti" => "Africa/Djibouti GMT+3:00",
        "Africa/Douala" => "Africa/Douala GMT+1:00",
        "Africa/El_Aaiun" => "Africa/El_Aaiun GMT+0:00",
        "Africa/Freetown" => "Africa/Freetown GMT+0:00",
        "Africa/Gaborone" => "Africa/Gaborone GMT+2:00",
        "Africa/Harare" => "Africa/Harare GMT+2:00",
        "Africa/Johannesburg" => "Africa/Johannesburg GMT+2:00",
        "Africa/Juba" => "Africa/Juba GMT+3:00",
        "Africa/Kampala" => "Africa/Kampala GMT+3:00",
        "Africa/Khartoum" => "Africa/Khartoum GMT+2:00",
        "Africa/Kigali" => "Africa/Kigali GMT+2:00",
        "Africa/Kinshasa" => "Africa/Kinshasa GMT+1:00",
        "Africa/Lagos" => "Africa/Lagos GMT+1:00",
        "Africa/Libreville" => "Africa/Libreville GMT+1:00",
        "Africa/Lome" => "Africa/Lome GMT+0:00",
        "Africa/Luanda" => "Africa/Luanda GMT+1:00",
        "Africa/Lubumbashi" => "Africa/Lubumbashi GMT+2:00",
        "Africa/Lusaka" => "Africa/Lusaka GMT+2:00",
        "Africa/Malabo" => "Africa/Malabo GMT+1:00",
        "Africa/Maputo" => "Africa/Maputo GMT+2:00",
        "Africa/Maseru" => "Africa/Maseru GMT+2:00",
        "Africa/Mbabane" => "Africa/Mbabane GMT+2:00",
        "Africa/Mogadishu" => "Africa/Mogadishu GMT+3:00",
        "Africa/Monrovia" => "Africa/Monrovia GMT+0:00",
        "Africa/Nairobi" => "Africa/Nairobi GMT+3:00",
        "Africa/Ndjamena" => "Africa/Ndjamena GMT+1:00",
        "Africa/Niamey" => "Africa/Niamey GMT+1:00",
        "Africa/Nouakchott" => "Africa/Nouakchott GMT+0:00",
        "Africa/Ouagadougou" => "Africa/Ouagadougou GMT+0:00",
        "Africa/Porto-Novo" => "Africa/Porto-Novo GMT+1:00",
        "Africa/Sao_Tome" => "Africa/Sao_Tome GMT+0:00",
        "Africa/Timbuktu" => "Africa/Timbuktu GMT+0:00",
        "Africa/Tripoli" => "Africa/Tripoli GMT+2:00",
        "Africa/Tunis" => "Africa/Tunis GMT+1:00",
        "Africa/Windhoek" => "Africa/Windhoek GMT+2:00",
        "America/Adak" => "America/Adak GMT-10:00",
        "America/Anchorage" => "America/Anchorage GMT-9:00",
        "America/Anguilla" => "America/Anguilla GMT-4:00",
        "America/Antigua" => "America/Antigua GMT-4:00",
        "America/Araguaina" => "America/Araguaina GMT-3:00",
        "America/Argentina/Buenos_Aires" => "America/Argentina/Buenos_Aires GMT-3:00",
        "America/Argentina/Catamarca" => "America/Argentina/Catamarca GMT-3:00",
        "America/Argentina/ComodRivadavia" => "America/Argentina/ComodRivadavia GMT-3:00",
        "America/Argentina/Cordoba" => "America/Argentina/Cordoba GMT-3:00",
        "America/Argentina/Jujuy" => "America/Argentina/Jujuy GMT-3:00",
        "America/Argentina/La_Rioja" => "America/Argentina/La_Rioja GMT-3:00",
        "America/Argentina/Mendoza" => "America/Argentina/Mendoza GMT-3:00",
        "America/Argentina/Rio_Gallegos" => "America/Argentina/Rio_Gallegos GMT-3:00",
        "America/Argentina/Salta" => "America/Argentina/Salta GMT-3:00",
        "America/Argentina/San_Juan" => "America/Argentina/San_Juan GMT-3:00",
        "America/Argentina/San_Luis" => "America/Argentina/San_Luis GMT-3:00",
        "America/Argentina/Tucuman" => "America/Argentina/Tucuman GMT-3:00",
        "America/Argentina/Ushuaia" => "America/Argentina/Ushuaia GMT-3:00",
        "America/Aruba" => "America/Aruba GMT-4:00",
        "America/Asuncion" => "America/Asuncion GMT-4:00",
        "America/Atikokan" => "America/Atikokan GMT-5:00",
        "America/Atka" => "America/Atka GMT-10:00",
        "America/Bahia" => "America/Bahia GMT-3:00",
        "America/Bahia_Banderas" => "America/Bahia_Banderas GMT-6:00",
        "America/Barbados" => "America/Barbados GMT-4:00",
        "America/Belem" => "America/Belem GMT-3:00",
        "America/Belize" => "America/Belize GMT-6:00",
        "America/Blanc-Sablon" => "America/Blanc-Sablon GMT-4:00",
        "America/Boa_Vista" => "America/Boa_Vista GMT-4:00",
        "America/Bogota" => "America/Bogota GMT-5:00",
        "America/Boise" => "America/Boise GMT-7:00",
        "America/Buenos_Aires" => "America/Buenos_Aires GMT-3:00",
        "America/Cambridge_Bay" => "America/Cambridge_Bay GMT-7:00",
        "America/Campo_Grande" => "America/Campo_Grande GMT-4:00",
        "America/Cancun" => "America/Cancun GMT-5:00",
        "America/Caracas" => "America/Caracas GMT-4:00",
        "America/Catamarca" => "America/Catamarca GMT-3:00",
        "America/Cayenne" => "America/Cayenne GMT-3:00",
        "America/Cayman" => "America/Cayman GMT-5:00",
        "America/Chicago" => "America/Chicago GMT-6:00",
        "America/Chihuahua" => "America/Chihuahua GMT-7:00",
        "America/Coral_Harbour" => "America/Coral_Harbour GMT-5:00",
        "America/Cordoba" => "America/Cordoba GMT-3:00",
        "America/Costa_Rica" => "America/Costa_Rica GMT-6:00",
        "America/Creston" => "America/Creston GMT-7:00",
        "America/Cuiaba" => "America/Cuiaba GMT-4:00",
        "America/Curacao" => "America/Curacao GMT-4:00",
        "America/Danmarkshavn" => "America/Danmarkshavn GMT+0:00",
        "America/Dawson" => "America/Dawson GMT-8:00",
        "America/Dawson_Creek" => "America/Dawson_Creek GMT-7:00",
        "America/Denver" => "America/Denver GMT-7:00",
        "America/Detroit" => "America/Detroit GMT-5:00",
        "America/Dominica" => "America/Dominica GMT-4:00",
        "America/Edmonton" => "America/Edmonton GMT-7:00",
        "America/Eirunepe" => "America/Eirunepe GMT-5:00",
        "America/El_Salvador" => "America/El_Salvador GMT-6:00",
        "America/Ensenada" => "America/Ensenada GMT-8:00",
        "America/Fort_Nelson" => "America/Fort_Nelson GMT-7:00",
        "America/Fort_Wayne" => "America/Fort_Wayne GMT-5:00",
        "America/Fortaleza" => "America/Fortaleza GMT-3:00",
        "America/Glace_Bay" => "America/Glace_Bay GMT-4:00",
        "America/Godthab" => "America/Godthab GMT-3:00",
        "America/Goose_Bay" => "America/Goose_Bay GMT-4:00",
        "America/Grand_Turk" => "America/Grand_Turk GMT-5:00",
        "America/Grenada" => "America/Grenada GMT-4:00",
        "America/Guadeloupe" => "America/Guadeloupe GMT-4:00",
        "America/Guatemala" => "America/Guatemala GMT-6:00",
        "America/Guayaquil" => "America/Guayaquil GMT-5:00",
        "America/Guyana" => "America/Guyana GMT-4:00",
        "America/Halifax" => "America/Halifax GMT-4:00",
        "America/Havana" => "America/Havana GMT-5:00",
        "America/Hermosillo" => "America/Hermosillo GMT-7:00",
        "America/Indiana/Indianapolis" => "America/Indiana/Indianapolis GMT-5:00",
        "America/Indiana/Knox" => "America/Indiana/Knox GMT-6:00",
        "America/Indiana/Marengo" => "America/Indiana/Marengo GMT-5:00",
        "America/Indiana/Petersburg" => "America/Indiana/Petersburg GMT-5:00",
        "America/Indiana/Tell_City" => "America/Indiana/Tell_City GMT-6:00",
        "America/Indiana/Vevay" => "America/Indiana/Vevay GMT-5:00",
        "America/Indiana/Vincennes" => "America/Indiana/Vincennes GMT-5:00",
        "America/Indiana/Winamac" => "America/Indiana/Winamac GMT-5:00",
        "America/Indianapolis" => "America/Indianapolis GMT-5:00",
        "America/Inuvik" => "America/Inuvik GMT-7:00",
        "America/Iqaluit" => "America/Iqaluit GMT-5:00",
        "America/Jamaica" => "America/Jamaica GMT-5:00",
        "America/Jujuy" => "America/Jujuy GMT-3:00",
        "America/Juneau" => "America/Juneau GMT-9:00",
        "America/Kentucky/Louisville" => "America/Kentucky/Louisville GMT-5:00",
        "America/Kentucky/Monticello" => "America/Kentucky/Monticello GMT-5:00",
        "America/Knox_IN" => "America/Knox_IN GMT-6:00",
        "America/Kralendijk" => "America/Kralendijk GMT-4:00",
        "America/La_Paz" => "America/La_Paz GMT-4:00",
        "America/Lima" => "America/Lima GMT-5:00",
        "America/Los_Angeles" => "America/Los_Angeles GMT-8:00",
        "America/Louisville" => "America/Louisville GMT-5:00",
        "America/Lower_Princes" => "America/Lower_Princes GMT-4:00",
        "America/Maceio" => "America/Maceio GMT-3:00",
        "America/Managua" => "America/Managua GMT-6:00",
        "America/Manaus" => "America/Manaus GMT-4:00",
        "America/Marigot" => "America/Marigot GMT-4:00",
        "America/Martinique" => "America/Martinique GMT-4:00",
        "America/Matamoros" => "America/Matamoros GMT-6:00",
        "America/Mazatlan" => "America/Mazatlan GMT-7:00",
        "America/Mendoza" => "America/Mendoza GMT-3:00",
        "America/Menominee" => "America/Menominee GMT-6:00",
        "America/Merida" => "America/Merida GMT-6:00",
        "America/Metlakatla" => "America/Metlakatla GMT-9:00",
        "America/Mexico_City" => "America/Mexico_City GMT-6:00",
        "America/Miquelon" => "America/Miquelon GMT-3:00",
        "America/Moncton" => "America/Moncton GMT-4:00",
        "America/Monterrey" => "America/Monterrey GMT-6:00",
        "America/Montevideo" => "America/Montevideo GMT-3:00",
        "America/Montreal" => "America/Montreal GMT-5:00",
        "America/Montserrat" => "America/Montserrat GMT-4:00",
        "America/Nassau" => "America/Nassau GMT-5:00",
        "America/New_York" => "America/New_York GMT-5:00",
        "America/Nipigon" => "America/Nipigon GMT-5:00",
        "America/Nome" => "America/Nome GMT-9:00",
        "America/Noronha" => "America/Noronha GMT-2:00",
        "America/North_Dakota/Beulah" => "America/North_Dakota/Beulah GMT-6:00",
        "America/North_Dakota/Center" => "America/North_Dakota/Center GMT-6:00",
        "America/North_Dakota/New_Salem" => "America/North_Dakota/New_Salem GMT-6:00",
        "America/Ojinaga" => "America/Ojinaga GMT-7:00",
        "America/Panama" => "America/Panama GMT-5:00",
        "America/Pangnirtung" => "America/Pangnirtung GMT-5:00",
        "America/Paramaribo" => "America/Paramaribo GMT-3:00",
        "America/Phoenix" => "America/Phoenix GMT-7:00",
        "America/Port-au-Prince" => "America/Port-au-Prince GMT-5:00",
        "America/Port_of_Spain" => "America/Port_of_Spain GMT-4:00",
        "America/Porto_Acre" => "America/Porto_Acre GMT-5:00",
        "America/Porto_Velho" => "America/Porto_Velho GMT-4:00",
        "America/Puerto_Rico" => "America/Puerto_Rico GMT-4:00",
        "America/Punta_Arenas" => "America/Punta_Arenas GMT-3:00",
        "America/Rainy_River" => "America/Rainy_River GMT-6:00",
        "America/Rankin_Inlet" => "America/Rankin_Inlet GMT-6:00",
        "America/Recife" => "America/Recife GMT-3:00",
        "America/Regina" => "America/Regina GMT-6:00",
        "America/Resolute" => "America/Resolute GMT-6:00",
        "America/Rio_Branco" => "America/Rio_Branco GMT-5:00",
        "America/Rosario" => "America/Rosario GMT-3:00",
        "America/Santa_Isabel" => "America/Santa_Isabel GMT-8:00",
        "America/Santarem" => "America/Santarem GMT-3:00",
        "America/Santiago" => "America/Santiago GMT-4:00",
        "America/Santo_Domingo" => "America/Santo_Domingo GMT-4:00",
        "America/Sao_Paulo" => "America/Sao_Paulo GMT-3:00",
        "America/Scoresbysund" => "America/Scoresbysund GMT-1:00",
        "America/Shiprock" => "America/Shiprock GMT-7:00",
        "America/Sitka" => "America/Sitka GMT-9:00",
        "America/St_Barthelemy" => "America/St_Barthelemy GMT-4:00",
        "America/St_Johns" => "America/St_Johns GMT-4:30",
        "America/St_Kitts" => "America/St_Kitts GMT-4:00",
        "America/St_Lucia" => "America/St_Lucia GMT-4:00",
        "America/St_Thomas" => "America/St_Thomas GMT-4:00",
        "America/St_Vincent" => "America/St_Vincent GMT-4:00",
        "America/Swift_Current" => "America/Swift_Current GMT-6:00",
        "America/Tegucigalpa" => "America/Tegucigalpa GMT-6:00",
        "America/Thule" => "America/Thule GMT-4:00",
        "America/Thunder_Bay" => "America/Thunder_Bay GMT-5:00",
        "America/Tijuana" => "America/Tijuana GMT-8:00",
        "America/Toronto" => "America/Toronto GMT-5:00",
        "America/Tortola" => "America/Tortola GMT-4:00",
        "America/Vancouver" => "America/Vancouver GMT-8:00",
        "America/Virgin" => "America/Virgin GMT-4:00",
        "America/Whitehorse" => "America/Whitehorse GMT-8:00",
        "America/Winnipeg" => "America/Winnipeg GMT-6:00",
        "America/Yakutat" => "America/Yakutat GMT-9:00",
        "America/Yellowknife" => "America/Yellowknife GMT-7:00",
        "Antarctica/Casey" => "Antarctica/Casey GMT+8:00",
        "Antarctica/Davis" => "Antarctica/Davis GMT+7:00",
        "Antarctica/DumontDUrville" => "Antarctica/DumontDUrville GMT+10:00",
        "Antarctica/Macquarie" => "Antarctica/Macquarie GMT+11:00",
        "Antarctica/Mawson" => "Antarctica/Mawson GMT+5:00",
        "Antarctica/McMurdo" => "Antarctica/McMurdo GMT+12:00",
        "Antarctica/Palmer" => "Antarctica/Palmer GMT-3:00",
        "Antarctica/Rothera" => "Antarctica/Rothera GMT-3:00",
        "Antarctica/South_Pole" => "Antarctica/South_Pole GMT+12:00",
        "Antarctica/Syowa" => "Antarctica/Syowa GMT+3:00",
        "Antarctica/Troll" => "Antarctica/Troll GMT+0:00",
        "Antarctica/Vostok" => "Antarctica/Vostok GMT+6:00",
        "Arctic/Longyearbyen" => "Arctic/Longyearbyen GMT+1:00",
        "Asia/Aden" => "Asia/Aden GMT+3:00",
        "Asia/Almaty" => "Asia/Almaty GMT+6:00",
        "Asia/Amman" => "Asia/Amman GMT+2:00",
        "Asia/Anadyr" => "Asia/Anadyr GMT+12:00",
        "Asia/Aqtau" => "Asia/Aqtau GMT+5:00",
        "Asia/Aqtobe" => "Asia/Aqtobe GMT+5:00",
        "Asia/Ashgabat" => "Asia/Ashgabat GMT+5:00",
        "Asia/Ashkhabad" => "Asia/Ashkhabad GMT+5:00",
        "Asia/Atyrau" => "Asia/Atyrau GMT+5:00",
        "Asia/Baghdad" => "Asia/Baghdad GMT+3:00",
        "Asia/Bahrain" => "Asia/Bahrain GMT+3:00",
        "Asia/Baku" => "Asia/Baku GMT+4:00",
        "Asia/Bangkok" => "Asia/Bangkok GMT+7:00",
        "Asia/Barnaul" => "Asia/Barnaul GMT+7:00",
        "Asia/Beirut" => "Asia/Beirut GMT+2:00",
        "Asia/Bishkek" => "Asia/Bishkek GMT+6:00",
        "Asia/Brunei" => "Asia/Brunei GMT+8:00",
        "Asia/Calcutta" => "Asia/Calcutta GMT+5:30",
        "Asia/Chita" => "Asia/Chita GMT+9:00",
        "Asia/Choibalsan" => "Asia/Choibalsan GMT+8:00",
        "Asia/Chongqing" => "Asia/Chongqing GMT+8:00",
        "Asia/Chungking" => "Asia/Chungking GMT+8:00",
        "Asia/Colombo" => "Asia/Colombo GMT+5:30",
        "Asia/Dacca" => "Asia/Dacca GMT+6:00",
        "Asia/Damascus" => "Asia/Damascus GMT+2:00",
        "Asia/Dhaka" => "Asia/Dhaka GMT+6:00",
        "Asia/Dili" => "Asia/Dili GMT+9:00",
        "Asia/Dubai" => "Asia/Dubai GMT+4:00",
        "Asia/Dushanbe" => "Asia/Dushanbe GMT+5:00",
        "Asia/Famagusta" => "Asia/Famagusta GMT+2:00",
        "Asia/Gaza" => "Asia/Gaza GMT+2:00",
        "Asia/Harbin" => "Asia/Harbin GMT+8:00",
        "Asia/Hebron" => "Asia/Hebron GMT+2:00",
        "Asia/Ho_Chi_Minh" => "Asia/Ho_Chi_Minh GMT+7:00",
        "Asia/Hong_Kong" => "Asia/Hong_Kong GMT+8:00",
        "Asia/Hovd" => "Asia/Hovd GMT+7:00",
        "Asia/Irkutsk" => "Asia/Irkutsk GMT+8:00",
        "Asia/Istanbul" => "Asia/Istanbul GMT+3:00",
        "Asia/Jakarta" => "Asia/Jakarta GMT+7:00",
        "Asia/Jayapura" => "Asia/Jayapura GMT+9:00",
        "Asia/Jerusalem" => "Asia/Jerusalem GMT+2:00",
        "Asia/Kabul" => "Asia/Kabul GMT+4:30",
        "Asia/Kamchatka" => "Asia/Kamchatka GMT+12:00",
        "Asia/Karachi" => "Asia/Karachi GMT+5:00",
        "Asia/Kashgar" => "Asia/Kashgar GMT+6:00",
        "Asia/Kathmandu" => "Asia/Kathmandu GMT+5:45",
        "Asia/Katmandu" => "Asia/Katmandu GMT+5:45",
        "Asia/Khandyga" => "Asia/Khandyga GMT+9:00",
        "Asia/Kolkata" => "Asia/Kolkata GMT+5:30",
        "Asia/Krasnoyarsk" => "Asia/Krasnoyarsk GMT+7:00",
        "Asia/Kuala_Lumpur" => "Asia/Kuala_Lumpur GMT+8:00",
        "Asia/Kuching" => "Asia/Kuching GMT+8:00",
        "Asia/Kuwait" => "Asia/Kuwait GMT+3:00",
        "Asia/Macao" => "Asia/Macao GMT+8:00",
        "Asia/Macau" => "Asia/Macau GMT+8:00",
        "Asia/Magadan" => "Asia/Magadan GMT+11:00",
        "Asia/Makassar" => "Asia/Makassar GMT+8:00",
        "Asia/Manila" => "Asia/Manila GMT+8:00",
        "Asia/Muscat" => "Asia/Muscat GMT+4:00",
        "Asia/Nicosia" => "Asia/Nicosia GMT+2:00",
        "Asia/Novokuznetsk" => "Asia/Novokuznetsk GMT+7:00",
        "Asia/Novosibirsk" => "Asia/Novosibirsk GMT+7:00",
        "Asia/Omsk" => "Asia/Omsk GMT+6:00",
        "Asia/Oral" => "Asia/Oral GMT+5:00",
        "Asia/Phnom_Penh" => "Asia/Phnom_Penh GMT+7:00",
        "Asia/Pontianak" => "Asia/Pontianak GMT+7:00",
        "Asia/Pyongyang" => "Asia/Pyongyang GMT+9:00",
        "Asia/Qatar" => "Asia/Qatar GMT+3:00",
        "Asia/Qostanay" => "Asia/Qostanay GMT+6:00",
        "Asia/Qyzylorda" => "Asia/Qyzylorda GMT+5:00",
        "Asia/Rangoon" => "Asia/Rangoon GMT+6:30",
        "Asia/Riyadh" => "Asia/Riyadh GMT+3:00",
        "Asia/Saigon" => "Asia/Saigon GMT+7:00",
        "Asia/Sakhalin" => "Asia/Sakhalin GMT+11:00",
        "Asia/Samarkand" => "Asia/Samarkand GMT+5:00",
        "Asia/Seoul" => "Asia/Seoul GMT+9:00",
        "Asia/Shanghai" => "Asia/Shanghai GMT+8:00",
        "Asia/Singapore" => "Asia/Singapore GMT+8:00",
        "Asia/Srednekolymsk" => "Asia/Srednekolymsk GMT+11:00",
        "Asia/Taipei" => "Asia/Taipei GMT+8:00",
        "Asia/Tashkent" => "Asia/Tashkent GMT+5:00",
        "Asia/Tbilisi" => "Asia/Tbilisi GMT+4:00",
        "Asia/Tehran" => "Asia/Tehran GMT+3:30",
        "Asia/Tel_Aviv" => "Asia/Tel_Aviv GMT+2:00",
        "Asia/Thimbu" => "Asia/Thimbu GMT+6:00",
        "Asia/Thimphu" => "Asia/Thimphu GMT+6:00",
        "Asia/Tokyo" => "Asia/Tokyo GMT+9:00",
        "Asia/Tomsk" => "Asia/Tomsk GMT+7:00",
        "Asia/Ujung_Pandang" => "Asia/Ujung_Pandang GMT+8:00",
        "Asia/Ulaanbaatar" => "Asia/Ulaanbaatar GMT+8:00",
        "Asia/Ulan_Bator" => "Asia/Ulan_Bator GMT+8:00",
        "Asia/Urumqi" => "Asia/Urumqi GMT+6:00",
        "Asia/Ust-Nera" => "Asia/Ust-Nera GMT+10:00",
        "Asia/Vientiane" => "Asia/Vientiane GMT+7:00",
        "Asia/Vladivostok" => "Asia/Vladivostok GMT+10:00",
        "Asia/Yakutsk" => "Asia/Yakutsk GMT+9:00",
        "Asia/Yangon" => "Asia/Yangon GMT+6:30",
        "Asia/Yekaterinburg" => "Asia/Yekaterinburg GMT+5:00",
        "Asia/Yerevan" => "Asia/Yerevan GMT+4:00",
        "Atlantic/Azores" => "Atlantic/Azores GMT-1:00",
        "Atlantic/Bermuda" => "Atlantic/Bermuda GMT-4:00",
        "Atlantic/Canary" => "Atlantic/Canary GMT+0:00",
        "Atlantic/Cape_Verde" => "Atlantic/Cape_Verde GMT-1:00",
        "Atlantic/Faeroe" => "Atlantic/Faeroe GMT+0:00",
        "Atlantic/Faroe" => "Atlantic/Faroe GMT+0:00",
        "Atlantic/Jan_Mayen" => "Atlantic/Jan_Mayen GMT+1:00",
        "Atlantic/Madeira" => "Atlantic/Madeira GMT+0:00",
        "Atlantic/Reykjavik" => "Atlantic/Reykjavik GMT+0:00",
        "Atlantic/South_Georgia" => "Atlantic/South_Georgia GMT-2:00",
        "Atlantic/St_Helena" => "Atlantic/St_Helena GMT+0:00",
        "Atlantic/Stanley" => "Atlantic/Stanley GMT-3:00",
        "Australia/ACT" => "Australia/ACT GMT+10:00",
        "Australia/Adelaide" => "Australia/Adelaide GMT+9:30",
        "Australia/Brisbane" => "Australia/Brisbane GMT+10:00",
        "Australia/Broken_Hill" => "Australia/Broken_Hill GMT+9:30",
        "Australia/Canberra" => "Australia/Canberra GMT+10:00",
        "Australia/Currie" => "Australia/Currie GMT+10:00",
        "Australia/Darwin" => "Australia/Darwin GMT+9:30",
        "Australia/Eucla" => "Australia/Eucla GMT+8:45",
        "Australia/Hobart" => "Australia/Hobart GMT+10:00",
        "Australia/LHI" => "Australia/LHI GMT+10:30",
        "Australia/Lindeman" => "Australia/Lindeman GMT+10:00",
        "Australia/Lord_Howe" => "Australia/Lord_Howe GMT+10:30",
        "Australia/Melbourne" => "Australia/Melbourne GMT+10:00",
        "Australia/NSW" => "Australia/NSW GMT+10:00",
        "Australia/North" => "Australia/North GMT+9:30",
        "Australia/Perth" => "Australia/Perth GMT+8:00",
        "Australia/Queensland" => "Australia/Queensland GMT+10:00",
        "Australia/South" => "Australia/South GMT+9:30",
        "Australia/Sydney" => "Australia/Sydney GMT+10:00",
        "Australia/Tasmania" => "Australia/Tasmania GMT+10:00",
        "Australia/Victoria" => "Australia/Victoria GMT+10:00",
        "Australia/West" => "Australia/West GMT+8:00",
        "Australia/Yancowinna" => "Australia/Yancowinna GMT+9:30",
        "Brazil/Acre" => "Brazil/Acre GMT-5:00",
        "Brazil/DeNoronha" => "Brazil/DeNoronha GMT-2:00",
        "Brazil/East" => "Brazil/East GMT-3:00",
        "Brazil/West" => "Brazil/West GMT-4:00",
        "CET" => "CET GMT+1:00",
        "CST6CDT" => "CST6CDT GMT-6:00",
        "Canada/Atlantic" => "Canada/Atlantic GMT-4:00",
        "Canada/Central" => "Canada/Central GMT-6:00",
        "Canada/Eastern" => "Canada/Eastern GMT-5:00",
        "Canada/Mountain" => "Canada/Mountain GMT-7:00",
        "Canada/Newfoundland" => "Canada/Newfoundland GMT-4:30",
        "Canada/Pacific" => "Canada/Pacific GMT-8:00",
        "Canada/Saskatchewan" => "Canada/Saskatchewan GMT-6:00",
        "Canada/Yukon" => "Canada/Yukon GMT-8:00",
        "Chile/Continental" => "Chile/Continental GMT-4:00",
        "Chile/EasterIsland" => "Chile/EasterIsland GMT-6:00",
        "Cuba" => "Cuba GMT-5:00",
        "EET" => "EET GMT+2:00",
        "EST5EDT" => "EST5EDT GMT-5:00",
        "Egypt" => "Egypt GMT+2:00",
        "Eire" => "Eire GMT+0:00",
        "Etc/GMT" => "Etc/GMT GMT+0:00",
        "Etc/GMT+0" => "Etc/GMT+0 GMT+0:00",
        "Etc/GMT+1" => "Etc/GMT+1 GMT-1:00",
        "Etc/GMT+10" => "Etc/GMT+10 GMT-10:00",
        "Etc/GMT+11" => "Etc/GMT+11 GMT-11:00",
        "Etc/GMT+12" => "Etc/GMT+12 GMT-12:00",
        "Etc/GMT+2" => "Etc/GMT+2 GMT-2:00",
        "Etc/GMT+3" => "Etc/GMT+3 GMT-3:00",
        "Etc/GMT+4" => "Etc/GMT+4 GMT-4:00",
        "Etc/GMT+5" => "Etc/GMT+5 GMT-5:00",
        "Etc/GMT+6" => "Etc/GMT+6 GMT-6:00",
        "Etc/GMT+7" => "Etc/GMT+7 GMT-7:00",
        "Etc/GMT+8" => "Etc/GMT+8 GMT-8:00",
        "Etc/GMT+9" => "Etc/GMT+9 GMT-9:00",
        "Etc/GMT-0" => "Etc/GMT-0 GMT+0:00",
        "Etc/GMT-1" => "Etc/GMT-1 GMT+1:00",
        "Etc/GMT-10" => "Etc/GMT-10 GMT+10:00",
        "Etc/GMT-11" => "Etc/GMT-11 GMT+11:00",
        "Etc/GMT-12" => "Etc/GMT-12 GMT+12:00",
        "Etc/GMT-13" => "Etc/GMT-13 GMT+13:00",
        "Etc/GMT-14" => "Etc/GMT-14 GMT+14:00",
        "Etc/GMT-2" => "Etc/GMT-2 GMT+2:00",
        "Etc/GMT-3" => "Etc/GMT-3 GMT+3:00",
        "Etc/GMT-4" => "Etc/GMT-4 GMT+4:00",
        "Etc/GMT-5" => "Etc/GMT-5 GMT+5:00",
        "Etc/GMT-6" => "Etc/GMT-6 GMT+6:00",
        "Etc/GMT-7" => "Etc/GMT-7 GMT+7:00",
        "Etc/GMT-8" => "Etc/GMT-8 GMT+8:00",
        "Etc/GMT-9" => "Etc/GMT-9 GMT+9:00",
        "Etc/GMT0" => "Etc/GMT0 GMT+0:00",
        "Etc/Greenwich" => "Etc/Greenwich GMT+0:00",
        "Etc/UCT" => "Etc/UCT GMT+0:00",
        "Etc/UTC" => "Etc/UTC GMT+0:00",
        "Etc/Universal" => "Etc/Universal GMT+0:00",
        "Etc/Zulu" => "Etc/Zulu GMT+0:00",
        "Europe/Amsterdam" => "Europe/Amsterdam GMT+1:00",
        "Europe/Andorra" => "Europe/Andorra GMT+1:00",
        "Europe/Astrakhan" => "Europe/Astrakhan GMT+4:00",
        "Europe/Athens" => "Europe/Athens GMT+2:00",
        "Europe/Belfast" => "Europe/Belfast GMT+0:00",
        "Europe/Belgrade" => "Europe/Belgrade GMT+1:00",
        "Europe/Berlin" => "Europe/Berlin GMT+1:00",
        "Europe/Bratislava" => "Europe/Bratislava GMT+1:00",
        "Europe/Brussels" => "Europe/Brussels GMT+1:00",
        "Europe/Bucharest" => "Europe/Bucharest GMT+2:00",
        "Europe/Budapest" => "Europe/Budapest GMT+1:00",
        "Europe/Busingen" => "Europe/Busingen GMT+1:00",
        "Europe/Chisinau" => "Europe/Chisinau GMT+2:00",
        "Europe/Copenhagen" => "Europe/Copenhagen GMT+1:00",
        "Europe/Dublin" => "Europe/Dublin GMT+0:00",
        "Europe/Gibraltar" => "Europe/Gibraltar GMT+1:00",
        "Europe/Guernsey" => "Europe/Guernsey GMT+0:00",
        "Europe/Helsinki" => "Europe/Helsinki GMT+2:00",
        "Europe/Isle_of_Man" => "Europe/Isle_of_Man GMT+0:00",
        "Europe/Istanbul" => "Europe/Istanbul GMT+3:00",
        "Europe/Jersey" => "Europe/Jersey GMT+0:00",
        "Europe/Kaliningrad" => "Europe/Kaliningrad GMT+2:00",
        "Europe/Kiev" => "Europe/Kiev GMT+2:00",
        "Europe/Kirov" => "Europe/Kirov GMT+3:00",
        "Europe/Lisbon" => "Europe/Lisbon GMT+0:00",
        "Europe/Ljubljana" => "Europe/Ljubljana GMT+1:00",
        "Europe/London" => "Europe/London GMT+0:00",
        "Europe/Luxembourg" => "Europe/Luxembourg GMT+1:00",
        "Europe/Madrid" => "Europe/Madrid GMT+1:00",
        "Europe/Malta" => "Europe/Malta GMT+1:00",
        "Europe/Mariehamn" => "Europe/Mariehamn GMT+2:00",
        "Europe/Minsk" => "Europe/Minsk GMT+3:00",
        "Europe/Monaco" => "Europe/Monaco GMT+1:00",
        "Europe/Moscow" => "Europe/Moscow GMT+3:00",
        "Europe/Nicosia" => "Europe/Nicosia GMT+2:00",
        "Europe/Oslo" => "Europe/Oslo GMT+1:00",
        "Europe/Paris" => "Europe/Paris GMT+1:00",
        "Europe/Podgorica" => "Europe/Podgorica GMT+1:00",
        "Europe/Prague" => "Europe/Prague GMT+1:00",
        "Europe/Riga" => "Europe/Riga GMT+2:00",
        "Europe/Rome" => "Europe/Rome GMT+1:00",
        "Europe/Samara" => "Europe/Samara GMT+4:00",
        "Europe/San_Marino" => "Europe/San_Marino GMT+1:00",
        "Europe/Sarajevo" => "Europe/Sarajevo GMT+1:00",
        "Europe/Saratov" => "Europe/Saratov GMT+4:00",
        "Europe/Simferopol" => "Europe/Simferopol GMT+3:00",
        "Europe/Skopje" => "Europe/Skopje GMT+1:00",
        "Europe/Sofia" => "Europe/Sofia GMT+2:00",
        "Europe/Stockholm" => "Europe/Stockholm GMT+1:00",
        "Europe/Tallinn" => "Europe/Tallinn GMT+2:00",
        "Europe/Tirane" => "Europe/Tirane GMT+1:00",
        "Europe/Tiraspol" => "Europe/Tiraspol GMT+2:00",
        "Europe/Ulyanovsk" => "Europe/Ulyanovsk GMT+4:00",
        "Europe/Uzhgorod" => "Europe/Uzhgorod GMT+2:00",
        "Europe/Vaduz" => "Europe/Vaduz GMT+1:00",
        "Europe/Vatican" => "Europe/Vatican GMT+1:00",
        "Europe/Vienna" => "Europe/Vienna GMT+1:00",
        "Europe/Vilnius" => "Europe/Vilnius GMT+2:00",
        "Europe/Volgograd" => "Europe/Volgograd GMT+4:00",
        "Europe/Warsaw" => "Europe/Warsaw GMT+1:00",
        "Europe/Zagreb" => "Europe/Zagreb GMT+1:00",
        "Europe/Zaporozhye" => "Europe/Zaporozhye GMT+2:00",
        "Europe/Zurich" => "Europe/Zurich GMT+1:00",
        "GB" => "GB GMT+0:00",
        "GB-Eire" => "GB-Eire GMT+0:00",
        "GMT" => "GMT GMT+0:00",
        "GMT0" => "GMT0 GMT+0:00",
        "Greenwich" => "Greenwich GMT+0:00",
        "Hongkong" => "Hongkong GMT+8:00",
        "Iceland" => "Iceland GMT+0:00",
        "Indian/Antananarivo" => "Indian/Antananarivo GMT+3:00",
        "Indian/Chagos" => "Indian/Chagos GMT+6:00",
        "Indian/Christmas" => "Indian/Christmas GMT+7:00",
        "Indian/Cocos" => "Indian/Cocos GMT+6:30",
        "Indian/Comoro" => "Indian/Comoro GMT+3:00",
        "Indian/Kerguelen" => "Indian/Kerguelen GMT+5:00",
        "Indian/Mahe" => "Indian/Mahe GMT+4:00",
        "Indian/Maldives" => "Indian/Maldives GMT+5:00",
        "Indian/Mauritius" => "Indian/Mauritius GMT+4:00",
        "Indian/Mayotte" => "Indian/Mayotte GMT+3:00",
        "Indian/Reunion" => "Indian/Reunion GMT+4:00",
        "Iran" => "Iran GMT+3:30",
        "Israel" => "Israel GMT+2:00",
        "Jamaica" => "Jamaica GMT-5:00",
        "Japan" => "Japan GMT+9:00",
        "Kwajalein" => "Kwajalein GMT+12:00",
        "Libya" => "Libya GMT+2:00",
        "MET" => "MET GMT+1:00",
        "MST7MDT" => "MST7MDT GMT-7:00",
        "Mexico/BajaNorte" => "Mexico/BajaNorte GMT-8:00",
        "Mexico/BajaSur" => "Mexico/BajaSur GMT-7:00",
        "Mexico/General" => "Mexico/General GMT-6:00",
        "NZ" => "NZ GMT+12:00",
        "NZ-CHAT" => "NZ-CHAT GMT+12:45",
        "Navajo" => "Navajo GMT-7:00",
        "PRC" => "PRC GMT+8:00",
        "PST8PDT" => "PST8PDT GMT-8:00",
        "Pacific/Apia" => "Pacific/Apia GMT+13:00",
        "Pacific/Auckland" => "Pacific/Auckland GMT+12:00",
        "Pacific/Bougainville" => "Pacific/Bougainville GMT+11:00",
        "Pacific/Chatham" => "Pacific/Chatham GMT+12:45",
        "Pacific/Chuuk" => "Pacific/Chuuk GMT+10:00",
        "Pacific/Easter" => "Pacific/Easter GMT-6:00",
        "Pacific/Efate" => "Pacific/Efate GMT+11:00",
        "Pacific/Enderbury" => "Pacific/Enderbury GMT+13:00",
        "Pacific/Fakaofo" => "Pacific/Fakaofo GMT+13:00",
        "Pacific/Fiji" => "Pacific/Fiji GMT+12:00",
        "Pacific/Funafuti" => "Pacific/Funafuti GMT+12:00",
        "Pacific/Galapagos" => "Pacific/Galapagos GMT-6:00",
        "Pacific/Gambier" => "Pacific/Gambier GMT-9:00",
        "Pacific/Guadalcanal" => "Pacific/Guadalcanal GMT+11:00",
        "Pacific/Guam" => "Pacific/Guam GMT+10:00",
        "Pacific/Honolulu" => "Pacific/Honolulu GMT-10:00",
        "Pacific/Johnston" => "Pacific/Johnston GMT-10:00",
        "Pacific/Kiritimati" => "Pacific/Kiritimati GMT+14:00",
        "Pacific/Kosrae" => "Pacific/Kosrae GMT+11:00",
        "Pacific/Kwajalein" => "Pacific/Kwajalein GMT+12:00",
        "Pacific/Majuro" => "Pacific/Majuro GMT+12:00",
        "Pacific/Marquesas" => "Pacific/Marquesas GMT-10:30",
        "Pacific/Midway" => "Pacific/Midway GMT-11:00",
        "Pacific/Nauru" => "Pacific/Nauru GMT+12:00",
        "Pacific/Niue" => "Pacific/Niue GMT-11:00",
        "Pacific/Norfolk" => "Pacific/Norfolk GMT+11:00",
        "Pacific/Noumea" => "Pacific/Noumea GMT+11:00",
        "Pacific/Pago_Pago" => "Pacific/Pago_Pago GMT-11:00",
        "Pacific/Palau" => "Pacific/Palau GMT+9:00",
        "Pacific/Pitcairn" => "Pacific/Pitcairn GMT-8:00",
        "Pacific/Pohnpei" => "Pacific/Pohnpei GMT+11:00",
        "Pacific/Ponape" => "Pacific/Ponape GMT+11:00",
        "Pacific/Port_Moresby" => "Pacific/Port_Moresby GMT+10:00",
        "Pacific/Rarotonga" => "Pacific/Rarotonga GMT-10:00",
        "Pacific/Saipan" => "Pacific/Saipan GMT+10:00",
        "Pacific/Samoa" => "Pacific/Samoa GMT-11:00",
        "Pacific/Tahiti" => "Pacific/Tahiti GMT-10:00",
        "Pacific/Tarawa" => "Pacific/Tarawa GMT+12:00",
        "Pacific/Tongatapu" => "Pacific/Tongatapu GMT+13:00",
        "Pacific/Truk" => "Pacific/Truk GMT+10:00",
        "Pacific/Wake" => "Pacific/Wake GMT+12:00",
        "Pacific/Wallis" => "Pacific/Wallis GMT+12:00",
        "Pacific/Yap" => "Pacific/Yap GMT+10:00",
        "Poland" => "Poland GMT+1:00",
        "Portugal" => "Portugal GMT+0:00",
        "ROK" => "ROK GMT+9:00",
        "Singapore" => "Singapore GMT+8:00",
        "SystemV/AST4" => "SystemV/AST4 GMT-4:00",
        "SystemV/AST4ADT" => "SystemV/AST4ADT GMT-4:00",
        "SystemV/CST6" => "SystemV/CST6 GMT-6:00",
        "SystemV/CST6CDT" => "SystemV/CST6CDT GMT-6:00",
        "SystemV/EST5" => "SystemV/EST5 GMT-5:00",
        "SystemV/EST5EDT" => "SystemV/EST5EDT GMT-5:00",
        "SystemV/HST10" => "SystemV/HST10 GMT-10:00",
        "SystemV/MST7" => "SystemV/MST7 GMT-7:00",
        "SystemV/MST7MDT" => "SystemV/MST7MDT GMT-7:00",
        "SystemV/PST8" => "SystemV/PST8 GMT-8:00",
        "SystemV/PST8PDT" => "SystemV/PST8PDT GMT-8:00",
        "SystemV/YST9" => "SystemV/YST9 GMT-9:00",
        "SystemV/YST9YDT" => "SystemV/YST9YDT GMT-9:00",
        "Turkey" => "Turkey GMT+3:00",
        "UCT" => "UCT GMT+0:00",
        "US/Alaska" => "US/Alaska GMT-9:00",
        "US/Aleutian" => "US/Aleutian GMT-10:00",
        "US/Arizona" => "US/Arizona GMT-7:00",
        "US/Central" => "US/Central GMT-6:00",
        "US/East-Indiana" => "US/East-Indiana GMT-5:00",
        "US/Eastern" => "US/Eastern GMT-5:00",
        "US/Hawaii" => "US/Hawaii GMT-10:00",
        "US/Indiana-Starke" => "US/Indiana-Starke GMT-6:00",
        "US/Michigan" => "US/Michigan GMT-5:00",
        "US/Mountain" => "US/Mountain GMT-7:00",
        "US/Pacific" => "US/Pacific GMT-8:00",
        "US/Pacific-New" => "US/Pacific-New GMT-8:00",
        "US/Samoa" => "US/Samoa GMT-11:00",
        "UTC" => "UTC GMT+0:00",
        "Universal" => "Universal GMT+0:00",
        "W-SU" => "W-SU GMT+3:00",
        "WET" => "WET GMT+0:00",
        "Zulu" => "Zulu GMT+0:00",
        "EST" => "EST GMT-5:00",
        "HST" => "HST GMT-10:00",
        "MST" => "MST GMT-7:00",
        "ACT" => "ACT GMT+9:30",
        "AET" => "AET GMT+10:00",
        "AGT" => "AGT GMT-3:00",
        "ART" => "ART GMT+2:00",
        "AST" => "AST GMT-9:00",
        "BET" => "BET GMT-3:00",
        "BST" => "BST GMT+6:00",
        "CAT" => "CAT GMT+2:00",
        "CNT" => "CNT GMT-4:30",
        "CST" => "CST GMT-6:00",
        "CTT" => "CTT GMT+8:00",
        "EAT" => "EAT GMT+3:00",
        "ECT" => "ECT GMT+1:00",
        "IET" => "IET GMT-5:00",
        "IST" => "IST GMT+5:30",
        "JST" => "JST GMT+9:00",
        "MIT" => "MIT GMT+13:00",
        "NET" => "NET GMT+4:00",
        "NST" => "NST GMT+12:00",
        "PLT" => "PLT GMT+5:00",
        "PNT" => "PNT GMT-7:00",
        "PRT" => "PRT GMT-4:00",
        "PST" => "PST GMT-8:00",
        "SST" => "SST GMT+11:00",
        "VST" => "VST GMT+7:00",
    );

    $option_array = array(
        "Apple",
        "Google",
        "iCal",
        "Microsoft365",
        "MicrosoftTeams",
        "Outlook.com",
        "Yahoo"
    );
    ?>

    <div class="as-calendar-data-form">
        <div class="as-calendar-title">
            <h2><?php _e('Add Your Schedule Data', 'as_calendar'); ?></h2>
        </div>
        <div class="as-calendar-form-data">
            <div class="as-calendar-form-label"><label><?php _e('Name', 'as_calendar'); ?></label></div>
            <div class="as-calendar-form-input"><input type="text" name="as_calendar_name" value="<?php echo wp_kses_post($name ? $name : ''); ?>"></div>
        </div>
        <div class="as-calendar-form-data">
            <div class="as-calendar-form-label"><label><?php _e('Description', 'as_calendar'); ?></label></div>
            <div class="as-calendar-form-input"><input type="text" name="as_calendar_description" value="<?php echo wp_kses_post($description ? $description : ''); ?>"></div>
        </div>
        <div class="as-calendar-form-data">
            <div class="as-calendar-form-label"><label><?php _e('Start Date', 'as_calendar'); ?></label></div>
            <div class="as-calendar-form-input"><input type="date" name="as_calendar_start_date" value="<?php echo wp_kses_post($startDate ? $startDate : ''); ?>"></div>
        </div>
        <div class="as-calendar-form-data">
            <div class="as-calendar-form-label"><label><?php _e('End Date', 'as_calendar'); ?></label></div>
            <div class="as-calendar-form-input"><input type="date" name="as_calendar_end_date" value="<?php echo wp_kses_post($endDate ? $endDate : ''); ?>"></div>
        </div>
        <div class="as-calendar-form-data">
            <div class="as-calendar-form-label"><label><?php _e('Start Time', 'as_calendar'); ?></label></div>
            <div class="as-calendar-form-input"><input type="time" name="as_calendar_start_time" value="<?php echo wp_kses_post($startTime ? $startTime : ''); ?>"></div>
        </div>
        <div class="as-calendar-form-data">
            <div class="as-calendar-form-label"><label><?php _e('End Time', 'as_calendar'); ?></label></div>
            <div class="as-calendar-form-input"><input type="time" name="as_calendar_end_time" value="<?php echo wp_kses_post($endTime ? $endTime : ''); ?>"></div>
        </div>
        <div class="as-calendar-form-data">
            <div class="as-calendar-form-label"><label><?php _e('Location', 'as_calendar'); ?></label></div>
            <div class="as-calendar-form-input"><input type="text" name="as_calendar_location" value="<?php echo wp_kses_post($location ? $location : ''); ?>"></div>
        </div>
        <div class="as-calendar-form-data">
            <div class="as-calendar-form-label"><label><?php _e('Button Label', 'as_calendar'); ?></label></div>
            <div class="as-calendar-form-input"><input type="text" name="as_calendar_button_label" value="<?php echo wp_kses_post($button_label ? $button_label : ''); ?>"></div>
        </div>
        <div class="as-calendar-form-data">
            <div class="as-calendar-form-label"><label><?php _e('Calendar Options', 'as_calendar'); ?></label></div>

            <div class="as-calendar-form-input">
                <div class="as-calendar-form-checkboxes">
                    <?php foreach ($option_array as $key) { ?>
                        <div class="as-calendar-options-data">
                            <div class="as-calendar-options-data-checkbox">
                                <input type="checkbox" id="<?php _e($key, 'as_calendar'); ?>" name="as_calendar_options[<?php _e($key, 'as_calendar'); ?>][is_checked]" value="on" <?php if (isset($options[$key]) && isset($options[$key]['is_checked'])) {
                                                                                                                                                                                        _e("checked='checked'", 'as_calendar');
                                                                                                                                                                                    } ?>>
                                <label for="<?php _e($key, 'as_calendar'); ?>"><?php _e($key, 'as_calendar'); ?></label>
                            </div>
                            <input type="text" name="as_calendar_options[<?php _e($key, 'as_calendar'); ?>][label]" class="as-calendar-options-extra-txt" placeholder="<?php _e('Custom Label Name', 'as_calendar')  ?>" value="<?php if (isset($options[$key]) && isset($options[$key]['label'])) {
                                                                                                                                                                                                                                    _e($options[$key]['label'], 'as_calendar');
                                                                                                                                                                                                                                } else {
                                                                                                                                                                                                                                    _e($key, 'as_calendar');
                                                                                                                                                                                                                                } ?>">
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
        <div class="as-calendar-form-data">
            <div class="as-calendar-form-label"><label><?php _e('Choose TimeZone', 'as_calendar'); ?></label></div>
            <div class="as-calendar-form-input">
                <select name="as_calendar_timezone">
                    <?php
                    foreach ($timezone_data as $key => $value) {
                    ?>
                        <option value="<?php _e($key, 'as_calendar'); ?>" <?php if (!empty($timeZone) && $key == $timeZone) {
                                                                                _e('selected="selected"', 'as_calendar');
                                                                            } ?>><?php echo wp_kses_post($value); ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>

        <div class="as-calendar-form-data">
            <div class="as-calendar-form-label"><label><?php _e('Choose Display Style', 'as_calendar'); ?></label></div>
            <div class="as-calendar-form-input">
                <select name="as_calendar_list_style">
                    <option value="" <?php if (empty($list_style)) {
                                            _e('selected="selected"', 'as_calendar');
                                        } ?>><?php _e('Default', 'as_calendar'); ?></option>
                    <option value="modal" <?php if ($list_style ==  'modal') {
                                                _e('selected="selected"', 'as_calendar');
                                            } ?>><?php _e('Modal', 'as_calendar'); ?></option>
                    <option value="overlay" <?php if ($list_style == 'overlay') {
                                                _e('selected="selected"', 'as_calendar');
                                            } ?>><?php _e('Overlay', 'as_calendar'); ?></option>
                </select>
            </div>
        </div>
        <div class="as-calendar-form-data">
            <div class="as-calendar-form-label"><label><?php _e('iCal File Name', 'as_calendar'); ?></label></div>
            <div class="as-calendar-form-input"><input type="text" name="as_calendar_button_ical_file_name" value="<?php echo wp_kses_post($iCalFileName ? $iCalFileName : 'download_invite'); ?>"></div>
        </div>
    </div>
<?php
}

function as_calendar_save_meta_box($post_id)
{
    $post = get_post($post_id);
    if ($post->post_type == "as_calendar") {
        update_post_meta($post_id, 'as_calendar_name', isset($_POST['as_calendar_name']) ? wp_kses_post($_POST['as_calendar_name']) : "");
        update_post_meta($post_id, 'as_calendar_description', isset($_POST['as_calendar_description']) ? wp_kses_post($_POST['as_calendar_description']) : "");
        update_post_meta($post_id, 'as_calendar_start_date', isset($_POST['as_calendar_start_date']) ? wp_kses_post($_POST['as_calendar_start_date']) : "");
        update_post_meta($post_id, 'as_calendar_end_date', isset($_POST['as_calendar_end_date']) ? wp_kses_post($_POST['as_calendar_end_date']) : "");
        update_post_meta($post_id, 'as_calendar_start_time', isset($_POST['as_calendar_start_time']) ? wp_kses_post($_POST['as_calendar_start_time']) : "");
        update_post_meta($post_id, 'as_calendar_end_time', isset($_POST['as_calendar_end_time']) ? wp_kses_post($_POST['as_calendar_end_time']) : "");
        update_post_meta($post_id, 'as_calendar_location', isset($_POST['as_calendar_location']) ? wp_kses_post($_POST['as_calendar_location']) : "");
        update_post_meta($post_id, 'as_calendar_button_label', isset($_POST['as_calendar_button_label']) ? wp_kses_post($_POST['as_calendar_button_label']) : "");
        update_post_meta($post_id, 'as_calendar_options', isset($_POST['as_calendar_options']) ? wp_kses_allowed_html($_POST['as_calendar_options']) : "");
        update_post_meta($post_id, 'as_calendar_timezone', isset($_POST['as_calendar_timezone']) ? wp_kses_post($_POST['as_calendar_timezone']) : "");
        update_post_meta($post_id, 'as_calendar_list_style', isset($_POST['as_calendar_list_style']) ? wp_kses_post($_POST['as_calendar_list_style']) : "");
        update_post_meta($post_id, 'as_calendar_button_ical_file_name', isset($_POST['as_calendar_button_ical_file_name']) ? wp_kses_post($_POST['as_calendar_button_ical_file_name']) : "");
    }
}
add_action('save_post', 'as_calendar_save_meta_box');
