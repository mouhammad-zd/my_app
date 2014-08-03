function reg_fill_selects()
    {
        if($('#reg_zip_sel').length > 0)
            $('#reg_zip_sel').remove();
        
        var reg_zip_sel = $('<select id="reg_zip_sel" class="reg_select"></select>');
        get_all_zip_codes(reg_zip_sel);
        reg_zip_sel.insertBefore($('#reg_phone_txt'));
        
        if($('#reg_country_sel').length > 0)
            $('#reg_country_sel').remove();
        
        var reg_country_sel = $('<select id="reg_country_sel" class="reg_select"></select>');
        get_all_countries(reg_country_sel);
        reg_country_sel.insertBefore($('#reg_country_span'));
    }
        
function upload_user_pic(type_of_upload,img_to_update)
{
    warn_user('#warning_header','',1,10000);
    upload(type_of_upload,
           '',
           '',
           function(r)
                {
                    if(r.s === '1')
                        {
                            if(img_to_update !== "")
                                {
                                    var random_num = parseInt(Math.random() * 100);
                                    var user_pic = r.r.replace('_256','_128');
                                    $(img_to_update).attr('src',g_web_link + "uploads/users/" + g_user.n + "/" + user_pic + '?v=' + random_num);
                                }
                            else
                                $('#reg_image_txt').val(r.r);
                            warn_user('#warning_header',languages[g_language]['reg_image_uploaded'],0,5000);
                        }
                },
           function(r)
                {
                    console.log('special caseeeeeeeeeeee');
                    if(r.r === '001')
                        {
                            warn_user('#warning_header',languages[g_language]['reg_no_image_uploaded'],0,10000);
                        }
                    else if(r.r === '002')
                        {
                            warn_user('#warning_header',languages[g_language]['reg_type_not_allowed'],0,10000);
                        }
                    else if(r.r === '003')
                        {
                            warn_user('#warning_header',languages[g_language]['reg_unkown_upload_error'],0,10000);
                        }
                    else
                        {
                            warn_user('#warning_header',languages[g_language]['reg_unsufficient_privileges'],0,10000);
                        }
                },
           function(error)
                {
                    if(error === "Selection cancelled.")
                        warn_user('#warning_header',languages[g_language]['reg_no_image_uploaded'],0,10000);
                    else
                        warn_user('#warning_header',error,0,10000);
                }
          )
}

function upload_news_media(type_of_upload,img_to_update)
{
    var warning_target = '#warning_footer';
    warn_user(warning_target,'',1,10000);
    upload(type_of_upload,
           '',
           '',
           function(r)
                {
                    if(r.s === '1')
                        {
                            if(img_to_update !== "")
                                {
                                    var random_num = parseInt(Math.random() * 100);
                                    var user_pic = r.r.replace('_256','_128');
                                    //$(img_to_update).attr('src',g_web_link + "uploads/users/" + g_user.n + "/" + user_pic + '?v=' + random_num);
                                }
                            else
                                {
                                    var target = "";
                                    if(type_of_upload === "news_video")
                                        target = $('#news_sender_videos_hidden');
                                    else if(type_of_upload === "news_pic")
                                        target = $('#news_sender_images_hidden');
                                    
                                    if(target.val() === "")
                                        target.val(r.r);
                                    else
                                        target.val($(target).val() + '||' + r.r);
                                }
                            warn_user(warning_target,languages[g_language]['news_sender_image_uploaded'],0,5000);
                        }
                },
           function(r)
                {
                    console.log('special caseeeeeeeeeeee');
                    if(r.r === '1')
                        {
                            warn_user(warning_target,languages[g_language]['news_sender_no_image_uploaded'],0,10000);
                        }
                    else if(r.r === '2')
                        {
                            warn_user(warning_target,languages[g_language]['news_sender_type_not_allowed'],0,10000);
                        }
                    else if(r.r === '3')
                        {
                            warn_user(warning_target,languages[g_language]['news_sender_upload_error'],0,10000);
                        }
                    else
                        {
                            warn_user(warning_target,languages[g_language]['news_sender_unsufficient_privileges'],0,10000);
                        }
                },
           function(error)
                {
                    if(error === "Selection cancelled.")
                        warn_user(warning_target,languages[g_language]['news_sender_no_image_uploaded'],0,10000);
                    else
                        warn_user(warning_target,error,0,10000);
                }
          )
}

