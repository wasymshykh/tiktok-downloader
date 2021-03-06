<?php

function normal_text($data)
{
    if (gettype($data) !== "array") {
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }
    return '';
}

function normal_text_back($text)
{
    if (gettype($text) !== "array") {
        return trim(htmlspecialchars_decode(trim($text), ENT_QUOTES), ' ');
    }
    return '';
}

function normal_date($date, $format = 'M d, Y h:i A')
{
    $d = date_create($date);
    return date_format($d, $format);
}

function current_date($format = 'Y-m-d H:i:s')
{
    return date($format);
}

function normal_to_db_date($date, $format = 'Y-m-d H:i:s')
{
    $d = date_create($date);
    return date_format($d, $format);
}

function go ($URL)
{
    header("location: $URL");
    die();
}

function json_response ($status_code, $type, $message)
{
    http_response_code($status_code);
    echo json_encode(['code' => $status_code, 'type' => $type, 'message' => $message]);
    die();
}

function content_type_to_name ($content_type)
{
    if ($content_type === 'hashtag' || $content_type === "") {
        return 'hashtag';
    }
    if ($content_type === 'profile') {
        return 'profile';
    }
    if ($content_type === 'video') {
        return 'video';
    }
}

function content_type_to_fa ($content_type)
{
    if ($content_type === 'hashtag' || $content_type === "") {
        return 'hashtag';
    }
    if ($content_type === 'profile') {
        return 'id-badge';
    }
    if ($content_type === 'video') {
        return 'video';
    }
}
