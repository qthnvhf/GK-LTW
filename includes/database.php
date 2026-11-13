<?php
// Ham ket noi MySQL, chi tao mot ket noi moi request.
function db(): mysqli
{
    static $connection = null;
    if ($connection instanceof mysqli) {
        return $connection;
    }

    try {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        $connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        $connection->set_charset('utf8mb4');
    } catch (mysqli_sql_exception $error) {
        http_response_code(500);
        exit('<meta charset="UTF-8"><div style="font:16px Arial;max-width:680px;margin:70px auto;padding:24px;border:1px solid #ddd;border-radius:16px">'
           . '<h2>Khong ket noi duoc MySQL</h2>'
           . '<p>Hay bat Apache + MySQL trong XAMPP, import file <b>database/QLNhanSu.sql</b>, '
           . 'va kiem tra <b>includes/config.php</b>.</p></div>');
    }
    return $connection;
}
