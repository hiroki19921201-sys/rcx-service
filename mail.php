<?php
// ===================================================
// RCX SALES お問い合わせフォーム メール送信スクリプト
// ===================================================

// --- 設定 ---
$to_email   = 'info@rcx-service.com';
$subject    = '【RCX SALES】お問い合わせがありました';
$thank_you  = 'https://rcx-service.com/thanks.html';

// --- エンコーディング設定 ---
mb_language('uni');
mb_internal_encoding('UTF-8');

// --- POST以外はトップにリダイレクト ---
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: https://rcx-service.com/');
    exit;
}

// --- ハニーポット（スパム対策）---
if (!empty($_POST['_honey'])) {
    header('Location: ' . $thank_you);
    exit;
}

// --- 入力値取得・サニタイズ ---
$company = isset($_POST['company']) ? trim(strip_tags($_POST['company'])) : '';
$name    = isset($_POST['name'])    ? trim(strip_tags($_POST['name']))    : '';
$email   = isset($_POST['email'])   ? trim(strip_tags($_POST['email']))   : '';
$phone   = isset($_POST['phone'])   ? trim(strip_tags($_POST['phone']))   : '';
$message = isset($_POST['message']) ? trim(strip_tags($_POST['message'])) : '';

// --- バリデーション ---
$errors = [];
if ($name === '') {
    $errors[] = 'お名前は必須です。';
}
if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = '有効なメールアドレスを入力してください。';
}
if ($message === '') {
    $errors[] = 'お問い合わせ内容は必須です。';
}

// エラーがあればトップに戻す
if (!empty($errors)) {
    header('Location: https://rcx-service.com/#contact');
    exit;
}

// --- 管理者宛メール本文 ---
$date_str = date('Y/m/d H:i');
$body  = "RCX SALES ウェブサイトから、お問い合わせがありました。" . "\r\n\r\n";
$body .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" . "\r\n";
$body .= "■ 会社名: " . $company . "\r\n";
$body .= "■ お名前: " . $name . "\r\n";
$body .= "■ メールアドレス: " . $email . "\r\n";
$body .= "■ 電話番号: " . $phone . "\r\n";
$body .= "■ お問い合わせ内容:" . "\r\n" . $message . "\r\n";
$body .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" . "\r\n\r\n";
$body .= "送信日時: " . $date_str . "\r\n";
$body .= "送信元IP: " . $_SERVER['REMOTE_ADDR'] . "\r\n";

// --- 管理者宛メールヘッダー ---
$headers  = "From: RCX SALES <info@rcx-service.com>" . "\r\n";
$headers .= "Reply-To: " . $name . " <" . $email . ">" . "\r\n";

// --- 管理者宛メール送信 ---
$result = mb_send_mail($to_email, $subject, $body, $headers);

// --- 自動返信メール（お客様宛） ---
if ($result) {
    $auto_subject = '【RCX SALES】お問い合わせありがとうございます';

    $auto_body  = $name . " 様" . "\r\n\r\n";
    $auto_body .= "この度はRCX SALESへお問い合わせいただき、" . "\r\n";
    $auto_body .= "誠にありがとうございます。" . "\r\n\r\n";
    $auto_body .= "お問い合わせ内容を確認のうえ、" . "\r\n";
    $auto_body .= "担当者より折り返しご連絡させていただきます。" . "\r\n";
    $auto_body .= "恐れ入りますが、しばらくお待ちくださいませ。" . "\r\n\r\n";
    $auto_body .= "─────────────────────────" . "\r\n";
    $auto_body .= "▼ お問い合わせ内容" . "\r\n";
    $auto_body .= "─────────────────────────" . "\r\n";
    $auto_body .= "会社名: " . $company . "\r\n";
    $auto_body .= "お名前: " . $name . "\r\n";
    $auto_body .= "メールアドレス: " . $email . "\r\n";
    $auto_body .= "電話番号: " . $phone . "\r\n";
    $auto_body .= "お問い合わせ内容:" . "\r\n" . $message . "\r\n";
    $auto_body .= "─────────────────────────" . "\r\n\r\n";
    $auto_body .= "※ このメールは自動送信されています。" . "\r\n";
    $auto_body .= "  このメールへの直接のご返信にはお答えできかねますので、" . "\r\n";
    $auto_body .= "  ご了承ください。" . "\r\n\r\n";
    $auto_body .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" . "\r\n";
    $auto_body .= "RCX SALES（RCXセールス株式会社）" . "\r\n";
    $auto_body .= "https://rcx-service.com/" . "\r\n";
    $auto_body .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" . "\r\n";

    $auto_headers = "From: RCX SALES <info@rcx-service.com>" . "\r\n";

    mb_send_mail($email, $auto_subject, $auto_body, $auto_headers);
}

// --- 完了ページへリダイレクト ---
header('Location: ' . $thank_you);
exit;
