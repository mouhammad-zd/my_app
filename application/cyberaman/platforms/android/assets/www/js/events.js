function ev_afui_ready()
{
    bind_custom_events();
    if(g_role === 2)
        {
            control_aside_as('admin');
            load_page("news.html",[false,true,"slide",true]);
        }
    else if(g_role === 1)
        {
            control_aside_as('user');
            load_page("news.html",[false,true,"slide",true]);
        }
    else
        {
            control_aside_as('guest');
            load_page("main.html",[false,true,"slide",true]);
        }
    //$.ui.loadContent("main.html",false,true,"pop",true);
}

function ev_document_ready()
    {
        if(localStorage.getItem('token'))
            {
                var i = {};
                i['t'] = localStorage.getItem('token');
                i['u'] = device.uuid;
                i['g'] = 1;
                var data = {op : 'v',i : i};
                $.ajax({url : g_api_link,data : data,type : "POST",dataType : "json",timeout: 500000,
                        success : function(data)
                                    {
                                        $.ui.backButtonText = " ";
                                        $.ui.launch();
                                        //analytics.startTrackerWithId('UA-52344579-1');
                                        control_aside_as('guest');
                                        if(data.s === '1')
                                            {
                                                store_user_infos(data.i);
                                                localStorage.setItem('token',data.t);
                                                g_token = data.t;
                                                if(data.p === '1')
                                                        g_role = 2;
                                                else
                                                        g_role = 1;
                                            }
                                        else
                                            {
                                                localStorage.removeItem('token');
                                                g_role = 0;
                                            }
                                    },
                        error : function(error){alert(error);alert(error.responseText);g_role=0}
                    });
            }
        else
            {
                $.ui.backButtonText = " ";
                $.ui.launch();
                g_role = 0;
            }
    }
    
/* This code is used for Intel native apps */
function ev_device_xdk_ready()
    {
        intel.xdk.device.hideSplashScreen();
    };
    
function ev_button_back()
    {
        if($('.backButton').css('visibility') === 'hidden')
            {
                setTimeout(function()
                            {
                                navigator.app.exitApp();
                            },'3000');
            }
        else
            {
                warn_user('#warning_header','',0,0);
                $.ui.goBack();
            }
    }

function ev_menu_swipe(direction)
    {
        if(direction === 'left')
            $.ui.toggleRightSideMenu(true);
        else if(direction === 'right')
            $.ui.toggleRightSideMenu(false);
        else if(direction === 'toggle')
            $.ui.toggleRightSideMenu();
        else
            return false;
    }
    
function ev_pause()
    {
    
    }

function ev_resume()
    {
    
    }

//This function will be executed each time the device is detected as online
//Note : It may be executed also before "device ready" event
function ev_online()
    {
    
    }

//This function will be executed each time device is detected as offline (disconnect from internet)
function ev_offline()
    {
    
    }