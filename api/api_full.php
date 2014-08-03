<?php
header("Access-Control-Allow-Origin: *");
include_once("../../includes.php");
include_once("../../includes/phpmailer/class.phpmailer.php");
include_once("../../includes/checkValidity.php");
include_once("../../languages/lang-Arabic.php");
include_once("Languages/lang-Arabic.php");
include_once("../../includes/session.php");
include_once("functions.php");

global $method;

if(isset($_REQUEST['method']) && in_array($_REQUEST['method'],array('GET','POST')))
    {
        $method = ($_REQUEST['method']) === 'GET' ? $_GET : $_POST;
    }
else
    {
        $method = $_POST;
    }

//header('Content-Type: text/html; charset=utf-8');
if (!isset($project)){header("location: ../../");}

$result = parse_request();
$response = parse_response($result);
echo $response;
$response = json_decode($response);
function parse_request()
    {
        global $method;
        $operation = isset($method['op']) && !empty($method['op'])?InputFilter($method['op']) : "";
        switch($operation)
            {
                case 'a':
                    {
                        if(isset($method['i'])) $result_json = audit_news($method['i']);
                        else $result_json = json_encode(array("s" => '001'));
                        break;
                    }
                case 'c':
                    {
                        if(isset($method['i'])) $result_json = contact_us($method['i']);
                        else $result_json = json_encode(array("s" => '001'));
                        break;
                    }
                case 'd':
                    {
                        if(isset($method['i'])) $result_json = get_news_details($method['i']);
                        else $result_json = get_news_details(array(""));
                        break;
                    }
                case 'g':
                    {
                        if(isset($method['i'])) $result_json = get_news($method['i']);
                        else $result_json = get_news(array(""));
                        break;
                    }
                case 'ga':
                    {
                        if(isset($method['i'])) $result_json = get_about ($method['i']);
                        else $result_json = get_about (array(""));
                        break;
                    }
                case 'g_a_a':
                    {
                        if(isset($method['i'])) $result_json = get_all_agencies($method['i']);
                        else $result_json = json_encode(array("s" => '001'));
                        break;
                    }
                case 'g_a_c':
                    {
                        $result_json = get_all_categories();
                        break;
                    }
                case 'g_a_d':
                    {
                        if(isset($method['i'])) $result_json = get_all_departments($method['i']);
                        else $result_json = get_all_departments (array("l" => "Arabic"));
                        break;
                    }
                case 'g_l_r':
                    {
                        $result_json = get_last_reporters();
                        break;
                    }
                case 'g_u_i':
                    {
                        if(isset($method['i'])) $result_json = get_user_infos($method['i']);
                        else $result_json = json_encode(array("s" => '1',"i" => "-1"));
                        break;
                    }
                case 'gs':
                    {
                        if(isset($method['i'])) $result_json = get_settings($method['i']);
                        else $result_json = json_encode(array("s" => '001'));
                        break;
                    }
                case 'gu':
                    {
                        if(isset($method['i'])) $result_json = get_unapprooved_news ($method['i']);
                        else $result_json = json_encode(array("s" => '001'));
                        break;
                    }
                case 'l':
                    {
                        if(isset($method['i'])) $result_json = sign_in($method['i']);
                        else $result_json = json_encode(array("s" => '001'));
                        break;
                    }
                case 'm':
                    {
                        if(isset($method['i'])) $result_json = get_more_news($method['i']);
                        else $result_json = get_more_news(array(""));
                        break;
                    }
                case 's':
                    {
                        if(isset($method['i'])) $result_json = sign_up($method['i']);
                        else $result_json = json_encode(array("s" => '001'));
                        break;
                    }
                case 'sn':
                    {
                        if(isset($method['i'])) $result_json = send_news($method['i']);
                        else $result_json = json_encode(array("s" => '001'));
                        break;
                    }
                case 's_n_i':
                    {
                        if(isset($method['i'])) $result_json = set_user_google_registration_id($method['i']);
                        else $result_json = json_encode(array("s" => '001'));
                        break;
                    }
                case 's_u_i':
                    {
                        if(isset($method['i'])) $result_json = set_user_infos($method['i']);
                        else $result_json = json_encode(array("s" => '001'));
                        break;
                    }
                case 'ss':
                    {
                        if(isset($method['i'])) $result_json = set_settings($method['i']);
                        else $result_json = set_settings(array(""));
                        break;
                    }
                case 'v':
                    {
                        if(isset($method['i'])) $result_json = is_valid_token($method['i']);
                        else $result_json = json_encode(array("s" => '001'));
                        break;
                    }
                case 'vu':
                    {
                        if(isset($method['i'])) $result_json = is_valid_user_name($method['i']);
                        else $result_json = json_encode(array("s" => '001'));
                        break;
                    }
                default :
                    {
                        $result_json = json_encode(array("s" => '000'));
                        break;
                    }
            }
        $result = json_decode($result_json);
        return $result;
    }
    
function parse_response($result)
{
    if(is_object($result))
        {
            $returned_array = array();
            foreach(get_object_vars($result) as $property_name => $property_value)
                {
                    $returned_array[$property_name] = $property_value;
                }
            return json_encode($returned_array);
        }
}

// Function may be used to get all categories in a form of collection of object each object contain 2 indexes i and n
// index i point to the category id
// index n point to the category name
function get_all_categories($categories_infos = array("l"))
    {
        if(isset($categories_infos['l']))
            {
                $passed_language = InputFilter($categories_infos['l']);
                $language = $db->get_var("select IdLang from languages where LangName = '$passed_language'") ? $passed_language : "Arabic";
            }
        else
            {
                $language = "Arabic";
            }
            
        global $db;
        if(empty($db))
            $db = new db();
        
        $all_categories_res = $db->get_results("select IdCat as cat_id,CatName as cat_name from languages as l,catlang as cl where l.IdLang = cl.IdLang and l.LangName = '$language' order by `sort`");
        if(count($all_categories_res) > 0)
            {
                $all_categories = array("s" => '1',"r" => array());
                $index = 0;
                foreach($all_categories_res as $category_row)
                    {
                        $all_categories['r'][$index] = array("i" => $category_row->cat_id,"n" => $category_row->cat_name);
                        $index ++;
                    }
                return json_encode($all_categories);
            }
        else
            {
                return json_encode(array("s" => '001'));
            }
    }

    // Function may be used to get all agencies in a form of collection of object each object contain 2 indexes i and n
// index i point to the agency id
// index n point to the agency name
function get_all_agencies($user_infos = array("t"))
    {      
        if(!isset($user_infos['t']))
            {
                return json_encode(array('s' => '001'));
            }
            
        global $db;
        if(empty($db))
            $db = new db();
        
        $passed_token = InputFilter($user_infos['t']);
        
        $is_valid_token_row = $db->get_row("select UserId,concat(UserName,' ',FamName) as full_name,NickName from users where app_token = '$passed_token'");
        
        if(!$is_valid_token_row)
            {
                return json_encode(array('s' => '002'));
            }
        
        $user_id = $is_valid_token_row->UserId;
        
        $is_admin_var = $db->get_var("select AdminId from admins where AdminId = '$user_id'");
        
        if(!$is_admin_var)
            {
                return json_encode(array('s' => '003'));
            }
        
        $all_agencies = array("s" => '1',"r" => array());
        
        $all_agencies['r'][0] = array("i" => $is_valid_token_row->NickName,"n" => $is_valid_token_row->full_name);
        
        $all_agencies_res = $db->get_results("select concat(UserName,' ',FamName) as full_name,NickName from users as u,groups as g where g.GroupId = u.GroupId and g.GroupName = 'agencies' ORDER BY FIELD(UserId, '20140000018')desc ;");
        if(count($all_agencies_res) > 0)
            {
                $index = 1;
                foreach($all_agencies_res as $agency_row)
                    {
                        $all_agencies['r'][$index] = array("i" => $agency_row->NickName,"n" => $agency_row->full_name);
                        $index ++;
                    }
                return json_encode($all_agencies);
            }
        else
            {
                return json_encode($all_agencies);
            }
    }
    
/// Function that will validate passed token and return a json object
// @param t : Token to validate
// @param u : Generate new token and update it on users table(optional)
// @param g : Gather user infos and return infos in index i(optiona)
// with index s which contains validation status and t which contains token in the case of token validity
function is_valid_token($user_infos = array("t","u","g"))
    {
        global $db;
        if(empty($db))
            $db = new db();
        
        if(!isset($user_infos['t']))
            return(json_encode(array("s" => '001')));
        else
            {
                $passed_token = InputFilter($user_infos['t']);
                $is_valid_token_row = $db->get_row("select CellNbr as cell_number,Contry as user_country,app_token,UserId as user_id,NickName as nick_name,UserPic as user_pic,Points as user_points,UserMail as email,concat(UserName,' ',FamName) as full_name from users where app_token = '$passed_token'");
                if($is_valid_token_row)
                    {
                        $user_id = $is_valid_token_row->user_id;
                        $nick_name = $is_valid_token_row->nick_name;
                        $email = $is_valid_token_row->email;
                        $old_token = $is_valid_token_row->app_token;
                        $is_admin_var = $db->get_var("select AdminId from admins where AdminId = '$user_id'");
                        $is_admin = $is_admin_var ? '1' : '0';
                        if(isset($user_infos['g']))
                            {
                                $cell_number = $is_valid_token_row->cell_number;
                                $full_name = $is_valid_token_row->full_name;
                                $user_pic = $is_valid_token_row->user_pic;
                                $user_points = $is_valid_token_row->user_points;
                                $user_country = $is_valid_token_row->user_country;
                                $infos_arr = array("c" => $cell_number,"co" => $user_country,"e" => $email,"f" => $full_name,"i" => $user_id,"n" => $nick_name,"p" => $user_pic,"pt" => $user_points);
                            }
                        else
                            {
                                $infos_arr = "-1";
                            }
                        if(isset($user_infos['u']))
                            {
                                $new_token = md5($nick_name . time().strrev($email));
                                $new_token_qu = $db->query("update users set app_token = '$new_token' where UserId = '$user_id'");
                                return(json_encode(array("s" => '1',"t" => $new_token,"u" => $user_id,"p" => $is_admin,"i" => $infos_arr)));
                            }
                        else
                            {
                                return(json_encode(array("s" => '1',"t" => $old_token,"u" => $user_id,"p" => $is_admin,"i" => $infos_arr)));
                            }
                    }
                else
                    return(json_encode(array("s" => '002')));
            }
    }
/**
 * Register a new user and enter the passed parameters as his infos and return 1 as index s and token as index t on success registration
 * or return status_error as index s on failure
 *  
 * @author Mouhammad Zein Eddine <mohammad@phptransformer.com>
 * @param associative_array $user_infos contain following indexes
    - String 'n' provide a unique nickname for the registered user(actually nickname will be the phone number)
    - String 'p' provide a password to the registered user
    - String 'g' provide a gender to the registered user
    - String 'c' provide a country to the registered user
    - String 'f' provide the full name of the registered user
    - String 'e' provide the email of registered user
    - String 'u' provide the uuid of movile device
    - String 'a' provide the android google id of mobile device
    - String 'ap' provide the apple notification id of mobile device
 * 
 * @return json return a jsonified array that will present the success of the registration or its failure by the index 's'
 * with the apptoken in case of success by the index 't'
 */
