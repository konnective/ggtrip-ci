<?php defined('BASEPATH') OR exit('No direct script access allowed');

function my_crypt($string, $action = 'e' )
{
    $secret_key = md5(APP_NAME).'_key';
    $secret_iv = md5(APP_NAME).'_iv';

    $output = false;
    $encrypt_method = "AES-256-CBC";
    $key = hash( 'sha256', $secret_key );
    $iv = substr( hash( 'sha256', $secret_iv ), 0, 16 );

    if( $action == 'e' ) {
        $output = base64_encode( openssl_encrypt( $string, $encrypt_method, $key, 0, $iv ) );
    }
    else if( $action == 'd' ){
        $output = openssl_decrypt( base64_decode( $string ), $encrypt_method, $key, 0, $iv );
    }

    return $output;
}

function re($array='')
{
    $CI =& get_instance();
    echo "<pre>";
    print_r($array);
    echo "</pre>";
    exit;
}

function e_id($id)
{
    return my_crypt($id);
}

function d_id($id)
{
    return my_crypt($id, 'd');
}

if ( ! function_exists('check_ajax'))
{
    function check_ajax()
    {
        $CI =& get_instance();
        if (!$CI->input->is_ajax_request())
            die;
    }
}

if ( ! function_exists('script'))
{
    function script($url='', $type='application/javascript')
    {
        return "\n<script src=\"".site_url($url)."\" type=\"$type\"></script>\n";
    }
}

if ( ! function_exists('responseMsg'))
{
    function responseMsg($success, $message, $redirect = null, $validate = null, $data = null)
    {
        $response = [
            'error'    => $success ? false : true,
            'message'  => $message
        ];

        if($redirect) {
            $response['redirect'] = $redirect === true ? get_instance()->input->server('HTTP_REFERER') : $redirect;
        }

        if($validate) $response['validate'] = $validate;

        if($data) {
            $response['data'] = $data;
        }

        die(json_encode($response));
    }
}

function status($status){
    switch ($status) {
        case 'Rating given':
        case 'Screenshot Uploaded':
            $class = 'info';
            break;
        case 'Created':
        case 'Assigned':
            $class = 'primary';
            break;
        case 'Email Sent':
        case 'Approved':
            $class = 'success';
            break;
        case 'Rejected':
            $class = 'danger';
            break;
        default:
            $class = 'warning';
            break;
    } 

    return '<span class="badge outline-badge-'.$class.'">'.$status.'</span>';
}

function show_file_on_browser(&$token, $fileType, $filePath, $download = 'inline', $file = ''){
    $CI =& get_instance();

    if(!empty($token)){
       $filePath = $CI->path.$filePath.'/';

        switch ($fileType) {
            default:
                $getFileDetails = $CI->generalmodel->get('client_files', 'client_id, filename', array('token' => $token, 'is_delete' => 0));
                break;
        }

        if(!empty($getFileDetails)){
            switch ($fileType) {
                case 'enquiry':
                    $filePath = $filePath.md5($getFileDetails['enquiry_id']).'/'.$getFileDetails['filename'];
                    break;
                
                default:
                    $filePath = $filePath.md5($getFileDetails['client_id']).'/'.$getFileDetails['filename'];
                    break;
            }

            if (file_exists($filePath)) {
                // Get the file's MIME type
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mimeType = finfo_file($finfo, $filePath);
                finfo_close($finfo);

                // Set the appropriate headers
                header('Content-Type: ' . $mimeType);
                header('Content-Length: ' . filesize($filePath));
                header('Content-Disposition: '.$download.'; filename="' . basename($filePath) . '"');

                // Read the file and output its contents
                readfile($filePath);
            } else {
                flashMsg('File not found!', $CI->input->server('HTTP_REFERER'));
            }
        } else {
            flashMsg('File details not found!', $CI->input->server('HTTP_REFERER'));
        }
    } else {
        flashMsg('It seems like token is missing!', $CI->input->server('HTTP_REFERER'));
    }
}