function open_reg_page()
    {    
        g_current_page = "signup";
        //analytics.trackView('Sign Up');
        $('#reg_btn').off('click');
        $('#reg_btn').on('click',register_me);
        $('#reg_upload_btn').off('click');
        $('#reg_upload_btn').on('click',function(){upload_user_pic('user_pic');});
        reg_fill_selects();
        $.selectBox.getOldSelects('reg_wrapper_div');
    }
    
    
function open_about_page()
    {
        g_current_page = "about";
        //analytics.trackView('About Us');
    }
    
function open_main_page()
    {
       g_current_page = "main";
    }
    
function open_login_page()
    {
        g_current_page = "login";
        $('#login_btn').on('click',login_me);
    }
    
function open_news_page()
    {
        get_news();
    }
    
function generate_captcha(random_number1,random_number2,target,before_target,target_name)
    {
        if(random_number1 === "")
            random_number1 = parseInt(Math.random() * 10);
        if(random_number2 === "")
            random_number2 = parseInt(Math.random() * 10);
        if(target === "undefined" || typeof target === "undefined" || typeof target === null || target === "")
            {
                if(before_target === "undefined" || typeof before_target === "undefined" || typeof before_target === null || before_target === "")
                    {
                        $('#captcha_question_span').remove();
                        $('<span id="captcha_question_span" class="rfloat captcha"> ? = ' + random_number1 + ' + ' + random_number2 + '</span>').insertBefore($('#contactus_captcha_result_txt'));
                    }
                else
                    {
                        if($('#' + target_name).length > 0)
                            $('#' + target_name).remove();
                        $('<span id="' + target_name + '" class="rfloat captcha"> ? = ' + random_number1 + ' + ' + random_number2 + '</span>').insertBefore($(before_target));
                    }
            }
        else
            {
                $(target).html('? = ' + random_number1 + ' + ' + random_number2);
            }
        localStorage.setItem("answer",random_number1 + random_number2);
    }
function open_contactus_page()
    {
        g_current_page = "contactus";
        if($('#contactus_departments_sel').length > 0)
            $('#contactus_departments_sel').remove();
        $('#contactus_departments_loading_img').show();
        var i = {};
        i['l'] = g_language;
        var data = {op : 'g_a_d',i : i};
        var department_sel = $('<select id="contactus_departments_sel" class="contactus_sel"></select>');
        $.ajax({url : g_api_link,data : data,type : 'POST',dataType : 'json',
                success : function(data)
                            {
                                var number1 = data.n1;
                                var number2 = data.n2;
                                if(data.s === '0')
                                    {
                                        department_sel.append('<option value="-1">' + languages[g_language]['contactus_no_department'] + '</option>');
                                    }
                                else if(data.s === '1')
                                    {
                                        var departments = data.r;
                                        get_all_departments(department_sel,departments);
                                    }
                                department_sel.insertBefore($('#contactus_departments_span'));
                                $('#contactus_departments_loading_img').hide();
                                $.selectBox.getOldSelects('contactus_wrapper_div');
                                generate_captcha(number1,number2);
                                $('#contactus_send_btn').on('click',contactus_send_email);
                            },
                error : function(error)
                            {
                                department_sel.append('<option value="-1">' + languages[g_language]['contactus_no_department'] + '</option>');
                                department_sel.insertBefore($('#contactus_departments_span'));
                                $('#contactus_departments_loading_img').hide();
                                $.selectBox.getOldSelects('contactus_wrapper_div');
                                generate_captcha("","");
                                $('#contactus_send_btn').on('click',contactus_send_email);
                            }
               })
    }
    