function sign_up($user_infos = array("n" => "","p" => "","g" => "","c" => "","f" => "","e" => "","ph" => "","u" => "","a" => "","ap" => ""))
{
    global $db,$GeoIpService,$IpNbr,$AdminRegOk,$AdminMail, $WebSiteName;
    
    if(empty($db)){$db = new db();}
    /*
     * Test if any of the provided infos are empty or not valid infos
     * If their were any invalid or empty infos return jsonified array representing status error 001 and an internal array of 0,1 representing each infos success status
     */
        
    //define jsonified arrays that will be returned in different cases of errors
    $empty_infos_json = json_encode(array("s" => '001'));
    $already_error_json = json_encode(array("s" => '002'));
    $invalid_infos_form_json = json_encode(array("s" => '003'));
    $malformed_error_json = json_encode(array("s" => '004'));
    $unkown_error_json = json_encode(array("s" => '005'));
    
    //create an array that will contain our indexes to prevent sending array with any other infos
    $valid_indexes_arr = array("n","p","g","c","f","e","ph","u","a","ap");
    $optional_indexes_arr = array("g","u","a","ap");
    
    // Propose that all infos are valid
    $infos_validity_arr = array("n" => 1,"p" => 1,"g" => 1,"c" => 1,"f" => 1,"e" => 1,"ph" => 1,"u" => 1,"a" => 1,"ap" => 1);
    
    //if the user infos is empty or not a valid array or its count is not equal to our supposed array
    if(empty($user_infos) || !is_array($user_infos) || count($user_infos) <= 0 || count($user_infos) > count($valid_indexes_arr))
        {return($invalid_infos_form_json);}
    
    //test each info validity
    $index = 0;
    foreach($user_infos as $info_name => $info_value)
        {
            if(!in_array($info_name,$valid_indexes_arr))
                {return($malformed_error_json);}
                
            // If it is not valid set the convenable index to 0
            if(!in_array($info_name, $optional_indexes_arr) && (empty($info_value) || strlen(trim($info_value)) == 0))
                {$infos_validity_arr[$info_name] = 0;}
            
            //If tested info is the email field and it is not already detected as invalid infos in the test above
            if($info_name == "e" && $infos_validity_arr[$info_name] == 1)
                {
                    // If it is not valid email set convenable index to 0
                    if(!check_email_address($info_value)){$infos_validity_arr[$info_name] = 0;}
                }
            $index ++;
        }
     // if error infos array contain any 0 in its element return a jsonified array with status 001 and an array of infos statuses
     foreach($infos_validity_arr as $info => $validity)
        {
            if(empty($validity) || $validity == 0)
                {return(json_encode(array("s" => '001',"v" => $infos_validity_arr)));}
        }
    
    /*Select from database for a user that have look like nick_name or email
     *If their were any already registered user with these infos return a jsonified array representing status error 002 as status to tell user to choose another infos
     */
    global $nick_name;
    $nick_name = InputFilter($user_infos['n']);
    $email = InputFilter($user_infos['e']);
    
    $already_user_var = $db->get_var("select UserId from users where `NickName` = '$nick_name' or `UserMail` = '$email'");
    if($already_user_var)
        {
            return($already_error_json);
        }
    
    $user_id = GenerateID('users', 'UserId');
    $group_id = '20070000001';
    $time_format = 'Y-m-d H:i:s';
    $full_name_arr = explode(' ',InputFilter($user_infos['f']),2);
    $user_name = $full_name_arr[0];
    $parent_name = "";
    $family_name = count($full_name_arr) > 1?end($full_name_arr):"";
    $birth_date = "";
    $gmt = '+2';//
    $gender = $user_infos['g'] == 1?1 : 0;
    $country = isset($user_infos['c'])?InputFilter($user_infos['c']) : get_country();
    $phone_number = "";
    $cell_number = $user_infos['ph'];
    $password = md5($user_infos['p']);
    $last_login = date("Y-m-d H:i:s");
    $last_ip = get_client_ip();
    $hobies = '';
    $job = '';
    $education = '';
    $pref_lang = 'Arabic';//
    $pref_time = '08:00-16:00';
    $cookie_life = 8640;
    $user_pic = "";//
    $user_site = "";//
    $banned = 0;
    $pref_theme = "Default";
    $user_sign = '';
    $points = 1;
    $active = $AdminRegOk == '1'?'0' : '1';
    $reg_date = date("Y-m-d H:i:s");
    $allow_html = 1;
    $allow_html = "0";
    $allow_bb_code = "0";
    $allow_smiles = "0";
    $allow_avatar = "1";
    $confirm_code = md5(date("Y-m-d") . $user_id . rand(1, 9999999999));
    $mailed = 0;
    $deleted = 0;
    $last_session = 0;
    $android_id = $user_infos['a'];
    $apple_id = $user_infos['ap'];
    $uuid = $user_infos['u'];
    $app_token = md5($nick_name . time().strrev($email));
    
    
    /* Insert provided infos to table users and get the result of insertion operation
     * If result is success then return jsonified array with status error 1 and app_token = md5($nick_name . time().str_rev($email)
     * Else if result is failure then return jsonified array with status error 003 that will tell user that an unkown error has been occured
     */
    
    $register_user_sql = "insert into users(`UserId`,`GroupId`,`TimeFormat`,`UserName`,`NickName`,`ParentName`,`FamName`,`BirthDate`,`Sex`,`GMT`,`Contry`,`PhoneNbr`,`CellNbr`,`PassWord`,`LastLogin`,`LastIP`,`Hobies`,`Job`,`Education`,`PrefLang`,`PrefTime`,`CookieLife`,`UserPic`,`UserMail`,`UserSite`,`Banned`,`PrefThem`,`UserSign`,`Points`,`Active`,`RegDate`,`allowHtml`,`allowBBcode`,`allowSmiles`,`allowAvatar`,`ConfirmCode`,`Mailed`,`Deleted`,`LastSession`,`android_id`,`apple_id`,`uuid`,`app_token`)"
            . "values"
            . "('$user_id','$group_id','$time_format','$user_name','$nick_name','$parent_name','$family_name','$birth_date','$gender','$gmt','$country','','$cell_number','$password','$last_login','$last_ip','$hobies','$job','$education','$pref_lang','$pref_time','$cookie_life','$user_pic','$email','$user_site',0,'$pref_theme','$user_sign','$points','$active','$reg_date','$allow_html','$allow_bb_code','$allow_smiles','$allow_avatar','$confirm_code','$mailed','$deleted','$last_session','$android_id','$apple_id','$uuid','$app_token')";
    
    $register_user_qu = $db->query($register_user_sql);
    
    if($AdminRegOk != "1")
        {
            //mkdir('../../uploads/users/'.$nick_name."/",0755,true);
            $url_variables = array("Prog", "acnt", "actvcode", "user");
            $url_values = array("account", "activate", $confirm_code, $nick_name);
            
            $activation_link = "";//CreateLink("", $url_variables, $url_values);
            $host = $_SERVER['HTTP_HOST'];
            $uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
            $from = $AdminMail;
            $from_name = $WebSiteName;
            $add_address[0] = $email;
            $add_address[1] = $nick_name;
            $subject = (api_email_subject_new_user) . $WebSiteName;
            $body = '<div dir="' . (DirHtml) . '" >' . (DearMr) . $user_name . " " . $family_name . "<br/>" . (api_email_body_new_user) . '<br><a href="' . $activation_link . '" target="_blank">' . $activation_link . '<a/>' . "</div>" . (EmailSignature);
            SendEmail($from, $from_name, $add_address, $subject, $body);
            mobile_login($nick_name);
            //LoginAsUser ($nick_name);
        }
    else
        {
            mobile_login($nick_name);
            //LoginAsUser ($nick_name);
        }
    if(file_exists("../../uploads/users/".$uuid."/"))
        {
            @rename("../../uploads/users/".$uuid."/", "../../uploads/users/".$nick_name);
        }
    $user_pictures = glob("../../uploads/users/$nick_name/avatar*");
    if(is_array($user_pictures))
        {
            global $current_extension;
            $current_extension = "";
            array_walk($user_pictures, function ($user_picture)
                                            {
                                                global $db,$nick_name;
                                                $extension_arr = explode('.',$user_picture);
                                                if(is_array($extension_arr) && count($extension_arr) > 1)
                                                    $current_extension = end($extension_arr);
                                                else
                                                    $current_extension = $extension_arr[0];
                                                $db->query("update users set UserPic = 'uploads/users/".$nick_name."/avatar_128.".$current_extension."' where NickName = '$nick_name'");
                                                return 0;
                                            });
        }
    return json_encode(array("s" => 1,"t" => $app_token));
}

/**
 * Login user using user name and password
 * return 1 as index s on success or return status_error as index s on failure
 *  
 * @author Mouhammad Zein Eddine <mohammad@phptransformer.com>
 * @param associative_array $user_infos contain following indexes
    - String 't' provide token for already registered used (in case a token is passed user name and password will not validated and just the token will)
    - String 'u' provide a unique nickname for the registered user(actually nickname will be the phone number)
    - String 'p' provide a password to the registered user
    - String 'd' provide device id to the registered user
 * 
 * @return json return a jsonified array that will present the success of the registration or its failure by the index 's'
 * with the apptoken in case of success by the index 't'
 */
function sign_in($user_infos = array("t" => "","u" => "","p" => "","d" => ""))
{
    global $db;
    
    if(empty($db))
        $db = new db();
        
    $empty_error_json = json_encode(array("s" => '001'));//in case that their were any empty element in the passed array
    $invalid_infos_form_json = json_encode(array("s" => '002'));//in case that array passed is empty or is not array
    $invalid_user_error_json = json_encode(array("s" => '003'));//in case that the provided infos are not valid or user not found
    $malformed_error_json = json_encode(array("s" => '004'));//in case of malformed parameters either not in same order or invalid indexed element used
    
    $valid_indexes_arr = array("t","u","p","d");
    $infos_validity_arr = array("t" => 1,"u" => 1,"p" => 1,"d" => 1);
    
    if(empty($user_infos) || !is_array($user_infos) || count($user_infos) <= 0 || count($user_infos) > count($valid_indexes_arr))
        return($invalid_infos_form_json);
    
    $index = 0;
    foreach($user_infos as $info_name => $info_value)
        {
            if(!in_array($info_name,$valid_indexes_arr))
                return($malformed_error_json);
                
            // If it is not valid set the convenable index to 0
            $index ++;
        }
    if(!isset($user_infos['t']))
        {
            if(!isset($user_infos['u']) || !isset($user_infos['p']) || !isset($user_infos['d']))
                {
                    return json_encode(array("s" => '001',"v" => array("t" => '-1',"u" => "0","p" => "0","d" => "0")));
                }
        }
        
    if(isset($user_infos['t']))
        {
            $passed_device_id = InputFilter($user_infos['d']);
            $user_token = InputFilter($user_infos['t']);
            $already_user_row = $db->get_row("select UserId,UserMail,NickName,app_token from users where `app_token` = '$user_token' and uuid = '$passed_device_id'");
        }
    else if(isset($user_infos['u']) && isset($user_infos['p']) && isset($user_infos['d']))
        {
            $passed_device_id = InputFilter($user_infos['d']);
            $passed_nick_name = InputFilter($user_infos['u']);
            $passed_password = md5(InputFilter($user_infos['p']));
            $already_user_row = $db->get_row("select UserId,UserMail,NickName,app_token from users where NickName = '$passed_nick_name' and Password = '$passed_password'");
        }
    else
        {
            return $malformed_error_json;
        }
    if(is_object($already_user_row))
        {
            $user_id = $already_user_row->UserId;
            $old_token = $already_user_row->app_token;
            $new_token = md5($already_user_row->NickName . time().strrev($already_user_row->UserMail));
            $new_token_qu = $db->query("update users set app_token = '$new_token',uuid = '$passed_device_id' where UserId = '$user_id'");
            $privilege_var = $db->get_var("select AdminId from admins where AdminId = '$user_id'");
            $privilege = $privilege_var ? '1' : '0';
            
            $infos_arr = get_user_infos(array("t" => $new_token));
            /*$is_valid_token_row = $db->get_row("select CellNbr as cell_number,Contry as user_country,app_token,UserId as user_id,NickName as nick_name,UserPic as user_pic,Points as user_points,UserMail as email,concat(UserName,' ',FamName) as full_name from users where app_token = '$new_token'");
            $nick_name = $is_valid_token_row->nick_name;
            $email = $is_valid_token_row->email;
            $cell_number = $is_valid_token_row->cell_number;
            $full_name = $is_valid_token_row->full_name;
            $user_pic = $is_valid_token_row->user_pic;
            $user_points = $is_valid_token_row->user_points;
            $user_country = $is_valid_token_row->user_country;
            $infos_arr = array("c" => $cell_number,"co" => $user_country,"e" => $email,"f" => $full_name,"i" => $user_id,"n" => $nick_name,"p" => $user_pic,"pt" => $user_points);*/
            
            mobile_login($already_user_row->NickName);
            
            return json_encode(array("s" => '1',"t" => $new_token,"p" => $privilege,"i" => $infos_arr));
        }
    else
        return $invalid_user_error_json;
}

function retrieve_news_in_list_details($results_res,$language)
    {
        global $db;
        if(empty($db))
            $db = new db();
        $news = $results_res;
        $news_id = $news->IdNews;
        $news_sql = "select * from languages as l,news as n,newslang as nl where l.IdLang = nl.IdLang and n.IdNews = nl.IdNews and l.LangName = '$language' and n.IdNews = '$news_id'";
        $news_row = $db->get_row($news_sql);
        $news_is_urgent = $news_row->urgent;
        $news_date = $news->Date;
        $news_agency = $news->agency;
        $news_author_id = $news->IdUserName;
        $news_author_name = $db->get_var("select concat(UserName,' ',FamName) from users where UserId = '$news_author_id'");
        $news_pic = $news_row->NewsPic;
        $news_title = $news_row->Tilte;

        $latest_id = $news_id;
        $last_news_date = $news_date;

        $social_date = socialDateDif(strtotime(date("Y-m-d H:i:s")), strtotime($news_date), true);

        $agency_pic_big = $db->get_var("select UserPic from users where NickName = '".$news_agency."'");
        $agency_pic = str_replace(array('128'),array('32'),$agency_pic_big);

        $author_pic_big = $db->get_var("select UserPic from users where NickName = '".$news_author_id."'");
        $author_pic = str_replace(array('128'),array('32'),$author_pic_big);

        $mobile_news_pic = str_replace('.','_320.',$news_pic);
        if(file_exists("../../uploads/news/pics/".$mobile_news_pic))
            {
                $news_pic = $mobile_news_pic;
            }
        return(array("latest_id" => $latest_id,"last_news_date" => $last_news_date,"details" =>  array("type" => "news","id" => $news_id,"pic" => $news_pic ,"title" => $news_title,"date" => $news_date,"social_date" => $social_date,"urgent" => $news_is_urgent,"agency" => $agency_pic,"agency_name" => $news_agency,"author" => $author_pic,"author_id" => $news_author_id,"author_name" => $news_author_name)));
    }
