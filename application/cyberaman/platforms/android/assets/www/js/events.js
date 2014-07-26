function ev_device_ready()
    {
        bind_custom_events();
        //analytics.startTrackerWithId('UA-52344579-1');
        control_aside_as('guest');
        if(localStorage.getItem('token'))
            {
                var i = {};
                i['t'] = localStorage.getItem('token');
                i['u'] = device.uuid;
                var data = {op : 'v',i : i};
                $.ajax({url : g_api_link,data : data,type : "POST",dataType : "json",
                        success : function(data)
                                    {
                                        if(data.s === '1')
                                            {
                                                localStorage.setItem('token',data.t);
                                                if(data.p === '1')
                                                    {
                                                        g_role = 2;
                                                        control_aside_as('admin');
                                                    }
                                                else
                                                    {
                                                        g_role = 1;
                                                        control_aside_as('user');
                                                    }
                                            }
                                        else
                                            {
                                                localStorage.removeItem('token');
                                                control_aside_as('guest');
                                                g_role = 0;
                                            }
                                    },
                        error : function(error){alert(error);console.log(error.responseText);}
                    });
            }
        else
            {
                control_aside_as('guest');
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