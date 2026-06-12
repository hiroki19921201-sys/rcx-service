<?php
// ===================================================
// RCX SALES 製品資料ダウンロード＋リード通知スクリプト
// ===================================================

// --- 設定 ---
$to_email  = 'info@rcx-service.com';
$subject   = '【RCX SALES】製品資料がダウンロードされました';
$pdf_file  = __DIR__ . '/docs/RCX_SALES_product.pdf';

// --- エンコーディング設定 ---
mb_language('uni');
mb_internal_encoding('UTF-8');

// --- POST以外は拒否 ---
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// --- ハニーポット ---
if (!empty($_POST['_honey'])) {
    echo json_encode(['success' => true]);
    exit;
}

// --- 入力値取得 ---
$email = isset($_POST['email']) ? trim(strip_tags($_POST['email'])) : '';
$phone = isset($_POST['phone']) ? trim(strip_tags($_POST['phone'])) : '';
    $company = isset($_POST['company']) ? trim(strip_tags($_POST['company'])) : '';
    $name    = isset($_POST['name'])    ? trim(strip_tags($_POST['name']))    : '';

// --- バリデーション ---
if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'メールアドレスを正しく入力してください']);
    exit;
}


    if ($name === '') {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'お名前を入力してください']);
        exit;
    }
// --- 通知メール送信 ---
$date_str = date('Y/m/d H:i');
$body  = "製品資料のダウンロードがありました。\r\n\r\n";
$body .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\r\n";
    $body .= "■ 会社名: " . $company . "\r\n";
    $body .= "■ お名前: " . $name . "\r\n";
$body .= "■ メールアドレス: " . $email . "\r\n";
$body .= "■ 電話番号: " . $phone . "\r\n";
$body .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\r\n\r\n";
$body .= "ダウンロード日時: " . $date_str . "\r\n";
$body .= "送信元IP: " . $_SERVER['REMOTE_ADDR'] . "\r\n";

$headers  = "From: RCX SALES <info@rcx-service.com>\r\n";
$headers .= "Reply-To: " . $email . "\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

    $mail_result = mb_send_mail($to_email, $subject, $body, $headers);
    if (!$mail_result) { error_log("download.php: mail send failed"); }

// --- PDFファイルをダウンロードさせる ---
if (file_exists($pdf_file)) {
    while (ob_get_level()) {
        ob_end_clean();
    }
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="RCX_SALES_product.pdf"');
    header('Content-Length: ' . filesize($pdf_file));
    header('Cache-Control: no-cache, must-revalidate');
    header('Pragma: no-cache');
    readfile($pdf_file);
    exit;
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'ファイルが見つかりません']);
    exit;
}
