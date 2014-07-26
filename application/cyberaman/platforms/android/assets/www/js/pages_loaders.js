/*function open_registration_page()
    {
        if(g_current_page !== 'signup')
            {
                g_current_page = 'signup';
                $.ui.loadContent("register.html",false,false,"slide",true);
            }
    }*/

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
        
function upload_user_pic()
{
    warn_user('#warning_header','<img src="images/loading1.gif" />',1,10000);
    upload('user_pic',
           '',
           '',
           function(r)
                {
                    if(r.s === '1')
                        {
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

function open_reg_page()
    {    
        g_current_page = "signup";
        //analytics.trackView('Sign Up');
        $('#reg_btn').off('click');
        $('#reg_btn').on('click',register_me);
        $('#reg_upload_btn').off('click');
        $('#reg_upload_btn').on('click',upload_user_pic);
        reg_fill_selects();
        $.selectBox.getOldSelects('reg_wrapper_div');
    }
    
    
function open_about_page()
    {
        g_current_page = "about";
        //analytics.trackView('About Us');
    }