function open_account_page()
    {
        var random_num = parseInt(Math.random() * 100);
        $('#account_image').attr('src',g_web_link + g_user.p + '?v=' + random_num);
        $('#account_points_span').html(languages[g_language]['account_points'] + g_user.pt);
        $('#account_image_btn').off('click');
        $('#account_image_btn').on('click',function(){upload_user_pic('already_user_pic','#account_image');});
        $('#account_nickname_txt').val(g_user.n);
        $('.account_input.editable').on('tap',function()
                                                {
                                                    $(this).removeClass('unselected').addClass('selected');
                                                });
        $('.account_input.editable').on('blur',function()
                                                {
                                                    $(this).removeClass('selected').addClass('unselected');
                                                });
        
        $('#account_email_txt').val(g_user.e);
        $('#account_cellnumber_txt').val(g_user.c);
        if(g_user.co === "")
            {
                if($('#account_country_sel').length > 0)
                    $('#account_country_sel').remove();
        
                var account_country_sel = $('<select id="account_country_sel" class="account_select"></select>');
                account_country_sel.append('<option value="-1">' + languages[g_language]['account_choose_country'] + '</option>');
                get_all_countries(account_country_sel);
                account_country_sel.insertBefore($('#account_country_span'));
                $.selectBox.getOldSelects('account_wrapper_div');
            }
        else
            {
                //var account_country_div = $('<div id="account_country_div">' + data_countries_iso_codes[g_language][g_user.co] + '</div>');
                //account_country_div.insertBefore($('#account_country_span'));
                $('#account_country_txt').val(data_countries_iso_codes[g_language][g_user.co]);
            }
        $('#account_password_txt').hide();
        $('#account_password_btn').on('click',
                                        function()
                                            {
                                                $('#account_password_txt').show();
                                                $('#account_password_btn').hide();
                                            });
        $('#account_password_txt').on('blur',
                                        function()
                                            {
                                                $('#account_password_txt').hide();
                                                $('#account_password_btn').show();
                                            })
        $('#account_save_btn').on('click',save_user_infos);
    }
    
function open_last_reporters_page()
    {
        var data = {op : 'g_l_r'};
        $.ajax({url : g_api_link,data : data,type : 'POST',dataType : 'json',
                success : function(data)
                            {
                                $.map(data.r,function(element,index)
                                    {
                                        var full_name = element.f;
                                        var nick_name = element.n;
                                        var user_pic = g_web_link + element.p;//.replace('_128','_64')
                                        var user_points = element.pt + "pt(s)";
                                        var user_id = element.i;
                                        
                                        var last_reporter_tp = tp_last_reporter;
                                        last_reporter_tp = last_reporter_tp.replaceArray(["{r_p}","{r_f}","{r_pt}","{r_n}","{r_i}"],[user_pic,full_name,user_points,nick_name,user_id]);
                                        last_reporter_tp = '<li>' + last_reporter_tp + '</li>';
                                        $('#last_reporters_ul').append(last_reporter_tp);
                                    });
                                $('.reporter_image').on('click',function(){
                                   localStorage.setItem("reporter_id",$(this).attr('data-id'));
                                   load_page("reporter.html",[false,false,"slide",true]);
                                });
                            },
                error : function(error){console.log(error);console.log(error.responseText);}
               });
    }
    
function open_reporter_page()
    {
        var data = {op : 'g_u_i',i : {i : localStorage.getItem("reporter_id"),j : 1}};
        $.ajax({url : g_api_link,data : data,type : 'POST',dataType : 'json',
                success : function(data)
                            {
                                localStorage.removeItem("reporter_id");
                                console.log(data);
                                var random_num = parseInt(Math.random() * 100);
                                $('#account_image').attr('src',g_web_link + data.i.p + '?v=' + random_num);
                                $('#account_points_span').html(languages[g_language]['account_points'] + data.i.pt);
                                $('#account_nickname_txt').val(data.i.n);
                                $('#account_email_txt').val(data.i.e);
                                $('#account_cellnumber_txt').val(data.i.c);
                                $('#account_country_txt').val(data_countries_iso_codes[g_language][data.i.co]);
                            },
                error : function(error){alert(error);alert(error.responseText);}
                });
    }
    
