//This function will switch the "mode" parameter value and show suitable aside items
//Finished
function control_aside_as(mode)
    {
        switch(mode)
            {
                case 'admin' :
                    {
                        $('#aside_new_account_link').hide();
                        $('#aside_account_link').show();
                        $('#aside_settings_link').show();
                        $('#aside_sendnews_link').show();
                        $('#aside_auditnews_link').show();
                        break;
                    }
                case 'user' :
                    {
                        $('#aside_new_account_link').hide();
                        $('#aside_account_link').show();
                        $('#aside_settings_link').show();
                        $('#aside_sendnews_link').show();
                        $('#aside_auditnews_link').hide();
                        break;
                    }
                case 'guest' :
                    {
                        $('#aside_new_account_link').show();
                        $('#aside_account_link').hide();
                        $('#aside_settings_link').hide();
                        $('#aside_sendnews_link').hide();
                        $('#aside_auditnews_link').hide();
                        break;
                    }
            }
    }

//This function will share application if clicked when we are not in "news details" page
//And share news title with link when we are inside "news details" page using the function "share_bit_ly_url"
//Finished
function share()
    {
        if(g_current_page === 'news_details')
            {
                var news_id = $('#current_news_id_txt').val();
                analytics.trackEvent('Category', 'Action', 'Share News', news_id);
                share_bit_ly_url(news_id);
            }
        else
            {
                var title = ' http://CyberAman.com : \r\n';
                var message = "شبكة إخبارية اجتماعية \r\n";
                message += "يمكنكم الحصول على آخر الأخبار والأخبار العاجلة عبر تطبيقها : \r\n";
                message += "https://play.google.com/store/apps/details?id=com.cyberaman.codnloc";
                window.plugins.socialsharing.share(message, title,null,'');
            }
    }       
        
function share_bit_ly_url(news_id)
//Finished
    {
        var title = " ⛔ CyberAman.com : \r\n ";
        title += " ⚓ " + $('.news_details_title_div').text()+" ⚓ \r\n  \r\n";
        var link_title = $('.news_details_title_div').text().substr(40);
        $.getJSON('http://api.bitly.com/v3/shorten?callback=?',
                  {format: "json",apiKey: "R_74464bacd2194f3383f3f714a523c42e",login: "o_d7d32ro76",longUrl: g_web_link + "Prog-" + g_current_program + "_ns-details_idnews-" + news_id + "_title-" + link_title + "_Lang-" + g_language + "_nl-1.pt"},
                  function(response)
                    {
                        var response_link = response.data.url;
                        title += response_link;
                        window.plugins.socialsharing.share(null, title,null);
                    }
                );
    }

//This function will reload current page
//
function refresh()
    {
        switch(g_current_page)
            {
                case 'news':
                    {
                        get_news();
                    }
                case 'news_details':
                    {
                        var news_id = "";
                        if($('#current_news_id_txt').length > 0)
                            {
                                news_id = $('#current_news_id_txt').val();
                            }
                        else
                            {
                                news_id = localStorage.get("news_id");
                            }
                        get_news_details_by_id(news_id);
                    }
                case 'category':
                    {
                        var cat_id = $('#current_cat_id_txt').val();
                        get_category_by_id(cat_id);
                    }
            }
    }

//This function will be executed when the title is clicked
//
function title_clicked()
    {
        switch(g_current_page)
            {
                case 'signup':
                    {
                        break;
                    }
                case 'login':
                    {
                        break;
                    }
                case 'news_sender':
                    {
                        break;
                    }
                case 'reporters':
                    {
                        break;
                    }
                case 'settings':
                    {
                        break;
                    }
                case 'account':
                    {
                        break;
                    }
                case 'author_news':
                    {
                        break;
                    }
                case 'contact_us':
                    {
                        break;
                    }
                case 'audit_news':
                    {
                        break;
                    }
                case 'news' || 'last_news':
                    {
                        break;
                    }
                case 'category':
                    {
                        break;
                    }
                case 'news_details':
                    {
                        break;
                    }
            }
    }

