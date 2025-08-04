<?php

/*
 * ==========================================================
 * UPLOAD.PHP
 * ==========================================================
 *
 * Manage all uploads of front-end and admin. � 2017-2025 board.support. All rights reserved.
 *
 */
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once('../include/functions.php');
if (sb_is_cloud()) {
    $data = json_decode(openssl_decrypt(base64_decode(isset($_POST['cloud']) ? $_POST['cloud'] : $_COOKIE['sb-cloud']), 'AES-256-CBC', hash('sha256', SB_CLOUD_KEY), 0, substr(hash('sha256', 'supportboard_iv'), 0, 16)), true);
    require_once(SB_CLOUD_PATH . '/script/config/config_' . $data['token'] . '.php');
}
if (defined('SB_CROSS_DOMAIN') && SB_CROSS_DOMAIN) {
    header('Access-Control-Allow-Origin: *');
}
if (isset($_FILES['file'])) {
        $fileError = $_FILES['file']['error'];
        //if (0 < $_FILES['file']['error']) {
        if ($fileError !== UPLOAD_ERR_OK) {
            $errorMessages = [
                UPLOAD_ERR_INI_SIZE   => 'The uploaded file exceeds the upload_max_filesize directive in php.ini.',
                UPLOAD_ERR_FORM_SIZE  => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.',
                UPLOAD_ERR_PARTIAL    => 'The uploaded file was only partially uploaded.',
                UPLOAD_ERR_NO_FILE    => 'No file was uploaded.',
                UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder.',
                UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
                UPLOAD_ERR_EXTENSION  => 'A PHP extension stopped the file upload.',
            ];

            $message = $errorMessages[$fileError] ?? 'Unknown upload error.';
            die(json_encode(['error' => true, 'message' => $message]));
            //die(json_encode(['error', 'Error into upload.php file.']));
    } else {
        $file_name = sb_sanatize_file_name($_FILES['file']['name']);
        $infos = pathinfo($file_name);
        $directory_date = date('d-m-y');
        $path = '../uploads/' . $directory_date;
        $url = SB_URL . '/uploads/' . $directory_date;
        $extension = sb_isset($infos, 'extension');
        if (sb_is_allowed_extension($extension)) {
            if (defined('SB_UPLOAD_PATH') && SB_UPLOAD_PATH && defined('SB_UPLOAD_URL') && SB_UPLOAD_URL) {
                $path = SB_UPLOAD_PATH . '/' . $directory_date;
                $url = SB_UPLOAD_URL . '/' . $directory_date;
            }
            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }
            $file_name = rand(1000, 99999) . '_' . sb_string_slug($file_name);
            $path = $path . '/' . $file_name;
            $url = $url . '/' . $file_name;

            $file_size = $_FILES['file']['size']; // bytes
            $file_size_mb = round($file_size / (1024 * 1024), 2); // MB

            $response = ['success', ''];
            move_uploaded_file($_FILES['file']['tmp_name'], $path);
            if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                array_push($response, getimagesize($path));
            }
            if (sb_get_multi_setting('amazon-s3', 'amazon-s3-active') || defined('SB_CLOUD_AWS_S3')) {
                $url_aws = sb_aws_s3($path);
                if (strpos($url_aws, 'http') === 0) {
                    $url = $url_aws;
                    unlink($path);
                }
            }
            $response[1] = $url;
            $response['size_bytes'] = $file_size;
            $response['size_mb'] = $file_size_mb;
            die(json_encode($response));
        } else {
            die(json_encode(['success', 'extension_error']));
        }
    }
} else {
    die(json_encode(['error', 'Support Board Error: Key file in $_FILES not found.']));
}

?>