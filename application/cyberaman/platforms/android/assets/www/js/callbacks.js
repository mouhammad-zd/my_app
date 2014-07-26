// This function will be used as callback for the upload success process
// Need Review
function upload_succeed(r,type,location,hidden_input,success_callback,error_callback)
    {
        var result = $.parseJSON(r.response);
        if(result.s === '1')// In case that image meet all requirement
            {
                    call_function(success_callback,result);
            }
        else// In case that image doesn't meet requirement or for any other reason that doesn't let server to store uploaded image
            {
                    call_function(error_callback,result);
            }
    }
    
// This function will be use a fail callback function to be executed on upload failure
// Need Review
function upload_fail(error)
    {
        alert("An error has occurred: Code = " + error.code);
        //console.log("upload error source " + error.source);
        //console.log("upload error target " + error.target);
    }
    
// This function will be user as callback for registration process to take suitable action
//
function handle_registration_response(data,target)
    {
        if(data.s === 1 || data.s === '1')//If registration is completed
            {
                warn_user(target,languages[g_language]['reg_successfully_registered'],0,5000);
                $.ui.goBack();
                g_token = data.t;
                //execute_query("UPDATE params set token = '" + data.t + "' where web_id = 1",4);
                //var pushNotification = window.plugins.pushNotification;
                //pushNotification.register(successHandler, errorHandler,{"senderID":"4350835736","ecb":"onNotificationGCM"});
                localStorage.setItem("token",g_token);
            }
        else
            {
                var persists = 0;
                var timeout = 6000;
                if(data.s === '001')//If empty error response occured
                    {
                        var v = data.v;
                        if(v.n === 0)
                            {
                                warn_user(target,languages[g_language]['reg_enter_phone'],persists,timeout);
                            }
                        else if(v.p === 0)
                            {
                                warn_user(target,languages[g_language]['reg_enter_password'],persists,timeout);
                            }
                        else if(v.f === 0)
                            {
                                warn_user(target,languages[g_language]['reg_enter_fullname'],persists,timeout);
                            }
                        else if(v.e === 0)
                            {
                                warn_user(target,languages[g_language]['reg_enter_valid_email'],persists,timeout);
                            }
                    }
                else if(data.s === '002')// If nickname or email are already found in server database
                    {
                        warn_user(target,languages[g_language]['reg_already_found'],persists,timeout);
                    }
                else// If an unkown error has been occured ( actually connection failed or invalid infos passed )
                    {
                            warn_user(target,languages[g_language]['reg_registration_failure'],persists,timeout);
                    }
            }
    }
    
// This function will be used as callback for the login process
// 
function handle_login_response(data,target)
    {
        if(data.s === '1')//If infos are valid
            {
                /*$('#main_page').remove();
                $.ui.loadContent('news.html?v=1',false,true,"pop",true);
                $('.panel').css('opacity','1');
                $.ui.clearHistory();
                token = data.t;
                execute_query("UPDATE params set token = '" + data.t + "' where web_id = 1",4);*/
                //localStorage.setItem("token",token);
                if(data.p === '1')//If user is admin
                    {
                        control_aside_as('admin');
                    }
                else
                    {
                        control_aside_as('user');
                    }
            }
        else
            {
                warn_user(target,languages[g_language]['login_invalid_infos'],0,10000);
            }
    }
    