//This function will fetch the array "data_countries_zip_code" and fill the "select_element" jquery object parameter
//
function get_all_zip_codes(select_element)
    {
        var zip_code_options = '';
        $.each(data_countries_zip_codes,function(index,element)
            {
                if(element === '+961')
                    zip_code_options += '<option value="' + element + '" selected="selected">' + element + '</option>';
                else
                    zip_code_options += '<option value="' + element + '">' + element + '</option>';
            })
        select_element.append(zip_code_options);
    }

//This function will apply regular expression test on the value "input_element" to test if it is a valid email format value or not
//Need Review
function is_valid_email_format(input_element)
    {
        var email = $(input_element).val();
        var pattern = new RegExp(/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i);
        return pattern.test(email);
    }

//This function will be used to validate the value of jquery input element and return a boolean value
//This function may also validate value if it is number or not
//Need Review
function validate_input(input_element,as_number)
    {
        if($(input_element).length <= 0 || $(input_element).val() === "" || typeof $(input_element) === null)
            return false;
        else
            {
                if(as_number === 1)
                    return !isNaN($(input_element).val());
                else
                    return true;
            }
    }

//This function will be used to send a value as user name to server and test it if is a valid user name then when result is returned
//A callback function is executed
//This function will have multiple behaviors once on registration once on login
//Need review
function validate_user_name(input_element,callback)
{
    var user_name = $(input_element).val();
    if(typeof user_name === null || user_name === "")
        {
            return false;
        }
    else
        {            
            var i = {};
            i['u'] = user_name;
            var data = {op : 'vu',i : i};
            $.ajax({
                        url : g_api_link,
                        data : data,
                        type : 'POST',
                        dataType : 'json',
                        error : function(){},
                        success : callback
                    });
        }
}

// This function will be used to tell device to open gallery to select an image to upload to server
// And when the file is selected a callback is executed to upload selected file
// This function will  get parameters to tell the callback function where it must upload the selected file when callback is executed
// Need Review
function upload(type,location,hidden_input,upl_success_callback,upl_error_callback,error_callback)
	{
            // Retrieve image file location from specified source
            if(type === 'news_pic' || type === 'user_pic')
                {
                    var params = { quality: 50,destinationType: navigator.camera.DestinationType.FILE_URI,sourceType: navigator.camera.PictureSourceType.PHOTOLIBRARY };
                }
            else
                {
                    var params = { quality: 50,destinationType: navigator.camera.DestinationType.FILE_URI,sourceType: navigator.camera.PictureSourceType.PHOTOLIBRARY,mediaType : navigator.camera.fileMediaType.ALLMEDIA};
                }
            navigator.camera.getPicture(function(imageURI){upload_to(type,location,hidden_input,imageURI,upl_success_callback,upl_error_callback)},error_callback,params);
	}

// This function will  be executed as the callback of the function "upload" to upload the selected file to server
// This function will get the type parameter wich is passed from the upload function to tell the server where we want to upload this file
// When this function is executed and a result is returned the function "upload_succeed" in "callbacks.js" will be executed
// Need Review
function upload_to(type,location,hidden_input,imageURI,success_callback,error_callback)
    {
        window.resolveLocalFileSystemURL(imageURI, function(entry){
                var file_name = entry.toURL();
                var upload_location = "";
                var ft = new FileTransfer();
                var options = new FileUploadOptions();
                options.fileKey="upl";
                options.params = {};
                switch(type)
                    {
                        case 'news_pic':
                            {
                                upload_location = encodeURI(g_web_link + "Programs/api/upload_news_pic.php");
                                options.fileName=file_name.substr(file_name.lastIndexOf('/')+1);
                                options.mimeType="text/plain";
                                break;
                            }
                        case 'news_video':
                            {
                                upload_location = encodeURI(g_web_link + "Programs/api/upload_news_video.php");
                                options.fileName=file_name.substr(file_name.lastIndexOf('/')+1);
                                options.chunkedMode = true;
                                break;
                            }
                        case 'user_pic':
                            {
                                upload_location = encodeURI(g_web_link + "Programs/api/upload_user_pic.php?u=" + device.uuid);
                                options.fileName=file_name.substr(file_name.lastIndexOf('/')+1);
                                options.mimeType="text/plain";
                                break;
                            }
                    }
                options.params.fileName = options.fileName ;

                ft.upload(imageURI, upload_location, function(r){upload_succeed(r,type,location,hidden_input,success_callback,error_callback)}, function(error){warn_user(location,error.code,0,4000);}, options);
                }, function(e){



            });
    }