/**
 * Get last n news order by order using language for each category or for specific category if index c found
 * @param String $l Language of retrieved news
 * @param String $t Token of the user
 * @param String $o Order of news
 * @param String $li count of news each time to get (n news = li news)
 * @param String $c category_id the category we want to get the last n news(if omitted all categories will be fetched)
 * @param String $ln get last news or by categories
 * @param String $u urgent news only or all news
 * @param String $a author_id the id of an author we want to fetch all news related to them
 * 
 * @return jsonified_array with index s = 1 and index r that contain all news appropriate to our selection if news found
 * or index s = status_error if no news found or user pass an invalid token
 */
function get_news($categories_infos = array('l'=> '','t' => '','o' => '','li' => '','c' => '','ln' => '','u' => '','a' => ''))
    {
        global $db;
        if(empty($db))
            {$db = new db();}
        
        $no_news_found_error_json = json_encode(array("s" => '001'));//in case of their were no news
        $invalid_token_error_json = json_encode(array("s" => '002'));//in case of invalid token passed
        $invalid_infos_form_json = json_encode(array("s" => '003'));//in case an invalid array passed to this function as $user_infos
        $malformed_error_json = json_encode(array("s" => '004'));//in case of malformed parameters either not in same order or invalid indexed element used
    
        //create an array that will contains our indexes to prevent sending array with any other infos
        $valid_indexes_arr = array("l","t","o","li","c","ln","u","a");
        
        //Create an array that will contains our optional indexes to skip when any of element are empty
        $optional_indexes_arr = array("l","t","o","li","c","ln","u","a");
        
        //Create an array that will contains our default values that will be used when any infos is empty
        $default_values_arr = array("l" => 'Arabic',"t" => "","o" => "desc","li" => 5,"c" => "","ln" => 1,"u" => 0,"a" => "");
    
        // Propose that all infos are valid
        $infos_validity_arr = array("l" => 1,"t" => 1,"o" => 1,"li" => 1,"c" => 1,"ln" => 1,"u" => 1,"a" => 1);
    
        //if the user infos is empty or not a valid array or its count is not equal to our supposed array
        if(empty($categories_infos) || !is_array($categories_infos) || count($categories_infos) <= 0 || count($categories_infos) > count($valid_indexes_arr))
            {
                $categories_infos = $default_values_arr;
            }
        //test each info validity
        $index = 0;
        foreach($categories_infos as $info_name => $info_value)
            {
                if(!in_array($info_name,$valid_indexes_arr))
                    {
                        return($malformed_error_json);
                    }
                
                // If it is not valid set the convenable index to 0
                if(!in_array($info_name, $optional_indexes_arr) && (empty($info_value) || strlen(trim($info_value)) == 0))
                    {
                        $infos_validity_arr[$info_name] = 0;
                    }
                else
                    {
                        if((empty($info_value) || strlen(trim($info_value)) == 0))
                            {
                                $categories_infos[$info_name] = $default_values_arr[$info_name];
                            }
                    }
                $index ++;
            }
        
        $language = isset($categories_infos['l']) && ($db->get_var("select LangName from languages where LangName = '".InputFilter($categories_infos['l'])."'"))? InputFilter($categories_infos['l']) : 'Arabic';
        $order = isset($categories_infos['o']) && in_array(InputFilter($categories_infos['o']),array('asc','desc'))? InputFilter($categories_infos['o']) : 'desc';
        $cat_id = isset($categories_infos['c']) && !empty($categories_infos['c']) && ($db->get_var("select IdCat from catlang where IdCat = '".InputFilter($categories_infos['c']."'")))? InputFilter($categories_infos['c']) : "";// Get news for specific category or for all categories
        $token = isset($categories_infos['t']) ? InputFilter($categories_infos['t']) : "";// Token of user to know if we want to get news suitable to user settings or all news
        $last_news = isset($categories_infos['ln']) && $categories_infos['ln'] == '1' ? 1 : 0;// Get news by last news
        $just_urgent_news = isset($categories_infos['u']) && $categories_infos['u'] == '1' ? 1 : 0;// Get just urgent news
        // If not empty so we are getting news for a specific author
        $author_news = isset($categories_infos['a']) && !empty($categories_infos['a']) && ($db->get_var("select UserId from users where UserId = '".InputFilter($categories_infos['a'])."'")) ? InputFilter($categories_infos['a']) : "";
        $limit = ($last_news == 1)?10 : (!empty($cat_id)?10 : 3);// count of news we want to retrieve each time
        $user_choice = 0;
        $for_days = 4;
        $urgent_sql = "";

        if($last_news == 1)// Get last news
            {
                if($just_urgent_news == 1)//Just get urgent news
                    {
                        $urgent_sql = " and n.urgent = 1";
                    }
                $categories_to_fetch_sql = "select * from news as n,newslang as nl,languages as l where l.IdLang = nl.IdLang and n.IdNews = nl.IdNews and n.Active = 1 and n.Deleted != 1 and  n.`Date` > DATE_SUB(NOW(), INTERVAL " . $for_days . " DAY) $urgent_sql order by n.IdNews desc,n.`Date` desc limit 0,$limit";
            }
        else if(!empty($author_news))// Get news for specific author
            {
                if($just_urgent_news == 1)//Just get urgent news
                    {
                        $urgent_sql = " and n.urgent = 1";
                    }
                $categories_to_fetch_sql = "select * from news as n,newslang as nl,languages as l where l.IdLang = nl.IdLang and n.IdNews = nl.IdNews and n.Active = 1 and n.Deleted != 1 and n.IdUserName = '$author_news'$urgent_sql order by n.IdNews desc,n.`Date` desc limit 0,$limit";
            }
        else if(!empty($cat_id))// Get news for specific category
            {
                if($token == "")
                    {
                        if($just_urgent_news == 1)
                            {
                                $urgent_sql = " and n.urgent = 1";
                            }
                    }
                else
                    {
                        $is_valid_token_var = $db->get_var("select UserId from users where app_token = '$token'");
                        if($is_valid_token_var)
                            {
                                $is_subscribed_cat_var = $db->get_row("select only_urgent from news_subscription where user_id = '$is_valid_token_var' and cat_id = '$cat_id'");
                                if($is_subscribed_cat_var)
                                    {
                                        if($is_subscribed_cat_var === 1)
                                            {
                                                $urgent_sql = " and n.urgent = 1";
                                            }
                                    }
                                else
                                    {
                                        if($just_urgent_news == 1)
                                            {
                                                $urgent_sql = " and n.urgent = 1";
                                            }
                                    }
                            }
                        else
                            {
                                if($just_urgent_news == 1)
                                    {
                                        $urgent_sql = " and n.urgent = 1";
                                    }
                            }
                    }
                $categories_to_fetch_sql = "select cl.IdCat,cl.CatName from languages as l,catlang as cl where l.IdLang = cl.IdLang and l.LangName = '$language' and cl.IdCat = '$cat_id' and cl.Deleted != 1 order by cl.`sort` $order";
            }
        else// Get all news sorted by category
            {
                if(!empty($token))//If their were any token validate token if is valid get categories due user settings
                    {
                        // Query to select all categories
                        $categories_to_fetch_sql = "select cl.IdCat,cl.CatName from languages as l,catlang as cl where l.IdLang = cl.IdLang and l.LangName = '$language' and cl.Deleted != 1 order by cl.`sort` $order";
                        // Validate token
                        $valid_token_var = $db->get_var("select UserId from users where app_token = '$token'");
                        // If is valid token so switch query to a query that will get categories due user settings
                        if($valid_token_var)
                            {
                                // Query to select categories that user is subscribed to
                                $user_categories_sql = "select cl.IdCat,cl.CatName,ns.only_urgent from languages as l,catlang as cl,news_subscription as ns where l.IdLang = cl.IdLang and cl.Deleted != 1 and cl.IdCat = ns.cat_id and l.LangName = '$language' and ns.user_id ='$valid_token_var' order by cl.`sort` $order";
                                // Calculate the count of categories selected
                                $count_of_user_categories_var = $db->get_var("select count(*) from languages as l,catlang as cl,news_subscription as ns where l.IdLang = cl.IdLang and cl.Deleted != 1 and cl.IdCat = ns.cat_id and l.LangName = '$language' and ns.user_id ='$valid_token_var' order by cl.`sort` $order");
                                if($count_of_user_categories_var > 0)
                                    {
                                        $user_choice = 1;
                                        $categories_to_fetch_sql = $user_categories_sql;
                                    }
                                else
                                    {
                                        if($just_urgent_news == 1)
                                            {
                                                $urgent_sql = " and n.urgent = 1";
                                            }
                                    }
                            }
                        else
                            {
                                if($just_urgent_news == 1)
                                    {
                                        $urgent_sql = " and n.urgent = 1";
                                    }
                            }
                    }
                else// If no token is found so get all categories
                    {
                        if($just_urgent_news == 1)
                            {
                                $urgent_sql = " and n.urgent = 1";
                            }
                        // Query to select all categories
                        $categories_to_fetch_sql = "select cl.IdCat,cl.CatName from languages as l,catlang as cl where l.IdLang = cl.IdLang and l.LangName = '$language' and cl.Deleted != 1 order by cl.`sort` $order";
                    }
            }
        
        $results_res = $db->get_results($categories_to_fetch_sql);
        
        if($results_res && count($results_res) > 0)
            {
                $result = array("result" => array());
                $latest_id = "";
                
                if($last_news == 1 || !empty($author_news))//Retrieve news details for last news or for author news or specific category
                    {
                        if($last_news == 1)
                            $result["result"][] = array("type" => "category","id" => '903930' ,"title" => api_last_news);
                        else
                            {
                                $author_name_var = $db->get_var("select concat(UserName,' ',FamName) as full_name from users where UserId = '$author_news'");
                                $result["result"][] = array("type" => "category","id" => '903930' ,"title" => api_author_news . $author_name_var);
                            }
                        foreach($results_res as $news)
                            {
                                $news_array = retrieve_news_in_list_details($news,$language);
                                $latest_id = $news_array['latest_id'];
                                $last_news_date = $news_array['last_news_date'];
                                $result["result"][] = $news_array['details'];
                            }
                            
                        if(!empty($author_news))// Query to calculate if their were any more news for the same author to fetch in next request from the server
                            $have_more_news_sql = "select count(*) from news as n where n.`Date` <= '$last_news_date' and n.Active = 1 and n.Deleted != 1 and n.`IdNews` != '$latest_id' and n.IdUserName = '$author_news'" . $urgent_sql ." order by n.Date Desc,n.IdNews desc";
                        else if(!empty($cat_id))// Query to calculate if their were any more news in same category to fetch in next request from the server
                            $have_more_news_sql = "select count(*) from news as n,newscategoies as nc where n.IdNews = nc.IdNews and nc.IdCat = '$cat_id' and n.`Date` <= '$last_news_date' and n.Active = 1 and n.Deleted != 1 and n.`IdNews` != '$latest_id'" . $urgent_sql ." order by n.Date desc,n.IdNews desc";
                        else// Query to calculate if their were any more last news to fetch in next request from the server
                            $have_more_news_sql = "select count(*) from news as n where n.`Date` <= '$last_news_date' and n.Active = 1 and n.Deleted != 1 and n.`IdNews` != '$latest_id'" . $urgent_sql ." order by n.Date Desc,n.IdNews desc";
                        
                        $have_more_news_var = $db->get_var($have_more_news_sql);
                        
                        if($have_more_news_var > 0)
                            {
                                if(!empty($author_news))
                                    $result["result"][] = array("type" => "more","id" => $latest_id,"author" => $author_news,"title" => api_get_news_more,"urgent" => $just_urgent_news);
                                else if(!empty($cat_id))
                                    $result["result"][] = array("type" => "more","id" => $latest_id,"cat" => $cat_id,"title" => api_get_news_more,"urgent" => $just_urgent_news);
                                else
                                    $result["result"][] = array("type" => "more","id" => $latest_id,"cat" => 'all',"title" => api_get_news_more,"urgent" => $just_urgent_news);
                            }
                        else
                            {
                                $result["result"][] = array("type" => "more","id" => -1);
                            }
                    }
                else
                    {
                        foreach($results_res as $category)
                            {
                                $cat_id = $category->IdCat;
                                if($user_choice == 1)
                                    {
                                        if($category->only_urgent == 1)
                                            {
                                                $urgent_sql = " and n.urgent = 1";
                                            }
                                    }
                                    
                                $news_sql = "select * from languages as l,news as n,newslang as nl,newscategoies as nc where l.IdLang = nl.IdLang and n.IdNews = nc.IdNews and n.IdNews = nl.IdNews and n.Active = 1 and n.Deleted != 1 and nc.IdCat = '$cat_id' and l.LangName = '$language'" . $urgent_sql ." order by n.Date desc,n.IdNews desc limit 0,$limit";

                                $news_res = $db->get_results($news_sql);
                                $latest_id = "";
                                if($news_res && count($news_res) > 0)
                                    {
                                        $result["result"][] = array("type" => "category","id" => $category->IdCat ,"title" => $category->CatName);
                                        foreach($news_res as $news)
                                            {
                                                $news_array = retrieve_news_in_list_details($news,$language);
                                                $latest_id = $news_array['latest_id'];
                                                $last_news_date = $news_array['last_news_date'];
                                                $result["result"][] = $news_array['details'];
                                            }
                                        $have_more_news_sql = "select count(*) from news as n,newscategoies as nc where n.IdNews = nc.IdNews and nc.IdCat = '$cat_id' and n.`Date` <= '$last_news_date' and n.Active = 1 and n.Deleted != 1 and n.`IdNews` != '$latest_id'" . $urgent_sql ." order by n.Date desc,n.IdNews desc";
                                        $have_more_news_var = $db->get_var($have_more_news_sql);

                                        if($have_more_news_var > 0)
                                            {
                                                if($user_choice == 1)
                                                    $result["result"][] = array("type" => "more","id" => $latest_id,"cat" => $cat_id,"title" => api_get_news_more,"urgent" => $category->only_urgent);
                                                else
                                                    $result["result"][] = array("type" => "more","id" => $latest_id,"cat" => $cat_id,"title" => api_get_news_more,"urgent" => $just_urgent_news);
                                            }
                                        else
                                            {$result["result"][] = array("type" => "more","id" => -1);}
                                    }
                            }
                    }
                
                if(count($result['result']) == 0)
                    {return $no_news_found_error_json;}
                else
                    {
                        $result['last_news_id'] = count($result['result']);
                        return json_encode(array("s" => 1,"r" => $result));
                    }
            }
    else
        {
            return $no_news_found_error_json;
        }
    }

