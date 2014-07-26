var g_current_page = "f";
var g_web_link = "http://192.168.0.105/phptransformerapi/";
var g_current_program = "cybernews";
var g_language = "Arabic";
var g_api_link = "http://192.168.0.105/phptransformerapi/programs/api/api_full.php";
var g_token = "";
var g_uuid = "";
var g_last_news = '1';
var g_news_per_page = 5;
var g_just_urgent = '0';
var g_role = 0;

$.ui.ready(initialize_application());

//This function will call all our intializing functions
function initialize_application()
{
    bind_system_events();
}

//This function will bind system basic events as ready,backbutton,menubutton,swipeleft,swiperight,online,offline ....
function bind_system_events()
    {
        document.addEventListener('deviceready',ev_device_ready, false);
        document.addEventListener("intel.xdk.device.ready", ev_device_xdk_ready, false);
        document.addEventListener('pause',ev_pause, false);
        document.addEventListener('resume',ev_resume, false);
        document.addEventListener("online",ev_online,false);
        document.addEventListener("offline",ev_offline,false);
        document.addEventListener("swipeLeft",function(){ev_menu_swipe('left');},false);
        document.addEventListener("swipeRight",function(){ev_menu_swipe('right');},false);
        document.addEventListener("menubutton", function(){ev_menu_swipe('toggle');}, false);
        document.addEventListener("backbutton",ev_button_back,false);
    }

//This function will bind custom events as click and load panels
function bind_custom_events()
    {
        $(document).on('click','#aside_closeapplication_link',exit_application);
        $(document).on('click','#aside_share_link',function(){g_old_page = g_current_page;g_current_page="share";share();g_current_page = g_old_page;})
        /*$(document).on('loadpanel','#news_unlogged_in_page',get_news);
        $(document).on('loadpanel','#news_details_unlogged_in_page',load_news_details);
        $(document).on('loadpanel','#category_news_unlogged_in_page',load_category);
        $(document).on('loadpanel','.panel',function(){$('.panel').css('opacity','1');$('.afScrollPanel').css('opacity','1');$('.afScrollbar').css('opacity','1');})
        $(document).on('click','#share_link',share);
        $(document).on('click','#refresh_link',refresh);
        $(document).on('click','#news_by_cat_link',load_news_page);
        $(document).on('click','#last_news_link',load_last_news_page);*/
    }

//This function will detect if current device is currently connected to internet and return status
function is_connected()
    {
        var networkState = navigator.network.connection.type;

        var states = {};
        states[Connection.UNKNOWN]  = 'Unknown connection';
        states[Connection.ETHERNET] = 'Ethernet connection';
        states[Connection.WIFI]     = 'WiFi connection';
        states[Connection.CELL_2G]  = 'Cell 2G connection';
        states[Connection.CELL_3G]  = 'Cell 3G connection';
        states[Connection.CELL_4G]  = 'Cell 4G connection';
        states[Connection.NONE]     = 'No network connection';

        if(states[networkState] === 'No network connection')
            return false;
        else
            return true;
    }