// This function will fetch an array of object "data_countries_iso_codes" suitable with current language
// and fill the jquery select element object with fecthed data
// Need Review
function get_all_countries(select_element)
    {
        var country_option = '';
        $.each(data_countries_iso_codes[g_language],
                function(index,element)
                    {
                        if(index === "LB")
                            country_option += '<option value="' + index + '" selected="selected">' + element + '</option>';
                        else
                            country_option += '<option value="' + index + '">' + element + '</option>';
                    }
              );
        select_element.append(country_option);
    }

// This function will send data to be registered
// When response is returned from server a functiion named "handle_registration_response" in "callbacks.js" will be called
// Need Review
function register_me()
    {
        var warning_target = '#warning_header';
        if(!validate_input('#reg_nickname_txt',0))
            {
                warn_user(warning_target,languages[g_language]["reg_enter_nickname"],0,2000);
                return false;
            }
        if(!validate_input('#reg_password_txt',0))
            {
                warn_user(warning_target,languages[g_language]["reg_enter_password"],0,2000);
                return false;
            }
        if(!validate_input('#reg_country_sel',0))
            {
                warn_user(warning_target,languages[g_language]["reg_choose_country"],0,2000);
                return false;
            }
        if(!validate_input('#reg_fullname_txt',0))
            {
                warn_user(warning_target,languages[g_language]["reg_enter_fullname"],0,2000);
                return false;
            }
        if(!validate_input('#reg_email_txt',0))
            {
                warn_user(warning_target,languages[g_language]["reg_enter_email"],0,2000);
                return false;
            }
        else
            {
                if(!is_valid_email_format('#reg_email_txt'))
                    {
                        warn_user(warning_target,languages[g_language]["reg_enter_valid_email"],0,2000);
                        return false;
                    }
            }
        if(!validate_input('#reg_phone_txt',1))
            {
                warn_user(warning_target,languages[g_language]["reg_enter_phone"],0,2000);
                return false;
            }
        var data = {};
        data['op'] = 's';
        data['i'] = {};
        data['i']['n'] = $('#reg_nickname_txt').val();//This will be the nickname of user (user name)
        data['i']['p'] = $('#reg_password_txt').val();//This will  be the password of user
        data['i']['g'] = "m";//This will be the gender of user
        data['i']['c'] = $('#reg_country_sel').val();//This will be the country of user
        data['i']['f'] = $('#reg_fullname_txt').val();//This will be the full name of the user
        data['i']['e'] = $('#reg_email_txt').val();//This will be the email of the user
        data['i']['ph'] = $('#reg_zip_sel').val() + $('#reg_phone_txt').val();//This will be the phone number of the user zip + number
        data['i']['u'] = device.uuid;//This will be the device uuid of the user
        data['i']['a'] = "";//This will be android id (empty while registration)
        data['i']['ap'] = "";//This will  be apple id (empty while registration)

        warn_user('#warning_header',languages[g_language]['reg_sending_data'] + '<img src="images/loading1.gif" style="vertical-align:medium;width:20px;" />',1,0);
        var request = $.ajax({
                                url : g_api_link,
                                data : data,
                                dataType : "json",
                                type : "POST",
                                success : function(data){handle_registration_response(data,warning_target);}, //Success handler that will handler resgistration response from server
                                error : function(data){warn_user('#warning_header',languages[g_language]['reg_registration_failure'],0,5000);console.log(data.responseText);}
                            });
    }

// This function will be used to send data to server to test the user name password pair if is valid or not
// Need Review
function login_me()
    {
        var warning_target = '#login_footer';
        if(!validate_input($('#login_nickname_txt'),0))
            {
                warn_user(warning_target,languages[g_language]["login_enter_nickname"],0,10000);
                return false;
            }
        if(!validate_input($('#login_password_txt'),0))
            {
                warn_user(warning_target,languages[g_language]["login_enter_password"],0,10000);
                return false;
            }
        var data = {};
        data['op'] = 'l';
        data['i'] = {};
        data['i']['u'] = $('#login_enter_nickname').val();//This will be user name
        data['i']['p'] = $('#login_enter_password').val();//This will be user password
        data['i']['d'] = device.uuid;
        $(warning_target).html(languages[g_language]['login_sending_data'] + '<img src="images/loading1.gif" style="vertical-align:medium;width:20px;" /> ');
        var request = $.ajax({
                                url : g_api_link,
                                data : data,
                                dataType : "json",
                                type : "POST",
                                success : function(data){handle_login_response(data,warning_target);}, //success handler that will parse server response and take suitable actions if infos are valid or warn user if invalid infos
                                error : function(data){warn_user('#login_footer',languages[g_language]['login_authentication_error'],0,10000);}
                             });
    }