function get_more_news($categories_infos = array('l'=> '','o' => '','li' => '','c' => '','ld' => '','lid' => '','t' => '','u' => '','ln' => '','a' => ''))
    {
        global $db;
        if(empty($db)){$db = new db();}
    
        $malformed_error_json = json_encode(array("s" => '001'));
        $invalid_infos_form_json = json_encode(array("s" => '002'));
        $invalid_category_error_json = json_encode(array("s" => '003'));
        $no_news_found_error_json = json_encode(array("s" => '004'));
    
        //create an array that will contains our indexes to prevent sending array with any other infos
        $valid_indexes_arr = array("l","o","li","c","ld","lid","t","u","ln","a");
        
        //Create an array that will contains our optional indexes to skip when any of element are empty
        $optional_indexes_arr = array("l","o","li","t","u","ln","a");
        
        //Create an array that will contains our default values that will be used when any infos is empty
        $default_values_arr = array("l" => 'Arabic',"o" => "desc","li" => 5,"c" => "","ld" => "","lid" => "","t" => "","u" => "","ln" => 0,"a" => "");
    
        // Propose that all infos are valid
        $infos_validity_arr = array("l" => 1,"o" => 1,"li" => 1,"c" => 1,"ld" => 1,"lid" => 1,"t" => 1,"u" => 1,"ln" => 1,"a" => 1);
    
        //if the user infos is empty or not a valid array or its count is not equal to our supposed array
        if(empty($categories_infos) || !is_array($categories_infos) || count($categories_infos) <= 0)
            {
                $categories_infos = $default_values_arr;
            }
            
        //test each info validity
        $index = 0;
        foreach($categories_infos as $info_name => $info_value)
            {
                if(!in_array($info_name,$valid_indexes_arr))
                    {
                        return($malformed_error_json);
                    }

                // If it is not valid set the convenable index to 0
                if(!in_array($info_name, $optional_indexes_arr) && (empty($info_value) || strlen(trim($info_value)) == 0))
                    {
                        $infos_validity_arr[$info_name] = 0;
                    }
                else
                    {
                        if((empty($info_value) || strlen(trim($info_value)) == 0))
                            {
                                $categories_infos[$info_name] = $default_values_arr[$info_name];
                            }
                    }
                $index ++;
            }
        
        $language = isset($categories_infos['l']) && ($db->get_var("select LangName from languages where LangName = '".InputFilter($categories_infos['l'])."'"))? InputFilter($categories_infos['l']) : 'Arabic';
        $order = isset($categories_infos['o']) && in_array(InputFilter($categories_infos['o']),array('asc','desc'))? InputFilter($categories_infos['o']) : 'desc';
        $cat_id = isset($categories_infos['c']) && !empty($categories_infos['c']) && ($db->get_var("select IdCat from catlang where IdCat = '".InputFilter($categories_infos['c']."'")))? InputFilter($categories_infos['c']) : "";// Get news for specific category or for all categories
        $token = isset($categories_infos['t']) ? InputFilter($categories_infos['t']) : "";// Token of user to know if we want to get news suitable to user settings or all news
        $last_news = isset($categories_infos['ln']) && $categories_infos['ln'] == '1' ? 1 : 0;// Get news by last news
        $just_urgent_news = isset($categories_infos['u']) && $categories_infos['u'] == '1' ? 1 : 0;// Get just urgent news
        // If not empty so we are getting news for a specific author
        $author_news = isset($categories_infos['a']) && !empty($categories_infos['a']) && ($db->get_var("select UserId from users where UserId = '".InputFilter($categories_infos['a'])."'")) ? InputFilter($categories_infos['a']) : "";
        $author_name = "";
        if(!empty($author_news))
        {
            $author_name = $db->get_var("select concat(UserName,' ',FamName) as full_name from users where UserId = '$author_news'");
        }
        $limit = ($last_news == 1)?10 : (!empty($cat_id)?10 : 3);// count of news we want to retrieve each time
        $user_choice = 0;
        $for_days = 4;
        $urgent_sql = "";
        $last_date = InputFilter($categories_infos['ld']);
        $last_id = InputFilter($categories_infos['lid']);
        
        $last_news_date = "";
        $latest_id = "";
        $is_valid_token_var = $db->get_var("select UserId from users where app_token = '$token'");
        if($last_news == 1 || !empty($author_news))
            {
                if($just_urgent_news == 1)
                    $urgent_sql = " and n.urgent = 1";
                $valid_category_row = true;
            }
        else if(!empty($cat_id))
            {
                if($is_valid_token_var)
                    {
                        $is_subscribed_var = $db->get_var("select only_urgent from news_subscription where user_id = '$is_valid_token_var' and cat_id = '$cat_id'");
                        if($is_subscribed_var)
                            {
                                if($is_subscribed_var == 1)
                                    {
                                        $urgent_sql = " and n.urgent = 1";
                                    }
                            }
                        else
                            {
                                if($just_urgent_news == 1)
                                    {
                                        $urgent_sql = " and n.urgent = 1";
                                    }
                            }
                    }
                $valid_category_row = $db->get_row("select * from languages as l,catlang as cl where l.IdLang = cl.IdLang and l.LangName = '$language' and cl.IdCat = '$cat_id' limit 0,1");
            }
        
        if($valid_category_row)
            {
                $result = array("result" => array());
                if($last_news == 1)
                    {
                        $news_res = $db->get_results("select * from languages as l,news as n,newslang as nl where l.IdLang = nl.IdLang and n.IdNews = nl.IdNews and n.Active = 1 and n.Deleted != 1 and l.LangName = '$language' and n.Date <= '$last_date' and n.IdNews != '$last_id' and n.IdNews < $last_id$urgent_sql order by n.Date desc,n.IdNews desc limit 0,$limit");
                    }
                else if(!empty($author_news))
                    {
                        $news_res = $db->get_results("select * from languages as l,news as n,newslang as nl where l.IdLang = nl.IdLang and n.IdNews = nl.IdNews and n.Active = 1 and n.Deleted != 1 and l.LangName = '$language' and n.Date <= '$last_date' and n.IdNews != '$last_id' and n.IdNews < $last_id and n.IdUserName = '$author_news'$urgent_sql order by n.Date desc,n.IdNews desc limit 0,$limit");
                    }
                else
                    {
                        $category_row = $valid_category_row;
                        $cat_id = $category_row->IdCat;
                        $news_res = $db->get_results("select * from languages as l,news as n,newslang as nl,newscategoies as nc where l.IdLang = nl.IdLang and n.IdNews = nc.IdNews and n.IdNews = nl.IdNews and n.Active = 1 and n.Deleted != 1 and nc.IdCat = '$cat_id' and l.LangName = '$language' and n.Date <= '$last_date' and n.IdNews != '$last_id'$urgent_sql order by n.Date desc,n.IdNews desc limit 0,$limit");
                    }

                if(count($news_res) > 0)
                    {
                        if(!empty($author_news))
                            {
                                $result["result"][] = array("type" => "category","id" => $author_news ,"title" => $author_name);
                            }
                        else if(!empty($cat_id))
                            {
                                $result["result"][] = array("type" => "category","id" => $cat_id ,"title" => $category_row->CatName);
                            }
                        else
                            {
                                $result["result"][] = array("type" => "category","id" => $category_row->IdCat ,"title" => $category_row->CatName);
                            }
                            
                        
                        foreach($news_res as $news)
                            {
                                $news_array = retrieve_news_in_list_details($news_res,$language);
                                $latest_id = $news_array['latest_id'];
                                $last_news_date = $news_array['last_news_date'];
                                $result["result"][] = $news_array['details'];
                            }
                            
                        if($last_news == 1)
                            $have_more_news_rs = $db->get_var("select count(*) from news as n where n.`Date` <= '$last_news_date' and n.`IdNews` != '$latest_id' and n.Active = 1 and n.Deleted != 1$urgent_sql order by n.Date desc,n.IdNews desc");
                        else if(!empty($author_news))
                            $have_more_news_rs = $db->get_var("select count(*) from news as n where n.`Date` <= '$last_news_date' and n.`IdNews` != '$latest_id' and n.Active = 1 and n.Deleted != 1 and n.IdUserName = '$author_news'$urgent_sql order by n.Date desc,n.IdNews desc");
                        else if(!empty($cat_id))
                            {
                                if($is_valid_token_var)
                                    {
                                        $is_subscribed_var = $db->get_var("select only_urgent from news_subscription where user_id = '$is_valid_token_var' and cat_id = '$cat_id'");
                                        if($is_subscribed_var == 1)
                                            {
                                                $urgent_sql = " and n.urgent = 1";
                                            }
                                        else
                                            {
                                                if($just_urgent_news == 1)
                                                    $urgent_sql = " and n.urgent = 1";
                                            }
                                    }
                                else
                                    {
                                        if($just_urgent_news == 1)
                                            $urgent_sql = " and n.urgent = 1";
                                    }
                                $have_more_news_rs = $db->get_var("select count(*) from news as n,newscategoies as nc where n.IdNews = nc.IdNews and nc.IdCat = '$cat_id' and n.`Date` <= '$last_news_date' and n.`IdNews` != '$latest_id' and n.Active = 1 and n.Deleted != 1$urgent_sql order by n.Date desc,n.IdNews desc");
                            }
                        else
                            $have_more_news_rs = $db->get_var("select count(*) from news as n,newscategoies as nc where n.IdNews = nc.IdNews and nc.IdCat = '$cat_id' and n.`Date` <= '$last_news_date' and n.`IdNews` != '$latest_id' and n.Active = 1 and n.Deleted != 1$urgent_sql order by n.Date desc,n.IdNews desc");

                        if($have_more_news_rs > 0)
                            {
                                if(!empty($author_news))
                                    $result["result"][] = array("type" => "more","id" => $latest_id,"author" => $author_news,"title" => api_get_news_more,"urgent" => $just_urgent_news);
                                else if(!empty($cat_id))
                                    $result["result"][] = array("type" => "more","id" => $latest_id,"cat" => $cat_id,"title" => api_get_news_more,"urgent" => $just_urgent_news);
                                else
                                    $result["result"][] = array("type" => "more","id" => $latest_id,"cat" => 'all',"title" => api_get_news_more,"urgent" => $just_urgent_news);
                                
                            }
                        else
                            $result["result"][] = array("type" => "more","id" => -1);
                        $result['last_news_id'] = count($result['result']);
                        return json_encode(array("s" => 1,"r" => $result));
                    }
                else
                    {
                        return $no_news_found_error_json;
                    }
            }
        else
            {
                return $invalid_category_error_json;
            }
}