function show_svg($icon, $stroke = ''){

    switch ($icon) {
        case 'delete':
            return '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="'.(!empty($stroke) ? $stroke : '#e7515a').'" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x-circle table-cancel">
                        <circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15">
                        </line><line x1="9" y1="9" x2="15" y2="15"></line>
                    </svg>';
            break;
        case 'trash':
            return '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="'.(!empty($stroke) ? $stroke : 'currentColor').'" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash">
                        <polyline points="3 6 5 6 21 6"></polyline>
                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                    </svg>';
            break;
        case 'archive':
            return '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="'.(!empty($stroke) ? $stroke : '#805dca').'" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-archive">
                        <polyline points="21 8 21 21 3 21 3 8"></polyline>
                        <rect x="1" y="3" width="22" height="5"></rect>
                        <line x1="10" y1="12" x2="14" y2="12"></line>
                        <line x1="12" y1="10" x2="12" y2="16"></line>
                    </svg>';
            break;
        case 'restore':
            return '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="'.(!empty($stroke) ? $stroke : '#4361ee').'" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-reply">
                        <polyline points="9 17 4 12 9 7"></polyline>
                        <path d="M20 18v-1a4 4 0 0 0-4-4H4"></path>
                    </svg>';
                    break;
        case 'edit':
            return '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="'.(!empty($stroke) ? $stroke : '#4361ee').'" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-3">
                        <path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                    </svg>';
            break;
        case 'files':
            return '<svg viewBox="0 0 24 24" width="24" height="24" stroke="'.(!empty($stroke) ? $stroke : '#1abc9c').'" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline>
                    </svg>';
            break;
        case 'notes':
            return '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="'.(!empty($stroke) ? $stroke : 'currentColor').'" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                    </svg>';
            break;
        case 'users':
            return '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="'.(!empty($stroke) ? $stroke : '#4361ee').'" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-users">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>';
            break;
        case 'user':
            return '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="'.(!empty($stroke) ? $stroke : 'currentColor').'" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>';
            break;
        case 'shifts':
            return '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="'.(!empty($stroke) ? $stroke : 'currentColor').'" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-layers"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                        <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                    </svg>';
            break;
        case 'download':
            return '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="'.(!empty($stroke) ? $stroke : 'currentColor').'" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-download">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="7 10 12 15 17 10"></polyline>
                        <line x1="12" y1="15" x2="12" y2="3"></line>
                    </svg>';
            break;
        case 'filter':
            return '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="'.(!empty($stroke) ? $stroke : 'currentColor').'" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-filter">
                        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                    </svg>';
            break;
        case 'calander':
            return '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="'.(!empty($stroke) ? $stroke : 'currentColor').'" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-calendar"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>';
            break;
        case 'clipboard':
            return '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="'.(!empty($stroke) ? $stroke : 'currentColor').'" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-clipboard"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect></svg>';
            break;
        
        default:
            return '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="'.(!empty($stroke) ? $stroke : '#1abc9c').'" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>';
            break;
    }
}

