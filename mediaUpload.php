<?php
include "../mainfile.php";

function is_logined() {
    return (bool)$_SESSION['xoopsUserId'];
}

function save_upload_file() {
    if (!isset($_FILES['upload']['error']) || !is_int($_FILES['upload']['error'])) {
        throw new RuntimeException('パラメータが不正です');
    }

    switch ($_FILES['upload']['error']) {
        case UPLOAD_ERR_OK: // OK
            break;
        case UPLOAD_ERR_NO_FILE:   // ファイル未選択
            throw new RuntimeException('ファイルが選択されていません');
        case UPLOAD_ERR_INI_SIZE:  // php.ini定義の最大サイズ超過
        case UPLOAD_ERR_FORM_SIZE: // フォーム定義の最大サイズ超過 (設定した場合のみ)
            throw new RuntimeException('ファイルサイズが大きすぎます');
        default:
            throw new RuntimeException('その他のエラーが発生しました');
    }

    if ($_FILES['upload']['size'] > 1000000) {
        throw new RuntimeException('ファイルサイズが大きすぎます');
    }

    $mime = shell_exec('file -bi '.escapeshellcmd($_FILES['upload']['tmp_name']));
    $mime = trim($mime);
    $mime = preg_replace("/ [^ ]*/", "", $mime);

    if (!$ext = array_search(
    $mime,
        array(
            'gif' => 'image/gif;',
            'jpg' => 'image/jpeg;',
            'png' => 'image/png;',
            'pdf' => 'application/pdf;',
        ),
        true
    )) {
        throw new RuntimeException('ファイル形式が不正です');
    }

    // ファイルデータからSHA-1ハッシュを取ってファイル名を決定し，保存する

    $path = sprintf('/uploads/%s.%s',
        sha1_file($_FILES['upload']['tmp_name']),
        $ext
    );

    if (!move_uploaded_file(
        $_FILES['upload']['tmp_name'],
        XOOPS_ROOT_PATH . $path
    )) {
        throw new RuntimeException('ファイル保存時にエラーが発生しました');
    }

    chmod($path, 0644);

    return $path;
}

if($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP', true, 400);
    return false;
}

if (!is_logined()) {
    header('HTTP', true, 403);
    return false;
}

$function_number = $_GET['CKEditorFuncNum'];

if (!(is_numeric($function_number) && intval($function_number) == $function_number)) {
    header('HTTP', true, 400);
    return false;
}

try {
    $path = save_upload_file();
} catch (RuntimeException $e) {
    header('HTTP', true, 400);
    echo $e->getMessage();
    return false;
}

$message = '';
echo "<script type='text/javascript'>window.parent.CKEDITOR.tools.callFunction(${function_number}, '${path}', '${message}');</script>";