function get_news_details($news_infos = array("n" => "","l" => ""))
    {
        global $db;
            if(empty($db)){$db = new db();}
        
        $invalid_news_error_json = json_encode(array("s" => '001'));
        $malformed_error_json = json_encode(array("s" => '002'));
        $invalid_language_error_json = json_encode(array("s" => '003'));
        
        $valid_indexes_arr = array("n","l");
        $optional_indexes_arr = array("l");
        $default_values_arr = array("n" => '',"l" => 'Arabic');
        $infos_validity_arr = array("n" => 1,"l" => 1);
    
        $index = 0;
        foreach($news_infos as $info_name => $info_value)
            {
                if(!in_array($info_name,$valid_indexes_arr) || $valid_indexes_arr[$index] != $info_name)
                    {return($malformed_error_json);}

                // If it is not valid set the convenable index to 0
                if(!in_array($info_name, $optional_indexes_arr) && (empty($info_value) || strlen(trim($info_value)) == 0))
                    {$infos_validity_arr[$info_name] = 0;}
                else
                    {
                        if((empty($info_value) || strlen(trim($info_value)) == 0))
                            {$news_infos[$info_name] = $default_values_arr[$info_name];}
                    }
                $index ++;
            }
    
        foreach($infos_validity_arr as $info => $validity)
            {
                if(empty($validity) || $validity == 0)
                    {return(json_encode(array("s" => '001',"v" => $infos_validity_arr)));}
            }
        
        $news_id = InputFilter($news_infos['n']);
        $language = InputFilter($news_infos['l']);
        
        $valid_language_var = $db->get_var("select IdLang from languages where LangName = '$language' and Deleted != 1");
        
        if(!$valid_language_var)
            return $invalid_language_error_json;
        
        $news_details_row = $db->get_row("select * from languages as l,news as n,newslang as nl where l.IdLang = nl.IdLang and n.IdNews = nl.IdNews and n.IdNews = '$news_id' and l.IdLang = '$valid_language_var' and n.Active = 1 and n.Deleted != 1");
        if($news_details_row)
            {
                $date = socialDateDif(strtotime(date("Y-m-d H:i:s")), strtotime($news_details_row->Date), true);
                $result['result'] = array("id" => $news_details_row->IdNews,"pic" => $news_details_row->NewsPic ,"title" => $news_details_row->Tilte,"full" => $news_details_row->FullMessage,"date" => $date);
                return json_encode(array("s" => 1,"r" => $result));
            }
        else
            {
                return $invalid_news_error_json;
            }
        
    }

function set_settings($settings_infos = array("t","l","n" => array(),"s" => array()))
{
    global $db;
    
    if(empty($db))
        {$db = new db();}
    
    $malformed_error_json = json_encode(array("s" => '001'));
    $invalid_token_error_json = json_encode(array("s" => '002'));
    $notification_settings_array_contain_invalid_infos = json_encode(array("s" => '003'));
    $display_settings_array_contain_invalid_infos = json_encode(array("s" => '004'));
    
    $valid_indexes_arr = array("t","l","n","s");
    $default_values_arr = array("t" => '',"l" => 'Arabic',"n" => '',"s" => '');
    $infos_validity_arr = array("t" => 1,"l" => 1,"n" => 1,"s" => 1);
    
    $index = 0;
    foreach($settings_infos as $info_name => $info_value)
        {
            if(!in_array($info_name,$valid_indexes_arr) || $valid_indexes_arr[$index] != $info_name)
                {return($malformed_error_json);}

            // If it is not valid set the convenable index to 0
            if(empty($info_value))
                {$infos_validity_arr[$info_name] = 0;}
            else
                {
                    if((empty($info_value)))
                        {$news_infos[$info_name] = $default_values_arr[$info_name];}
                }
            $index ++;
        }

    foreach($infos_validity_arr as $info => $validity)
        {
            if(empty($validity) || $validity == 0)
                {return(json_encode(array("s" => '001',"v" => $infos_validity_arr)));}
        }
    
    $passed_language = InputFilter($settings_infos['l']);
    
    $valid_language = $db->get_var("select * from languages where LangName = '$passed_language'");
    if(!$valid_language)
        $language = 'Arabic';
    else
        $language = $passed_language;
    
    $token = InputFilter($settings_infos['t']);
    
    $valid_user_row = $db->get_row("select * from users where app_token = '$token'");
    if($valid_user_row)
        {
            $user_id = $valid_user_row->UserId;
            $update_user_preferred_language = $db->query("update users set PrefLang = '$language' where UserId = '$user_id'");
            $notifications_settings_arr = $settings_infos['n'];
            $subscriptions_settings_arr = $settings_infos['s'];
    
            foreach ($notifications_settings_arr as $valid_notification_setting)
                {
                    if(count($valid_notification_setting) != 2 || !isset($valid_notification_setting['u']) || !isset($valid_notification_setting['i']))
                        {return $notification_settings_array_contain_invalid_infos;}
                }
            
            foreach ($subscriptions_settings_arr as $valid_subscription_setting)
                {
                    if(count($valid_subscription_setting) != 2 || !isset($valid_subscription_setting['u']) || !isset($valid_subscription_setting['i']))
                        {return $display_settings_array_contain_invalid_infos;}
                }
                
            foreach ($notifications_settings_arr as $setting)
                {
                    $only_urgent = InputFilter($setting['u']) == 1?1 : 0;
                    $news_group_id = InputFilter($setting['i']);
                    $valid_news_group_id_var = $db->get_var("select * from catlang where IdCat = '$news_group_id'");
                    if(!$valid_news_group_id_var)
                        {$news_group_id = '20100000000';}
                    
                    $already_found_setting_var = $db->get_var("select id from notification where id_user = '$user_id' and id_news_group = '$news_group_id'");
                    
                    if($already_found_setting_var)
                        {$update_setting_qu = $db->query("update notification set only_urgent = '$only_urgent' where id_news_group = '$news_group_id' and id_user = '$user_id'");}
                    else
                        {$insert_setting_qu = $db->query("insert into notification values(NULL,'$user_id','$news_group_id','$only_urgent')");}
                }
                
            foreach ($subscriptions_settings_arr as $setting)
                {
                    $only_urgent = InputFilter($setting['u']) == 1?1 : 0;
                    $news_group_id = InputFilter($setting['i']);
                    $valid_news_group_id_var = $db->get_var("select * from catlang where IdCat = '$news_group_id'");
                    if(!$valid_news_group_id_var)
                        {$news_group_id = '20100000000';}
                    
                    $already_found_setting_var = $db->get_var("select id from news_subscription where user_id = '$user_id' and cat_id = '$news_group_id'");
                    
                    if($already_found_setting_var)
                        {$update_setting_qu = $db->query("update news_subscription set only_urgent = '$only_urgent' where cat_id = '$news_group_id' and user_id = '$user_id'");}
                    else
                        {$insert_setting_qu = $db->query("insert into news_subscription values(NULL,'$user_id','$news_group_id','$only_urgent')");}
                }
        }
    else
        {return $invalid_token_error_json;}
    
}

function get_settings($settings_infos = array("t"))
{
    global $db;
    
    if(empty($db))
        {$db = new db();}
    
    $invalid_infos_error_json = json_encode(array("s" => '001'));
    $malformed_error_json = json_encode(array("s" => '002'));
    $invalid_token_error_json = json_encode(array("s" => '003'));
    
    $valid_indexes_arr = array("t");
    $infos_validity_arr = array("t" => 1);
    
    $index = 0;
    
    if(!is_array($settings_infos) || count($settings_infos) <= 0 || count($settings_infos) > $valid_indexes_arr)
        {return $invalid_infos_error_json;}
    
    foreach($settings_infos as $info_name => $info_value)
        {
            if(!in_array($info_name,$valid_indexes_arr) || $valid_indexes_arr[$index] != $info_name)
                {return($malformed_error_json);}
            $index ++;
        }
        
    $passed_token = InputFilter($settings_infos['t']);
    $valid_token_row = $db->get_row("select * from users where app_token = '$passed_token' limit 0,1");
    
    if($valid_token_row)
        {
            $user_id = $valid_token_row->UserId;
            $language = $valid_token_row->PrefLang;
            $returned_results = array("s" => '1',"l" => $language,"no" => array(),"se" => array());
            
            $all_categories_res = $db->get_results("select cl.IdCat,cl.CatName from catlang as cl,languages as l where l.IdLang = cl.IdLang and l.LangName = '$language'");
            if(count($all_categories_res) > 0)
                {
                    foreach($all_categories_res as $category_row)
                        {
                            $cat_id = $category_row->IdCat;
                            $cat_name = $category_row->CatName;
                            $notifications_settings_row = $db->get_row("select * from notification where id_user = '$user_id' and id_news_group = '$cat_id'");
                            $returned_results["no"][$cat_id]["i"] = $cat_id;
                            $returned_results["no"][$cat_id]["n"] = $cat_name;
                            $returned_results["no"][$cat_id]["s"] = 0;
                            
                            $returned_results["se"][$cat_id]["i"] = $cat_id;
                            $returned_results["se"][$cat_id]["n"] = $cat_name;
                            $returned_results["se"][$cat_id]["s"] = 0;
                            if($subscriptions_settings_row)
                                {
                                    $returned_results["no"][$cat_id]["s"] = 1;
                                    $returned_results["no"][$cat_id]["u"] = $subscriptions_settings_row->only_urgent;
                                }
                            
                            $subscriptions_settings_row = $db->get_var("select * from news_subscription where user_id = '$user_id' and cat_id = '$cat_id'");
                            if($subscriptions_settings_row)
                                {
                                    $returned_results["se"][$cat_id]["s"] = 1;
                                    $returned_results["se"][$cat_id]["u"] = $subscriptions_settings_row->only_urgent;
                                }
                        }
                }
            return json_encode($returned_results);
        }
    else
        {return $invalid_token_error_json;}
}

function get_about($page_infos = array("l" => "Arabic"))
{
    $page_not_found_json_error = array("s" => '001');
    
    if(isset($page_infos['l']))
        {
            $passed_language = InputFilter($page_infos['l']);
            $is_valid_language_var = $db->get_var("select IdLang from language where LangName = '$passed_language' and Deleted != 1");
            if($is_valid_language_var)
                $page_language = $is_valid_language_var;
            else
                $page_language = 'Arabic';
        }
    else
        {
            $page_language = 'Arabic';
        }
        
    global $db;
    
    if(empty($db))
        $db = new db();
    
    $about_page_row = $db->get_row("select pl.PageTitle,pl.Content from pages as p,pagelang as pl,languages as l where l.IdLang = pl.IdLang and p.IdPage = pl.IdPage and p.PageNbr = 1 and l.LangName = '$page_language'");
    if($about_page_row)
        {
            return json_encode(array("s" => 1,"r" => array("title" => $about_page_row->PageTitle,"content" => $about_page_row->Content)));
        }
    else
        {
            return json_encode($page_not_found_json_error);
        }
}