// This function will be used to get news from server then send them to a callback to fill page
// This function will work due current settings
// Need Review
function get_news()
    {
        if(g_last_news === '0')
            {
                analytics.trackView('News By Category');
                g_current_page = "news";
            }
        else
            {
                analytics.trackView('Last News');
                g_current_page = "last_news";
            }
            
        start_loading();
        
        var i = {};
        i['l'] = 'Arabic';
        i['t'] = g_token;
        i['o'] = 'desc';
        i['li'] = g_news_per_page;
        i['c'] = '';
        i['ln'] = g_last_news;
        i['u'] = g_just_urgent;
        
        var data = {op : 'g',i : i};
        var request = $.ajax({
                              url : g_api_link,
                              data : data,
                              dataType : 'json',
                              type: 'POST',
                              timeout : 10000,
                              success : function(data)
                                {
                                    news_has_been_loaded(data,'#news_unlogged_in_wrapper_div ul',0,'append');//success handler that will handle getted news and insert them to DOM
                                },
                              error : function(data)
                                {
                                    //console.log(web_link + "Programs/api/api.php");
                                    stop_loading();
                                    console.log(data.responseText);
                                    navigator.notification.alert(
                                            'لا يمكن إحضار الأخبار حاليا قد تكون المشكلة من إتصالك بالإنترنت ، إذا استمرت هذه الرسالة بالظهور ننصحك بالذهاب إلى \n \n الإعدادات > التطبيقات > CyberAman > قم بمحو البيانات',  // message
                                            function(){},         // callback
                                            'تنبيه',            // title
                                            'حسناً'                  // buttonName
                                        );

                                    //alert('لا يمكن إحضار الأخبار حاليا قد تكون المشكلة من إتصالك بالإنترنت ، إذا استمرت هذه الرسالة بالظهور ننصحك بالذهاب إلى \n \n الإعدادات > التطبيقات > CyberAman > قم بمحو البيانات');
                                    return false;
                                }
                             });
    }



// Warn user by showing message inside specific "input_element" using "msg" parameter
// You can decide also if message it will persists or no using boolean value for parameter "persists"
// When you decide that message will not persists so set timeout of hiding message in seconds using parameter "timeout"
//Finished
function warn_user(input_element,msg,persist,timeout)
    {
        $(input_element).show();
        if(typeof timeout === null || timeout === "" || timeout === null)
            timeout = 5000;
        if(persist === 1)
            $(input_element).html(msg);
        else
            {
                $(input_element).html(msg).show();
                setTimeout(function(){$(input_element).hide();},timeout);
            }
    }
    
function get_countries()
    {
        //control_aside_as('admin');
        get_all_countries($('#countries'));
    }

// This function it will be as a function to execute a callback function and pass parameters to this function
// Finished
function call_function(fn_name,data)
    {
        fn_name(data);
    }

String.prototype.replaceArray = function(find, replace) {
  var replaceString = this;
  var regex; 
  for (var i = 0; i < find.length; i++) {
    regex = new RegExp(find[i], "g");
    replaceString = replaceString.replace(regex, replace[i]);
  }
  return replaceString;
};

function start_loading()
    {
        $('.afScrollPanel').css({'background-image' : 'url(images/loading1.gif)','background-repeat' : 'no-repeat','background-position' : 'center'});
    }
    
function stop_loading()
    {
        $('.afScrollPanel').css('background-image' , 'none');
    }
    
function register_for_notifications()
    {
        var pushNotification = window.plugins.pushNotification;
        pushNotification.register(successHandler, errorHandler,{"senderID":"4350835736","ecb":"onNotificationGCM"});
    }
    
function exit_application()
    {
        navigator.app.exitApp();
    }