// This function will be use as success callback function of the function "get_news" in the file operations.js
// This function will get a data parameter returned from previous function then fetch it to fill a specific element inside page
// With formatted fetched data
// data : data passed back from "get_news" function
// put_in : Element selector to fill with data
// is_more : specify if we are fetching data from a more button click action or not
// place : Type of fill it will  be after element passed in "put_in" parameter or before it
//
function fill_news(data,put_in,is_more,place)
    {
        switch(data.s)
            {
                
            }
        var result = data.r.result;
        var target = $(put_in);
        var last_date = "";
        var last_id = "";
        var counter = 0;
        var background_color = "";
        var category_name = "";
        if(is_more === 0)//In the case that we are not executing a more action so we will empty target to fill it with data else we will append it
            {
                target.html('');
            }
        // Iterate through result array of object to fill target with fetched data
        $.map(result,function(els,i)
                {
                    background_color = (g_last_news === '1' && i%2 === 0)?'#f4f4f4;' : '#ffffff;';
                    var row_obj = {eid : els.id,etitle : els.title,etype : els.type,eurgent : els.urgent,epic : els.pic,
                                            eauthor : els.author,eagency : els.agency,edate : els.date,sdate : els.social_date,childs : "",wrapper : $('<li></li>')
                                            };
                    if(row_obj.etype === 'news')//fetching news rows
                        {
                            var news_tp = tp_news_row;
                            var news_image_tp = '';
                            var news_agency = g_web_link + row_obj.eagency;
                            var urgent_class = (row_obj.eurgent === 1)?" urgent" : "";
                            
                            if(row_obj.epic !== 'newspaper.png')
                                {
                                    news_image_tp = tp_news_image;
                                    news_image_tp.replaceArray(['{n_u}','{n_i}','{g_w}','{n_p}'],[]);
                                }

                            news_tp.replaceArray(['{b_c}','{n_a}','{n_u}','{n_i}','{n_t}','{n_p}'],[background_color,news_agency,urgent_class,row_obj.eid,row_obj.etitle,news_image_tp]);
                            row_obj.childs = news_tp;
                            last_date = row_obj.edate;
                            last_id = row_obj.eid;
                            counter ++;
                        }
                    else if(row_obj.etype === "category" && is_more === 0)// Fetching category row
                                {
                                    category_name = row_obj.etitle;
                                    if(g_last_news === '1' || g_current_page === 'category')// Current settings is to show just last news
                                        {
                                            row_obj.childs += '<div class="news_category_header">' + row_obj.etitle + '</div>';
                                        }
                                    else
                                        {
                                            row_obj.childs += '<a href="#" class="news_category_link" data-href="category.html" data-id="' + row_obj.eid + '" data-persist-ajax="true" data-refresh-ajax="true">'+
                                                                    '<div class="news_category_header">' + row_obj.etitle + '</div>'+
                                                              '</a>';
                                        }
                                }
                    else if(row_obj.etype === "more")
                                {
                                    var ecat = els.cat;
                                    if(is_more === 1)
                                        {
                                            if(row_obj.eid === "-1")
                                                target.remove();
                                            else
                                                {
                                                    $(target).attr('data-urgent',row_obj.eurgent);
                                                    $(target).attr('data-date',last_date);
                                                    $(target).attr('data-id',last_id);
                                                }
                                        }
                                    else
                                        {
                                            if(row_obj.eid !== "-1")
                                                {
                                                    var news_more_tp = tp_news_more;
                                                    var more_label = '';
                                                    if(g_last_news === '1')
                                                        more_label = languages[g_language]['news_more_label'];
                                                    else if(g_current_page === 'news_reporters')
                                                        more_label = languages[g_language]['news_reporter_more_label'] + reporter_name;
                                                    else
                                                        more_label = languages[g_language]['news_cat_more_label'] + category_name;
                                                    news_more_tp.replaceArray(['{n_c}','{n_u}','{l_d}','{n_c}','{n_i}','{m_l}'],[ecat,row_obj.eurgent,last_date,ecat,row_obj.eid,more_label]);
                                                    row_obj.childs = news_more_tp;
                                                }
                                        }
                                }
                    row_obj.wrapper.append(row_obj.childs);
                    if(place === 'before')
                        {
                            row_obj.wrapper.insertBefore(target);
                        }
                    else if(place === 'append')
                        {
                            row_obj.wrapper.appendTo(target);
                        }
                    if(counter < 5 && is_more == 1)
                        $(target).remove();
                });
    }