function send_news($news_infos = array("t" => "","c" => "","a" => "","ca" => "","l" => "","i" => "","v" => "","u" => "","ti" => ""))
{
    global $db;
    
    if(empty($db))
        $db = new db();
    
    $invalid_infos_json_error = json_encode(array("s" => '001'));
    $malformed_error_json = json_encode(array("s" => '002'));
    $invalid_token_error_json = json_encode(array("s" => '003'));
    $unsufficient_permissions_error_json = json_encode(array("s" => '004'));
    $invalid_language_error_json = json_encode(array("s" => '005'));
    $empty_content_error_json = json_encode(array("s" => '006'));
    
    $valid_indexes_arr = array("t","l","c","a","ca","i","v" => "","u","ti");
    $guest_valid_indexes_arr = array("l","c","ca","i","v" => "" ,"ti");
    
    
    if(!isset($news_infos) || !is_array($news_infos) || count($news_infos) > count($valid_indexes_arr) || count($news_infos) <= 0)
        {return $invalid_infos_json_error;}
    
    $index = 0;
    foreach($news_infos as $info_name => $info_value)
        {
            if(!in_array($info_name,$valid_indexes_arr))
                {return($malformed_error_json);}
            $index ++;
        }
    
    $passed_language = isset($news_infos['l']) && !empty($news_infos['l'])?InputFilter($news_infos['l']):"";
    if(!empty($passed_language))
        {
            $news_language_var = $db->get_var("select IdLang from languages where LangName = '$passed_language' and Deleted != 1");
            if($news_language_var)
                {$news_language = $news_language_var;}
            else
                {return $invalid_language_error_json;}
        }
    else
        {return $invalid_language_error_json;}
    
    $passed_news_body = isset($news_infos['c']) && !empty($news_infos['c'])?$news_infos['c'] : "";
    if(empty($passed_news_body))
        {return $empty_content_error_json;}
    else
        {
            $w_tags_news_body = strip_tags($passed_news_body);
            $news_body = str_replace(array("\n"),array("<line>"),$w_tags_news_body);
        }
    
    if(isset($news_infos['t']))
        {
            $passed_token = InputFilter($news_infos['t']);
            $is_valid_token_row = $db->get_row("select * from users where app_token = '$passed_token'");
            if(!$is_valid_token_row)
                {return $invalid_token_error_json;}
            
            else
                {
                    $user_id = $is_valid_token_row->UserId;
                    $user_nick_name = $is_valid_token_row->NickName;
                    
                    $is_admin_var = $db->get_var("select AdminId from admins where AdminId = '$user_id'");
                    if($is_admin_var)
                        {
                            $news_urgent = isset($news_infos['u']) && !empty($news_infos['u']) && $news_infos['u'] == 1 ? 1 : 0;
                            $passed_agency = isset($news_infos['a']) && !empty($news_infos['a'])?InputFilter($news_infos['a']):$user_nick_name;
                            $is_valid_agency_var = $db->get_var("select NickName from users as u,groups as g where g.GroupId = u.GroupId and (md5(u.NickName) = '" . md5($passed_agency) . "' or u.NickName = '$user_nick_name') and g.GroupName = 'agencies'");
                            $news_agency = $is_valid_agency_var ? $is_valid_agency_var : $user_nick_name;
                            $news_active = 1;
                        }
                    else
                        {
                            $news_urgent = 0;
                            $news_agency = $user_nick_name;
                            $new_active = 0;
                        }
                }
        }
    else
        {
            $is_guest = 1;
            foreach($news_infos as $info_name => $info_value)
                {
                    if(!in_array($info_name,$guest_valid_indexes_arr))
                        return $unsufficient_permissions_error_json;
                }
            $user_id = '20070000000';
            $news_urgent = 0;
            $news_agency = "Guest";
            $news_active = 0;
        }
    
    
    $news_id = GenerateID('news', 'IdNews');
    $news_date = date('Y-m-d H:i:s',time());
    $year = date('Y',time());
    $month = date('m',time());
    $day = date('d',time());
    $news_hits = 0;
    $news_deleted = 0;
    
    $passed_news_title = isset($news_infos['ti']) && !empty($news_infos['ti']) ? InputFilter($news_infos['ti']) : mb_substr($news_body,0,150,"utf8");    
    $news_title_full = str_replace(array("<line>","\n"),array(" "," "),$passed_news_title);
    $news_title_to_replace = InputFilter($news_title_full);
    $news_title = str_replace(array(":", "/", "\\", "@","#",'"',"'"), array("", "", "", "","","",""),$news_title_to_replace);
    
    $news_subtitle = "";
    
    $news_full_brief = str_replace(array("<line>","\n"),array(" "," "),$news_body);
    $news_brief = mb_substr($news_full_brief,0,500,"utf8");
    
    $news_full_message_full = str_replace(array("<line>"),array("<br />"),$news_body);
    $news_full_message = InputFilter($news_full_message_full);
    
    $news_note = "";
    
    $passed_category = isset($news_infos['ca']) && !empty($news_infos['ca'])?InputFilter($news_infos['ca']):"";
    $news_category = !empty($news_infos['ca']) && ($db->get_var("select IdCat from catlang as cl,languages as l where l.IdLang = cl.IdLang and cl.IdCat = '$passed_category' and l.LangName = '$news_language'"))?$passed_category : "20100000000";
    
    $passed_news_images = isset($news_infos['i']) && !empty($news_infos['i'])?InputFilter($news_infos['i']) : "";
    if(isset($news_infos['i']) && !empty($news_infos['i']))
        {
            $passed_news_images_arr = explode('||',$passed_news_images);
            foreach($passed_news_images_arr as $passed_news_image)
                {
                    if(file_exists("../../uploads/gallery/Albums/$year/$day-$month-$year/$passed_news_image"))
                        {$news_images_arr[] = $passed_news_image;}
                }
            
            if(isset($news_images_arr) && is_array($news_images_arr) && count($news_images_arr) > 0)
                {$news_image = $news_images_arr[0];}
            else
                {
                    $news_image = 'newspaper.png';
                    $news_images_arr = array();
                }
        }
    else
        {
            $news_image = 'newspaper.png';
            $news_images_arr = array();
        }
        
    $insert_news_sql = "insert into news(IdNews,IdUserName,Date,Active,Hits,NewsPic,Deleted,urgent,agency) values ('$news_id','$user_id','$news_date','$news_active','$news_hits','$news_image','$news_deleted','$news_urgent','$news_agency')";
    $insert_news_qu = $db->query($insert_news_sql);

    if($db->dbh->affected_rows > 0)
        {
            if($news_urgent == 1)
                {
                    $marquee_message = str_replace(array(":", "/", "\\", "@",'"',"'"), array("", "", "", "","",""),$news_title);
                    $marquee_title_in_link = mb_substr($marquee_message, 0, 40, "utf8");
                    $marquee_id = GenerateID('marques', 'idMarque');
                    $marquee_link = CreateLink('',array('Prog','ns','idnews','title'),array('news','details',$news_id,$marquee_title_in_link));
                    $marquee_start_date = date('Y-m-d H:i:s',time());
                    $marquee_end_date = date('Y-m-d H:i:s', strtotime("$marquee_start_date +30 hours"));
                    $db->query("insert into marques(idMarque,Link,StartDate,EndDate,Deleted) values('$marquee_id','$marquee_link','$marquee_start_date','$marquee_end_date','0')");
                    $db->query("insert into marqlang(idmarque,idLang,Message) values('$marquee_id','$news_language','$marquee_message')");
                }
            $insert_news_category_sql = "insert into `newscategoies` values('$news_id','$news_category')";
            $insert_news_category_qu = $db->query($insert_news_category_sql);
            //$insert_news_category_sql;
            
            $news_full_message .= '<div id="news_desc_images_wrapper_div">';
            if(count($news_images_arr)>0)//If their is any image uploaded
                {
                    $all_images = '<br />';
                    $counter = 0;
                    foreach($news_images_arr as $news_image_row)
                        {
                            if($counter === 0)
                                {
                                    create_news_thumbs("../../uploads/gallery/Albums/$year/$day-$month-$year/".$news_image_row, "../../uploads/news/pics/".$news_image_row, 100);
                                }
                            $extension = pathinfo($news_image_row, PATHINFO_EXTENSION);
                            $media_id = GenerateID("gallery", 'IdMedia');
                            $media_path = "uploads/gallery/Albums/$year/$day-$month-$year/$news_image_row";
                            $AddDate = $news_date;
                            $MapLocation = "";
                            $MediaRank = "";
                            $MediaType = $extension;
                            $news_caption = InputFilter($news_title);
                            $Desc = "";
                            $Place = "";
                            $Tags = "";
                            $insert_to_gallery_sql = "insert into gallery(IdMedia,Path,AddDate,MapLocation,MediaRank,MediaType) values ('$media_id','$media_path','$AddDate','$MapLocation','$MediaRank','$MediaType')";
                            $insert_to_gallery_qu = $db->query($insert_to_gallery_sql);
                            $insert_into_gallery_lang_sql = "insert into gallerylang(`IdMedia`,`IdLang`,`Caption`,`Desc`,`Place`,`Tags`) values ('$media_id','$news_language','$news_caption','$Desc','$Place','$Tags')";
                            $insert_into_gallery_lang_qu = $db->query($insert_into_gallery_lang_sql);
                            $all_images .= "<div class=\'news_desc_images\'><img src=\'uploads/gallery/Albums/$year/$day-$month-$year/$news_image_row\' /></div>";
                            $counter++;
                        }
                    $news_full_message .= $all_images;
                }
            $news_full_message .= '</div>';
            
            $insert_news_lang_sql = "insert into newslang(IdLang,IdNews,Tilte,SubTitle,Breif,FullMessage,Note) values ('$news_language','$news_id','$news_title','$news_subtitle','$news_brief','$news_full_message','$news_note')";
            $insert_news_lang_qu = $db->query($insert_news_lang_sql);
            
            if($db->dbh->affected_rows > 0)
                {
                    $db->query("update users set points = points + 1 where UserId = '$user_id'");
                    return json_encode(array("s" => '1',"r" => array("id" => $news_id)));
                }
        }
}

function audit_news($news_infos=array("t" => "","i" => "","u" => ""))
{
    global $db;
    if(empty($db))
        $db = new db();
    
    $invalid_infos_error_json = json_encode(array("s" => '001'));
    $invalid_token_error_json = json_encode(array("s" => '002'));
    $unsufficient_privilege_error_json = json_encode(array("s" => '003'));
    $invalid_news_error_json = json_encode(array("s" => '004'));
    
    $valid_indexes_arr = array("t","i","u");
    
    if(!isset($news_infos['t']) || !is_array($news_infos) || count($news_infos) <= 0 || count($news_infos) > count($valid_indexes_arr) || !isset($news_infos['t']) || empty($news_infos['t']) || !isset($news_infos['i']) || empty($news_infos['i']))
        {return $invalid_infos_error_json;}
    
    $passed_token = InputFilter($news_infos['t']);
    $is_valid_token_var = $db->get_var("select UserId from users where app_token = '$passed_token'");
    
    if(!$is_valid_token_var)
        {return $invalid_token_error_json;}
    else
        {
            $user_id = $is_valid_token_var;
            $is_admin_var = $db->get_var("select AdminId from admins where AdminId = '$user_id'");
            if(!$is_admin_var)
                return $unsufficient_privilege_error_json;
            
            $passed_news_id = InputFilter($news_infos['i']);
            $is_valid_unaudited_news_var = $db->get_var("select IdNews from news where IdNews = '$passed_news_id' and Active = 0");
            if(!$is_valid_unaudited_news_var)
                return $invalid_news_error_json;
            
            $news_id = $passed_news_id;
            
            $urgency = isset($news_infos['u']) && in_array(intval($news_infos['u']),array(0,1,2))?intval($news_infos['u']) : 0;
            
            switch($urgency)
                {
                    case '0':
                        $news_user_id_var = $db->get_var("select IdUserName from news where `IdNews` = '$news_id'");
                        $db->query("update news set Deleted = 1,del_by = '$news_user_id_var' where IdNews = '$news_id'");
                        $db->query("update users set points = points - 1 where UserId = '".$news_id."'");
                        return json_encode(array("s" => '1',"r" => '10'));
                        break;
                    case '1':
                        $db->query("update news set Active = 1,active_by = '$user_id' where IdNews = '$news_id'");
                        return json_encode(array("s" => "11","r" => array("i" => $news_id)));
                        break;
                    case '2':
                        $db->query("update news set Active = 1,urgent = 1,active_by = '$user_id' where IdNews = '$news_id'");
                        $marquee_id = GenerateID('marques', 'idMarque');
                        $marquee_start_date = date('Y-m-d H:i:s',time());
                        $marquee_end_date = date('Y-m-d H:i:s', strtotime("$marquee_start_date +30 hours"));
                        $language_news_inserted_in_rs = $db->get_results("select IdLang,Tilte from news as n,newslang as nl where n.IdNews = nl.IdNews and n.IdNews = '$news_id'");
                        foreach($language_news_inserted_in_rs as $language_news_inserted_in_row)
                        {
                            $marquee_message = str_replace(array(":", "/", "\\", "@",'"',"'","#"), array("", "", "", "","","",""),$language_news_inserted_in_row->Tilte);
                            $marquee_title_in_link = mb_substr($marquee_message, 0, 40, "utf8");
                            $marquee_link = CreateLink('',array('Prog','ns','idnews','title'),array('news','details',$news_id,$marquee_title_in_link));
                            $lang_id = $language_news_inserted_in_row->IdLang;
                            $db->query("insert into marques(idMarque,Link,StartDate,EndDate,Deleted) values('$marquee_id','$marquee_link','$marquee_start_date','$marquee_end_date','0')");
                            $db->query("insert into marqlang(idmarque,idLang,Message) values('$marquee_id','$lang_id','$marquee_message')");
                        }
                        return json_encode(array("s" => "12","r" => array("i" => $news_id)));
                        break;
                    default : return $invalid_news_error_json;break;
                }
            
        }
}

