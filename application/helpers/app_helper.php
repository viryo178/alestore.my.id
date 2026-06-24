<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function rupiah($value)
{
    return 'Rp '.number_format((float) $value, 0, ',', '.');
}

function h($value)
{
    return html_escape((string) $value);
}

function active_menu($segment, $class = 'active')
{
    $CI =& get_instance();
    return $CI->uri->segment(1) === $segment ? $class : 'collapsed';
}

function status_badge($status)
{
    $map = array(
        'active' => 'success',
        'available' => 'success',
        'verified' => 'primary',
        'sold' => 'secondary',
        'pending' => 'warning',
        'approved' => 'success',
        'rejected' => 'danger',
        'deactived' => 'danger',
        'unavailable' => 'secondary',
        'inactive' => 'secondary',
    );
    $status = $status ?: '-';
    $color = isset($map[$status]) ? $map[$status] : 'info';
    return '<span class="badge bg-'.$color.'">'.h(ucwords(str_replace('_', ' ', $status))).'</span>';
}

function now_sql()
{
    return (new DateTimeImmutable('now', new DateTimeZone(defined('APP_TIMEZONE') ? APP_TIMEZONE : 'Asia/Jakarta')))->format('Y-m-d H:i:s');
}

function today_sql_date()
{
    return (new DateTimeImmutable('now', new DateTimeZone(defined('APP_TIMEZONE') ? APP_TIMEZONE : 'Asia/Jakarta')))->format('Y-m-d');
}
