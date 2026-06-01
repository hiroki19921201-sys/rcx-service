<?php
// ===================================================
// RCX SALES お問い合わせフォーム メール送信スクリプト
// ===================================================

// --- 設定 ---
$to_email   = 'info@rcx-service.com';
$subject    = '【RCX SALES】お問い合わせがありました';
$thank_you  = 'https://rcx-service.com/thanks.html';

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

// --- メール本文作成 ---
$body = "";
$body .= "RCX SALES ウェブサイトから、お問い合わせがありました。\n\n";
$body .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
$body .= "■ 会社名\n" . $company . "\n\n";
$body .= "■ お名前\n" . $name . "\n\n";
$body .= "■ メールアドレス\n" . $email . "\n\n";
$body .= "■ 電話番号\n" . $phone . "\n\n";
$body .= "■ お問い合わせ内容\n" . $message . "\n";
$body .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
$body .= "送信日時: " . date('Y年m月d日 H:i') . "\n";
$body .= "送信元IP: " . $_SERVER['REMOTE_ADDR'] . "\n";

// --- メールヘッダー ---
mb_language('ja');
mb_internal_encoding('UTF-8');

$headers  = "From: RCX SALES <info@rcx-service.com>\r\n";
$headers .= "Reply-To: " . $name . " <" . $email . ">\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

// --- メール送信 ---
$result = mb_send_mail($to_email, $subject, $body, $headers);

// --- 自動返信メール（お客様宛） ---
if ($result) {
    $auto_subject = '【RCX SALES】お問い合わせありがとうございます';
    $auto_body = "";
    $auto_body .= $name . " 様\n\n";
    $auto_body .= "この度はRCX SALESにお問い合わせいただき、\n";
    $auto_body .= "誠にありがとうございます。\n\n";
    $auto_body .= "以下の内容で受け付けました。\n";
    $auto_body .= "担当者より折り返しご連絡いたしますので、\n";
    $auto_body .= "しばらくお待ちください。\n\n";
    $auto_body .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    $auto_body .= "■ 会社名: " . $company . "\n";
    $auto_body .= "■ お名前: " . $name . "\n";
    $auto_body .= "■ メールアドレス: " . $email . "\n";
    $auto_body .= "■ 電話番号: " . $phone . "\n\n";
    $auto_body .= "■ お問い合わせ内容:\n" . $message . "\n";
    $auto_body .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    $auto_body .= "※ このメールは自動送信です。\n";
    $auto_body .= "  このメールに直接ご返信いただいても対応できません。\n\n";
    $auto_body .= "──────────────────────────\n";
    $auto_body .= "RCX SALES（RCXセールス株式会社）\n";
    $auto_body .= "URL: https://rcx-service.com/\n";
    $auto_body .= "──────────────────────────\n";

    $auto_headers = "From: RCX SALES <info@rcx-service.com>\r\n";
    $auto_headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

    mb_send_mail($email, $auto_subject, $auto_body, $auto_headers);
}

// --- 完了ページへリダイレクト ---
header('Location: ' . $thank_you);
exit;