function get_unapprooved_news($user_infos = array("t" => "","l" => "","li" => ""))
{
    global $db;
    
    if(empty($db))
        $db = new db();
    
    $invalid_infos_error_json = json_encode(array("s" => '001'));
    $invalid_token_error_json = json_encode(array("s" => '002'));
    $invalid_language_error_json = json_encode(array("s" => '003'));
    $no_unapprooved_news_error_json = json_encode(array("s" => '004'));
    
    $valid_indexes_arr = array("t","l","li");
    
    if(!isset($user_infos) || !is_array($user_infos) || count($user_infos) <= 0 || !isset($user_infos['t']) || !isset($user_infos['l']) || count($user_infos) > count($valid_indexes_arr))
        {return$invalid_infos_error_json;}
    
    $passed_token = isset($user_infos['t']) && !empty($user_infos['t'])?InputFilter($user_infos['t']) : "";
    $is_valid_token_var = $db->get_var("select UserId from users where app_token = '$passed_token'");
    
    
    if($is_valid_token_var)
        {
            $passed_language = isset($user_infos['l']) && !empty($user_infos['l']) ? InputFilter($user_infos['l']) : "Arabic";
            $is_valid_language_var = $db->get_var("select IdLang from languages where Deleted != 1 and LangName = '$passed_language'");
            if($is_valid_language_var)
                {
                    $lang_id = $is_valid_language_var;
                    $limit = isset($infos['li']) && intval($infos['li']) > 0 && intval($infos['li']) <= 200 ? intval($infos['li']) : 20;
                    $limited_unapprooved_news_res = $db->get_results("select nl.Tilte as news_title,n.IdNews as news_id from languages as l,news as n,newslang as nl where l.IdLang = nl.IdLang and n.IdNews = nl.IdNews and n.Active != 1 and n.Deleted != 1 and l.IdLang = '$lang_id' Order By `Date` desc limit 0,$limit");
                    file_put_contents("unapprooved.txt", "select nl.Tilte as news_title,n.IdNews as news_id from languages as l,news as n,newslang as nl where l.IdLang = nl.IdLang and n.IdNews = nl.IdNews and n.Active != 1 and n.Deleted != 1 and l.IdLang = '$lang_id' Order By `Date` desc limit 0,$limit");
                    if(count($limited_unapprooved_news_res) > 0)
                        {
                            $returned_result = array();
                            $index = 0;
                            foreach($limited_unapprooved_news_res as $unapprooved_news_row)
                                {
                                    $returned_result[$index] = array("i" => $unapprooved_news_row->news_id,"t" => $unapprooved_news_row->news_title);
                                    $index ++;
                                }
                            return json_encode(array("s" => 1,"r" => $returned_result));
                        }
                    else
                        {return $no_unapprooved_news_error_json;}
                }
            else
                {return $invalid_language_error_json;}
        }
    else
        {return $invalid_token_error_json;}
}

/* This function will invoke send email function to send a contact us email */
function contact_us($email_infos = array("c" => "","d"=>"","e"=>"","f"=>"","l" => "","m"=>"","r" => "","t"=>""))
    {
        global $db,$AdminMail;
        
        $AdminMail = "info@cyberaman.com";
        if(empty($db))
            $db = new db();
        
        $invalid_infos_error_json = json_encode(array("s" => '001'));
        $invalid_token_error_json = json_encode(array("s" => '002'));
        $invalid_sender_error_json = json_encode(array("s" => '003'));
        $empty_message_error_json = json_encode(array("s" => '004'));
        if((!isset($email_infos['t']) && (!isset($email_infos['f']) || !isset($email_infos['e']))) || !isset($email_infos['m']) || !isset($email_infos['c']))
            return $invalid_infos_error_json;
        
        if(isset($_SESSION['n1']) && isset($_SESSION['n2']))
            {
                if($email_infos['c'] != $_SESSION['n1'] + $_SESSION['n2'])
                    {
                        file_put_contents("cal.txt",$email_infos['c'] . "must = " . $_SESSION['n1']. ' + ' .$_SESSION['n1']. "=" . $_SESSION['n1'] + $_SESSION['n2'] ."\n",FILE_APPEND);
                        unset($_SESSION['n1']);
                        unset($_SESSION['n2']);
                        $number1 = rand(0,10);
                        $_SESSION['n1'] = $number1;
                        $number2 = rand(0,10);
                        $_SESSION['n2'] = $number2;
                        return  json_encode(array("s" => '006',"n1" => $number1,"n2" => $number2));
                    }
            }
        $passed_token = InputFilter($email_infos['t']);
        if(!empty($passed_token))
            {
                $is_valid_token_row= $db->get_row("select Concat(UserName,' ',FamNAme) as full_name,UserMail as email,CellNbr,Contry from users where app_token = '$passed_token'");
                if(!$is_valid_token_row)
                    {
                        return $invalid_token_error_json;
                    }
                $email = $is_valid_token_row->email;
                $full_name = $is_valid_token_row->full_name;
                $tel_number = $is_valid_token_row->CellNbr;
                $country = $is_valid_token_row->Contry;
            }
        else
            {
                if(empty($email_infos['e']) || !check_email_address(InputFilter($email_infos['e'])))
                    return $invalid_sender_error_json;
                $email = InputFilter($email_infos['e']);
                $full_name = !empty($email_infos['f'])?InputFilter($email_infos['f']) : "زائر";
                $tel_number = "";
                $country = "";
            }
        $reason = (isset($email_infos['r']) && !empty($email_infos['r']) && in_array(InputFilter($email_infos['r']), array('question','complaint','suggestion')))?InputFilter($email_infos['r']) : "complaint";
        $passed_language = (isset($email_infos['l']) && !empty($email_infos['l'])) && $db->get_var("select LangName from languages where LangName = '" . InputFilter($email_infos['l']) ."'")?InputFilter($email_infos['l']) : "";
        $passed_department = (isset($email_infos['d']) && !empty($email_infos['d']))?InputFilter($email_infos['d']) : "";
        $is_valid_department_row = $db->get_row("SELECT c.`DepEmail`,cl.`DepName` FROM `contactus` as c , `contactuslang` as cl,`languages` as l where l.IdLang = cl.IdLang and `c`.`IdDep` = `cl`.`IdDep` and `c`.`IdDep`='$passed_department' and l.LangName = '$passed_language'");
        if($is_valid_department_row)
            {
                $department_name = $is_valid_department_row->DepName;
                $department_email = $is_valid_department_row->DepEmail;
            }
        else
            {
                $department_name = $passed_department;
                $department_email = $AdminMail;
            }
    
        $title = isset($email_infos['ti']) && !empty($email_infos['ti'])?InputFilter($email_infos['ti']) : "رسالة من تطبيق سايبر أمان";
        $message = InputFilter($email_infos['m']);
        $from = $email;
        $from_name = $full_name;
        $add_address[0] = $email;
        $add_address[1] = $department_name;
        $subject = "";
        $street = "";
        $city = "";
        $body = "TelNumber : ".$tel_number."<br/> Street :".$street."<br/> City :".$city."<br/> Contry :".$country."<br/> TypeOfFeedback :".$reason."<br/> Message :".$message;
        $mail_phpmailer = new PHPMailer();
        ob_start();
        $mailed = api_send_email($mail_phpmailer, $email,$full_name, $department_email, $title, $message,"");
        $output = ob_get_clean();
        file_put_contents("output.txt",$output ."\n" ,FILE_APPEND);
        unset($_SESSION['n1']);
        unset($_SESSION['n2']);
        $number1 = rand(0,10);
        $_SESSION['n1'] = $number1;
        $number2 = rand(0,10);
        $_SESSION['n2'] = $number2;
        if($mailed)
            {
                return json_encode(array("s" => '1',"n1" => $number1,"n2" => $number2,"e" => $AdminMail));
            }
        else
            {
                return json_encode(array("s" => '005',"n1" => $number1,"n2" => $number2,"e" => $AdminMail));
            }
    }

function send_email($email_infos=array("t" => "","to" => "","m" => "","ti" => ""))
{
    global $db;
    
    if(empty($db))
        $db = new db();
    
    $invalid_infos_error_json = json_encode(array("s" => '001'));
    $invalid_token_error_json = json_encode(array("s" => '002'));
    $invalid_reciever_error_json = json_encode(array("s" => '003'));
    $empty_message_error_json = json_encode(array("s" => '004'));
    
    if(!isset($email_infos['t']) || !isset($email_infos['to']) || !isset($email_infos['m']))
        return $invalid_infos_error_json;
    
    if(empty($email_infos['to']) || !check_email_address($email_infos['to']))
        return $invalid_reciever_error_json;
    
    $to = InputFilter($email_infos['to']);
    
    if(empty($email_infos['m']))
        return $empty_message_error_json;
    
    $message = InputFilter($email_infos['m']);
    
    $passed_token = InputFilter($email_infos['t']);
    $is_valid_token_row= $db->get_row("select Concat(UserName,' ',FamNAme) as full_name,UserMail as email from users where app_token = '$passed_token'");
    if(!$is_valid_token_row)
        return $invalid_token_error_json;
    
    $email = $is_valid_token_row->email;
    $full_name = $is_valid_token_row->full_name;
    
    $title = isset($email_infos['ti']) && !empty($email_infos['ti'])?InputFilter($email_infos['ti']) : "";
    
    $mail_phpmailer = new PHPMailer();
    
    $mailed = api_send_email($mail_phpmailer, $email,$full_name, $to, $title, $message,"");
    
    if($mailed)
        return json_encode(array("s" => 1));
    else
        return json_encode(array("s" => 0));
}