function get_link($type, $hasPermission, $link, $hidden = [], $name = '', $modalTitle = false, $icon = null)
{
    if($hasPermission) {
        switch ($type) {
            case 'edit':
                return anchor($link, '<i class="far fa-pencil-alt mr-2"></i> Edit', 'class="bs-tooltips dropdown-item" data-bs-toggle="tooltip" data-bs-placement="top" data-original-title="Edit" aria-label="Edit" data-bs-original-title="Edit"');
                break;
            case 'form':
                return form_open($link, 'method="GET"').
                            '<a class="bs-tooltips dropdown-item btn-get-form" data-bs-toggle="tooltip" data-bs-placement="top" data-original-title="'.$modalTitle.'" aria-label="'.$modalTitle.'" data-bs-original-title="'.$modalTitle.'" href="javascript:;">
                                <i class="far fa-'.(!empty($icon) ? $icon : 'eye').' mr-2"></i> '.$name.'
                            </a>'.
                       form_close();
                break;
            case 'delete':
                return form_open($link, '', $hidden).
                            '<a class="bs-tooltips dropdown-item delete-archive-item" href="javascript:;" data-bs-toggle="tooltip" data-bs-placement="top" data-original-title="Delete" aria-label="Delete" data-bs-original-title="Delete">
                                <i class="far fa-trash-alt mr-2"></i> Delete
                            </a>'.
                       form_close();
                break;
            case 'cancel':
                return form_open($link, '', $hidden).
                            '<a class="bs-tooltips dropdown-item delete-archive-item" href="javascript:;" data-bs-toggle="tooltip" data-bs-placement="top" data-original-title="Cancel" aria-label="Cancel" data-bs-original-title="Cancel">
                                <i class="far fa-times mr-2"></i> Cancel
                            </a>'.
                       form_close();
                break;
            case 'archive':
                return form_open($link, '', $hidden).
                            '<a class="bs-tooltips dropdown-item delete-archive-item" href="javascript:;" data-bs-toggle="tooltip" data-bs-placement="top" data-original-title="Archive" aria-label="Archive" data-bs-original-title="Archive">
                                <i class="far fa-archive mr-2"></i> Archive
                            </a>'.
                       form_close();
                break;
            case 'restore':
                return form_open($link, '', $hidden).
                            '<a class="bs-tooltips dropdown-item delete-archive-item" href="javascript:;" data-bs-toggle="tooltip" data-bs-placement="top" data-original-title="Restore" aria-label="Restore" data-bs-original-title="Restore">
                                <i class="far fa-reply mr-2"></i> Restore
                            </a>'.
                       form_close();
                break;
            case 'approve':
                return form_open($link, '', $hidden).
                            '<a class="bs-tooltips dropdown-item delete-archive-item" href="javascript:;" data-bs-toggle="tooltip" data-bs-placement="top" data-original-title="Approve" aria-label="Approve" data-bs-original-title="Approve">
                                <i class="far fa-thumbs-up mr-2"></i> Approve
                            </a>'.
                        form_close();
                break;
            case 'reject':
                return form_open($link, '', $hidden).
                            '<a class="bs-tooltips dropdown-item delete-archive-item" href="javascript:;" data-bs-toggle="tooltip" data-bs-placement="top" data-original-title="Reject" aria-label="Reject" data-bs-original-title="Reject">
                                <i class="far fa-thumbs-down mr-2"></i> Reject
                            </a>'.
                        form_close();
                break;
            case 'files':
                return anchor($link, '<i class="far fa-file mr-2"></i> '.(!empty($name) ? $name : 'View Files'), 'class="bs-tooltips dropdown-item" data-bs-toggle="tooltip" data-bs-placement="top" data-original-title="View Files" aria-label="View Files" data-bs-original-title="View Files"');
                break;
            case 'download':
                return anchor($link.'/download', '<i class="far fa-download mr-2"></i> Download', 'class="bs-tooltips dropdown-item" data-bs-toggle="tooltip" data-bs-placement="top" data-original-title="Download" aria-label="Download" data-bs-original-title="Download"');
                break;
            case 'notes':
                return anchor($link, '<i class="far fa-sticky-note mr-2"></i> View Notes', 'class="bs-tooltips dropdown-item" data-bs-toggle="tooltip" data-bs-placement="top" data-original-title="View Notes" aria-label="View Notes" data-bs-original-title="View Notes"');
                break;
            case 'shifts':
                return anchor($link, '<i class="far fa-briefcase mr-2"></i> View All Shifts', 'class="bs-tooltips dropdown-item" data-bs-toggle="tooltip" data-bs-placement="top" data-original-title="View All Shift" aria-label="View All Shift" data-bs-original-title="View All Shift"');
                break;
            case 'filters':
                return anchor($link, '<i class="far fa-briefcase mr-2"></i> View All Shifts', 'class="bs-tooltips dropdown-item" data-bs-toggle="tooltip" data-bs-placement="top" data-original-title="View All Shift" aria-label="View All Shift" data-bs-original-title="View All Shift"');
                break;
            default:
                if($modalTitle) {
                    return form_open($link, 'method="GET"').
                                '<a class="bs-tooltips dropdown-item btn-modal-item" data-modal-title="'.$modalTitle.'" data-bs-toggle="tooltip" data-bs-placement="top" data-original-title="'.$modalTitle.'" aria-label="'.$modalTitle.'" data-bs-original-title="'.$modalTitle.'" href="javascript:;">
                                    <i class="far fa-'.(!empty($icon) ? $icon : 'eye').' mr-2"></i> '.(!empty($name) ? $name : 'View').'
                                </a>'.
                        form_close();
                } else {
                    return anchor($link, '<i class="far fa-eye mr-2"></i> View', 'class="bs-tooltips dropdown-item" data-bs-toggle="tooltip" data-bs-placement="top" data-original-title="View" aria-label="View" data-bs-original-title="View"');
                }
                break;
        }
    } else {
        return '';
    }
}

function get_badge($html, $badge){
    return '<span class="badge badge-'.$badge.' badge-pills">'.$html.'</span>';
}

function show_actions($action = ''){
    return '<div class="btn-group">
                <a class="dropdown-toggle dropdown-toggle-split" href="#" role="button" id="dropdownMenuLink'.rand(10000, 99999).'" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="true" data-reference="parent">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-more-horizontal"><circle cx="12" cy="12" r="1"></circle><circle cx="19" cy="12" r="1"></circle><circle cx="5" cy="12" r="1"></circle></svg>
                </a>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuLink'.rand(10000, 99999).'">
                    '.$action.'
                </div>
            </div>';
}