function open_news_sender_page()
{
    if(g_role !== 2)
        {
            $('.admin').hide();
        }
    $.ui.blockUI(.5);
    var data = {op : 'g_a_c',i : {l : g_language}};
    $.ajax({
            url : g_api_link,
            data : data,
            type : 'POST',
            dataType : 'json',
            success : function(data)
                        {
                            if(data.s === '1')
                                {
                                    if($('#news_sender_categories_sel').length > 0)
                                        $('#news_sender_categories_sel').remove();
        
                                    var news_sender_categories_sel = $('<select id="news_sender_categories_sel" class="news_sender_select"></select>');
                                    //news_sender_categories_sel.append('<option value="-1">' + languages[g_language]['account_choose_country'] + '</option>');
                                    get_all_categories(news_sender_categories_sel,data.r);
                                    news_sender_categories_sel.insertBefore($('#news_sender_categories_span'));
                                }
                            else if(data.s === '001')
                                {
                                    if($('#news_sender_categories_sel').length > 0)
                                        $('#news_sender_categories_sel').remove();
        
                                    var news_sender_categories_sel = $('<select id="news_sender_categories_sel" class="news_sender_select"></select>');
                                    get_all_categories(news_sender_categories_sel,{i : "-1",n : languages[g_language]['news_sender_no_categories_found']});
                                    news_sender_categories_sel.insertBefore($('#news_sender_categories_span'));
                                }
                            if(g_role === 2)
                                {
                                    var agencies_data = {op : 'g_a_a',i : {t : g_token}};
                                    $.ajax({url : g_api_link,data : agencies_data,type : 'POST',dataType : 'json',
                                            success: function(data)
                                                        {
                                                            console.log("result = " + data.s);
                                                            if(data.s === '1')
                                                                {
                                                                    if($('#news_sender_agencies_sel').length > 0)
                                                                        $('#news_sender_agencies_sel').remove();
        
                                                                    var news_sender_agencies_sel = $('<select id="news_sender_agencies_sel" class="news_sender_select"></select>');
                                                                    //news_sender_categories_sel.append('<option value="-1">' + languages[g_language]['account_choose_country'] + '</option>');
                                                                    get_all_agencies(news_sender_agencies_sel,data.r);
                                                                    news_sender_agencies_sel.insertBefore($('#news_sender_agencies_span'));
                                                                    $.ui.unblockUI();
                                                                    generate_captcha("","","#news_sender_captcha_span");
                                                                    $.selectBox.getOldSelects('news_sender_wrapper_div');
                                                                    $('#news_sender_send_btn').on('click',send_news);
                                                                    $('#news_sender_add_picture_btn').on('click',function(){upload_news_media('news_pic',"");});
                                                                    $('#news_sender_add_video_btn').on('click',function(){upload_news_media('news_video',"");});
                                                                }
                                                            else if(data.s === '001')
                                                                {
                                                                    $.ui.unblockUI();
                                                                    warn_user('#warning_header',languages[g_language]['news_sender_agencies_lack_infos'],0,1000);
                                                                }
                                                            else if(data.s === '002')
                                                                {
                                                                    $.ui.unblockUI();
                                                                    warn_user('#warning_header',languages[g_language]['news_sender_invalid_token'],0,4000);
                                                                }
                                                            else if(data.s === '003')
                                                                {
                                                                    $.ui.unblockUI();
                                                                    warn_user('#warning_header',languages[g_language]['news_sender_not_admin'],0,4000);
                                                                }
                                                        },
                                            error: function(error)
                                                        {
                                                            console.log(error);
                                                            console.log(error.responseText);
                                                            $.ui.unblockUI();
                                                            warn_user('#warning_header',languages[g_language]['news_sender_agencies_lack_infos'],0,1000);
                                                        }
                                            })
                                }
                            else
                                {
                                    $.ui.unblockUI();
                                    generate_captcha("","","#news_sender_captcha_span");
                                    $.selectBox.getOldSelects('news_sender_wrapper_div');
                                    $('#news_sender_send_btn').on('click',send_news);
                                    $('#news_sender_add_picture_btn').on('click',function(){upload_news_media('news_pic',"");});
                                    $('#news_sender_add_video_btn').on('click',function(){upload_news_media('news_video',"");});
                                }
                        },
            error : function(error)
                        {
                            alert(error);
                            alert(error.responseText);
                        }
           });
}