function get_client_ip()
    {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP']) && !empty($_SERVER['HTTP_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_X_FORWARDED']) && !empty($_SERVER['HTTP_X_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if(isset($_SERVER['HTTP_FORWARDED_FOR']) && !empty($_SERVER['HTTP_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_FORWARDED']) && !empty($_SERVER['HTTP_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if(isset($_SERVER['REMOTE_ADDR']) && !empty($_SERVER['REMOTE_ADDR']))
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }



function mobile_login($UserNickName)
    {
        global $db,$WebiteFolder, $CacheEnabled, $MainPrograms, $DefaultLang, $Lang, $PrefLang, $ActiveMessage, $UserId, $GroupId, $TimeFormat,
        $UserName, $NickName, $ParentName, $FamName, $BirthDate, $Sex, $Gmt, $Contry, $Rue,
        $AddDetails, $CodePostal, $ZipCode, $PhoneNbr, $CellNbr, $PassWord, $LastLogin, $LastIP,
        $Hobies, $Job, $Education, $PrefLang, $PrefTime, $CookieLife, $UserPic, $UserMail,
        $UserSite, $Banned, $PrefThem, $UserSign, $Points, $Active, $RegDate, $allowHtml,
        $allowBBcode, $allowSmiles, $allowAvatar, $ThemeName, $AdminFileName, $town;
        $UserNickName;
// Get user info
        $query = " select * from `users` where `NickName`='" . $UserNickName . "' ; ";
        if(empty($db))
            $db = new db();
        $LoginAsUser = $db->get_row($query);

        $UserId = $LoginAsUser->UserId;
        $GroupId = $LoginAsUser->GroupId;
        $UserName = $LoginAsUser->UserName;
        $NickName = $LoginAsUser->NickName;
        $FamName = $LoginAsUser->FamName;
        $CellNbr = $LoginAsUser->CellNbr;
        $LastLogin = $LoginAsUser->LastLogin;
        $LastIP = $LoginAsUser->LastIP;
        $PrefTime = $LoginAsUser->PrefTime;
        $CookieLife = $LoginAsUser->CookieLife;
        $UserPic = $LoginAsUser->UserPic;
        $UserMail = $LoginAsUser->UserMail;
        $RegDate = $LoginAsUser->RegDate;
        $app_token = $LoginAsUser->app_token;
        $TimeFormat = $LoginAsUser->TimeFormat;
        $Gmt = $LoginAsUser->Gmt;
        
        if(!isset($_SESSION['NickName']) || $_SESSION['NickName'] == 'Guest')
            $_SESSION['NickName'] = $NickName;
        
        $_SESSION['app_token'] = $app_token;
        if (empty($UserPic))$UserPic = 'images/avatars/noavatar.gif';

        ini_set('session.use_only_cookies', 1);
        $new_name = session_name();

        if ($NickName != 'Guest')
            {
                if (isset($_SESSION['NickName']))
                    {
                        if (!isset($_SESSION['LastLogin']))
                            {
                                $_SESSION['LastLogin'] = true;
                                $Logtimestamp = strtotime(date($TimeFormat));
                                $LogLastLogin = date($TimeFormat, $Logtimestamp + ($Gmt * 60 * 60));
                                $db->query(" update `users` set `LastLogin`='" . $LogLastLogin . "' where `app_token`='" . $app_token . "' ; ");
                            }
                    }
                $db->query(" update `users` set `PrefLang`='" . $Lang . "' where `UserId`='" . $UserId . "' ; ");
                setcookie("LastSession", session_id(), time() + $CookieLife,"/".$WebiteFolder."/");
                $query = "UPDATE `users` SET `LastSession` = '" . session_id() . "' WHERE `UserId` = '" . $UserId . "' ;";
                $db->query($query);
            }
    }

// Function to store a passed notification registration id with user have a passed token
function set_user_google_registration_id($user_infos = array('t' => '','uuid' => '','id' => '','ty' => 'a'))
    {
        if(!isset($user_infos) || (!isset($user_infos['t'])))
            return json_encode(array('s' => '001'));
        
        global $db;
        if(empty($db))
            $db = new db();
        
        file_put_contents("note.txt", implode('---',$user_infos));
        if(isset($user_infos['t']) && $user_infos['t'] == 'token')
            {
                $passed_device = InputFilter($user_infos['uuid']);
                $passed_notifications_registration_id = InputFilter($user_infos['id']);
                $is_valid_device = $db->get_var("select uuid from cyberapp where uuid = '$passed_device'");
                if($is_valid_device)
                    {
                        if($user_infos['ty'] == 'ap')
                            $notifications_query = "update cyberapp set apple_id = '$passed_notifications_registration_id' where uuid = '$is_valid_device'";
                        else
                            $notifications_query = "update cyberapp set android_id = '$passed_notifications_registration_id' where uuid = '$is_valid_device'";
                    }
                else
                    {
                        if($user_infos['ty'] == 'ap')
                            $notifications_query = "insert into cyberapp(apple_id,uuid) values('$passed_notifications_registration_id','$passed_device')";
                        else
                            $notifications_query = "insert into cyberapp(android_id,uuid) values('$passed_notifications_registration_id','$passed_device')";
                    }
            }
        else
            {
                $passed_token = InputFilter($user_infos['t']);
                $is_valid_token_var = $db->get_var("select UserId from users where app_token = '$passed_token'");
                if(!is_valid_token_var)
                    return json_encode(array('s' => '002'));
        
                $passed_notifications_registration_id = $user_infos['id'];
        
                if($user_infos['ty'] == 'ap')
                    $notifications_query = "update users set apple_id = '$passed_notifications_registration_id' where UserId = '$is_valid_token_var'";
                else
                    $notifications_query = "update users set android_id = '$passed_notifications_registration_id' where UserId = '$is_valid_token_var'";
            }
        //echo $notifications_query;
        file_put_contents("notifications.txt", $notifications_query);
        $db->query($notifications_query);
        return json_encode(array('s' => '1'));
    }
    
function api_send_email($mail,$from,$full_name,$to,$title="",$message="",$attachments="")
    {
        global $EmailMethode;
        global $SmtpHost, $SMTPusername, $SMTPpassword, $SmtpPort, $SMTPSecure;
        if (strtolower($EmailMethode) == "smtp")
            {
                $mail->IsSMTP();
                $mail->Host = $SmtpHost;
                $mail->Port = $SmtpPort;
                $mail->SMTPAuth = true;     // turn on SMTP authentication
                $mail->Username = $SMTPusername;
                $mail->Password = $SMTPpassword;
                $mail->SMTPSecure = $SMTPSecure;
            }
        else
            {
                $mail->IsSendmail();
            }//end if
        $mail->From = $from;
        $mail->FromName = $full_name;
        if (is_array($to))
            {
                $mail->AddAddress($to[0], $to[1]);
            }
        else
            {
                $mail->AddAddress($to);
            }//end if

        $mail->IsHTML(true);
        $mail->Subject = $title;
        $mail->Body = $message;

        if ($attachments != "")
            {
                if (is_array($attachments))
                    {
                        $mail->AddAttachment($attachments[0], $attachments[1]);  // optional name
                    }
                else
                    {
                        $mail->AddAttachment($attachments);  // optional name
                    }
            }

        if (!$mail->Send())
            {
                return false;
            }
        else
            {
                return true;
            }//end if
}

// Validate a passed user name if is already found in database or not
// If it is found then return a json encoded string contains key s associated with value '1'
// Else return encoded string contains key s associated with one of following values :
//      '002' : No username is passed
//      '003' : User name is not found in our database
function is_valid_user_name($user_infos = array("u" => ''))
{
    if(!isset($user_infos['u']))
        {
            return json_encode(array('s' => '002'));
        }
    else
        {
            global $db;
            if(empty($db))
                {
                    $db = new db();
                    $passed_user_name = InputFilter($user_infos['u']);
                    $is_valid_user_name = $db->get_var("select * from users where NickName = '".$passed_user_name."'");
                    if($is_valid_user_name)
                        {
                            return json_encode(array('s' => '1'));
                        }
                    else
                        {
                            return json_encode(array('s' => '003'));
                        }
                }
        }
}

//Get the country of visitor as plain text
function get_country()
{
    $ip_address = get_client_ip();
    $ip_infos_json = file_get_contents("http://freegeoip.net/json/".$ip_address);
    $ip_infos_arr = json_decode($ip_infos_json);
    return $ip_infos_arr->country_code;
}

function get_all_departments($infos = array("l" => ""))
    {
        global $db;
        if(empty($db))
            $db = new db();
        $language = !isset($user_infos['l']) || !$db->get_var("select LangName from languages where LangName = '".InputFilter($infos['l'])."'")?"Arabic" : InputFilter($infos['l']);
        
        $number1 = rand(0,10);
        $_SESSION['n1'] = $number1;
        $number2 = rand(0,10);
        $_SESSION['n2'] = $number2;
        $all_departments_res = $db->get_results("select c.IdDep,cl.DepName from contactus as c,contactuslang as cl,languages as l where l.IdLang = cl.IdLang and c.IdDep = cl.IdDep and l.LangName = '$language'");
        if(count($all_departments_res) > 0)
            {
                $all_departments = array("s" => '1',"r" => array(),"n1" => $number1,"n2" => $number2);
                $index = 0;
                foreach($all_departments_res as $department_row)
                    {
                        $all_departments['r'][$index] = array("i" => $department_row->IdDep,"n" => $department_row->DepName);
                        $index ++;
                    }
            }
        else
            {
                $all_departments = array("s" => '0',"n1" => $number1,"n2" => $number2);
            }
        return json_encode($all_departments);
    }
    
function get_user_infos($user_infos = array("t" => "","i" => "","j" => ""))
{
    global $db;
    if(empty($db))
        $db = new db();
    
    $token = isset($user_infos['t'])?InputFilter($user_infos['t']) : "";
    $id = isset($user_infos['i'])?InputFilter($user_infos['i']) : "";
    
    if(!empty($token))
        $is_valid_user_row = $db->get_row("select CellNbr as cell_number,Contry as user_country,app_token,UserId as user_id,NickName as nick_name,UserPic as user_pic,Points as user_points,UserMail as email,concat(UserName,' ',FamName) as full_name from users where app_token = '$token'");
    else if(!empty($id))
        $is_valid_user_row = $db->get_row("select CellNbr as cell_number,Contry as user_country,app_token,UserId as user_id,NickName as nick_name,UserPic as user_pic,Points as user_points,UserMail as email,concat(UserName,' ',FamName) as full_name from users where UserId = '$id'");
    else
        {
            $is_valid_user_row = false;
        }
    if($is_valid_user_row)
        {
            $user_id = $is_valid_user_row->user_id;
            $nick_name = $is_valid_user_row->nick_name;
            $email = $is_valid_user_row->email;
            $cell_number = $is_valid_user_row->cell_number;
            $full_name = $is_valid_user_row->full_name;
            $user_pic = $is_valid_user_row->user_pic;
            $user_points = $is_valid_user_row->user_points;
            $user_country = $is_valid_user_row->user_country;
            $infos_arr = array("c" => $cell_number,"co" => $user_country,"e" => $email,"f" => $full_name,"i" => $user_id,"n" => $nick_name,"p" => $user_pic,"pt" => $user_points);
        }
    else
        {
            $infos_arr = "-1";
        }
    if(isset($user_infos['j']))
        return json_encode(array("s" => '1',"i" => $infos_arr));
    else
        return $infos_arr;
}

function set_user_infos($user_infos = array("t" => "","n" => "","e" => "","p" => "","c" => ""))
{
    global $db;
    if(empty($db))
        $db = new db();
    
    $passed_token = isset($user_infos['t'])?InputFilter($user_infos['t']):"";
    if(!empty($passed_token))
    {
        $is_valid_token_row = $db->get_row("select Contry,UserId,NickName,UserMail,Password from users where app_token = '$passed_token'");
        if($is_valid_token_row)
            {
                $new_nick_name_bool = isset($user_infos['n']) && !empty($user_infos['n']) && $user_infos['n'] != $is_valid_token_row->NickName? '1' : 0;
                $new_email_bool = isset($user_infos['e']) && !empty($user_infos['e']) && $user_infos['e'] != $is_valid_token_row->UserMail? '1' : 0;
                $new_password_bool = isset($user_infos['p']) && !empty($user_infos['p']) && $user_infos['p'] != $is_valid_token_row->Password? '1' : 0;
                $new_country_bool = isset($user_infos['c']) && !empty($user_infos['c']) && $user_infos['c'] != $is_valid_token_row->Contry? '1' : 0;
                if(!$new_nick_name_bool && !$new_email_bool && !$new_password_bool && !$new_country_bool)
                    {
                        return json_encode(array("s"=>'003'));
                    }
                else
                    {
                        $is_found_nickname_var = $db->get_var("select NickName from users where NickName = '".$user_infos['n']."'");
                        $new_nick_name_sql = $new_nick_name_bool && !$is_found_nickname_var ? "NickName = '".$user_infos['n']."'," : "";
                        if(empty($new_nick_name_sql))
                            {
                                if($new_nick_name_bool === 0)
                                    $new_nick_name_bool = 2;
                                else
                                    $new_nick_name_bool = 3;
                            }
                        
                        $is_found_email_var = !$db->get_var("select UserMail from users where UserMail = '".$user_infos['e']."'");
                        $new_email_sql = $new_email_bool && !$is_found_email_var && check_email_address($user_infos['e'])?"UserMail = '".$user_infos['e']."'," : "";
                        if(empty($new_email_sql))
                            {
                                if($new_email_bool === 0)
                                    $new_email_bool = 2;
                                else
                                    $new_email_bool = 3;
                            }
                        
                        $new_password_sql = $new_password_bool ? "Password = '".md5($user_infos['p'])."'," : "";
                        if(empty($new_password_sql))
                            {
                                if($new_password_bool === 0)
                                    $new_password_bool = 2;
                                else
                                    $new_password_bool = 3;
                            }
                        
                        $new_country_sql = $new_country_bool ? "Contry = '".$user_infos['c']."'," : "";
                        if(empty($new_country_sql))
                            {
                                if($new_country_bool === 0)
                                    $new_country_bool = 2;
                                else
                                    $new_country_bool = 3;
                            }
                        if(!empty($new_nick_name_sql) || !empty($new_email_sql) || !empty($new_password_sql) || !empty($new_country_sql))
                            {
                                $news_sql = "update users set ".$new_nick_name_sql.$new_email_sql.$new_password_sql.$new_country_sql;
                                //echo $news_sql;
                                $news_sql = substr($news_sql,0,strlen($news_sql) - 1)." where app_token = '$passed_token'";
                                $db->query($news_sql);
                            }
                        return json_encode(array("s" => '1',"i" => get_user_infos(array("t" => $passed_token)),"v" => array("e" => $new_email_bool,"n" => $new_nick_name_bool,"p" => $new_password_bool,"c" => $new_country_bool)));
                    }
            }
        else
            {
                return json_encode(array("s" => '002'));
            }
    }
    else
    {
        return json_encode(array("s" => '001'));
    }
}

function get_last_reporters()
{
    global $db;
    if(empty($db))
        $db = new db();
    
    $result = array("s"=>"","r" => array());
    $last_reporters_sql = "select UserId as user_id,UserPic as user_pic,NickName as user_nickname,concat(UserName,' ',FamName) as user_full_name,Points as user_points from users Order By Points desc limit 0,10";
    $last_reporters_res = $db->get_results($last_reporters_sql);
    if(count($last_reporters_res) > 0)
        {
            $result["s"] = '1';
            $index = 0;
            foreach($last_reporters_res as $reporter_row)
                {
                    $result["r"][$index] = array("f" => $reporter_row->user_full_name,"i" => $reporter_row->user_id,"n" => $reporter_row->user_nickname,"p" => $reporter_row->user_pic,"pt" => $reporter_row->user_points);
                    $index++;
                }
        }
    else
        {
            $result["s"] = '0';
        }
    return json_encode($result);
}
?>