if ( ! function_exists('encr_password'))
{
    function encr_password($password)
    {
        $salt = bin2hex(random_bytes(32));
        $hashedPassword = password_hash($password . $salt, PASSWORD_BCRYPT, ['cost' => 12]);

        return ['salt' => $salt, 'hashedPassword' => $hashedPassword];
    }
}

function admin($uri='')
{
    return $uri;
}

function get_post_data()
{
    $CI =& get_instance();
    $postArray = [];

    if(!empty($CI->input->post())){
        foreach($CI->input->post() as $k => $v){
            if(in_array($k, ['id'])) continue;

            if(in_array($k, ['password'])) {
                if(!empty($v)) {
                    $hp = encr_password($v);
                    $postArray['password_salt'] = $hp['salt'];
                    $postArray['password'] = $hp['hashedPassword'];
                }
            } else if(in_array($k, ['role_id', 'client_id', 'invite_id', 'rating_platform_id', 'review_id', 'property_id'])) {
                $postArray[$k] = !empty($v) ? d_id($v) : 0;
            } else if(in_array($k, ['date_of_birth', 'next_follow_date', 'expiry_date'])) {
                $postArray[$k] = !empty($v) ? convert_date($v) : '';
            } else if(in_array($k, ['assigned_to', 'rating_platform_ids'])) {
                $postArray[$k] = !empty($v) ? implode(',', array_map('d_id', $v)) : '';
            } else if(in_array($k, ['property_ids', 'permissions', 'freelancer_ids', 'selected_items'])) {
                $postArray[$k] = !empty($v) ? array_map('d_id', $v) : '';
            } else if(in_array($k, ['passenger_details', 'card_details', 'other_services', 'type_of_cab', 'room_type'])) {
                $postArray[$k] = !empty($v) ? json_encode($v) : '';
            } else {
                $postArray[$k] = $v;
            }
        }
    }

    return $postArray;
}

function create_directories($path){
    $directories = explode('/', rtrim($path, '/'));
    $currentPath = FCPATH;

    foreach ($directories as $directory) {
        $currentPath .= $directory . '/';
        
        // Check if the directory does not exist and create it
        if (!is_dir($currentPath)) {
            mkdir($currentPath, 0777, true);
        }

        // Path to the index.html file in the current directory
        $htmlFilePath = $currentPath . 'index.html';
        
        // Content for the index.html file
        $htmlFileContent = "<!DOCTYPE html><html><head><title>404 Not found</title></head><body><p>Page Not found.</p></body></html>";
        
        // Check if index.html file does not exist and create it
        if (!file_exists($htmlFilePath)) {
            file_put_contents($htmlFilePath, $htmlFileContent);
        }
    }
}

function role_conditions(){
    $CI =& get_instance();
    $role_conditions = [];

    if(!$CI->user->is_super_admin) {
        $role_conditions['is_super_admin'] = 0;

        if(!$CI->user->is_admin) {
            $role_conditions['is_admin'] = 0;

            if(!$CI->user->is_staff) {
                $role_conditions['is_staff'] = 0;

                if(!$CI->user->is_client) {
                    $role_conditions['is_client'] = 0;
                }
            }
        }
    }

    return $role_conditions;
}

function is_staff(){
    $CI =& get_instance();

    if($CI->user->is_super_admin || $CI->user->is_admin || $CI->user->is_staff) {
        return true;
    }

    return false;
}

function self_data_only(){
    $CI =& get_instance();

    if(!empty($CI->user) && $CI->user->is_staff) {
        return true;
    }

    return false;
}

function randomPassword($length = 12) {
    $lowercase = 'abcdefghijklmnopqrstuvwxyz';
    $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $digits = '1234567890';
    $special = '@$!%*?&#';
    $allCharacters = $lowercase . $uppercase . $digits . $special;

    $password = [];
    $password[] = $lowercase[random_int(0, strlen($lowercase) - 1)];
    $password[] = $uppercase[random_int(0, strlen($uppercase) - 1)];
    $password[] = $digits[random_int(0, strlen($digits) - 1)];
    $password[] = $special[random_int(0, strlen($special) - 1)];

    for ($i = 4; $i < $length; $i++) {
        $password[] = $allCharacters[random_int(0, strlen($allCharacters) - 1)];
    }

    shuffle($password);

    return implode($password);
}

function show_date($date) {
    return $date ? date('d/m/Y', strtotime($date)) : '-';
}

function convert_date($date) {
    return $date ? date('Y-m-d', strtotime(str_replace('/', '-', $date))) : '';
}

function show_amount($amount) {
    return "$ " . number_format($amount ?? 0);
}