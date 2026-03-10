<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

// ── 입력값 검증 ──────────────────────────────────────
if (empty($_POST['name'])    ||
    empty($_POST['email'])   ||
    empty($_POST['phone'])   ||
    empty($_POST['message']) ||
    !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        "result"   => "error",
        "message"  => "No arguments Provided!",
        "received" => $_POST
    ]);
    exit;
}

// ── 입력값 sanitize ──────────────────────────────────
$name    = strip_tags(htmlspecialchars($_POST['name']));
$email   = strip_tags(htmlspecialchars($_POST['email']));
$phone   = strip_tags(htmlspecialchars($_POST['phone']));
$message = strip_tags(htmlspecialchars($_POST['message']));

// ── 메일 설정 ────────────────────────────────────────
$to      = 'callor@callor.com';
$subject = '=?UTF-8?B?' . base64_encode("홈페이지 문의: $name") . '?=';

$body =
    "<h2 style='color:#E85D2F;'>홈페이지 문의</h2>" .
    "<table style='border-collapse:collapse;width:100%;max-width:600px'>" .
    "<tr><td style='padding:8px;border:1px solid #ddd;background:#f9f9f9;width:100px'><b>이름</b></td><td style='padding:8px;border:1px solid #ddd'>$name</td></tr>" .
    "<tr><td style='padding:8px;border:1px solid #ddd;background:#f9f9f9'><b>이메일</b></td><td style='padding:8px;border:1px solid #ddd'>$email</td></tr>" .
    "<tr><td style='padding:8px;border:1px solid #ddd;background:#f9f9f9'><b>연락처</b></td><td style='padding:8px;border:1px solid #ddd'>$phone</td></tr>" .
    "<tr><td style='padding:8px;border:1px solid #ddd;background:#f9f9f9'><b>문의내용</b></td><td style='padding:8px;border:1px solid #ddd'>$message</td></tr>" .
    "</table>";

// ── qmail 호환 헤더 (\r\n 아닌 \n 사용) ────────────────
$headers  = "From: callor.com <callor@callor.com>\n";
$headers .= "Reply-To: $name <$email>\n";
$headers .= "MIME-Version: 1.0\n";
$headers .= "Content-Type: text/html; charset=UTF-8\n";
$headers .= "Content-Transfer-Encoding: base64\n";
$headers .= "X-Mailer: PHP/" . PHP_VERSION . "\n";

// ── 발송 (body를 base64 인코딩) ──────────────────────
$encoded_body = chunk_split(base64_encode($body));
$result = mail($to, $subject, $encoded_body, $headers);

// ── JSON 응답 ────────────────────────────────────────
header('Content-Type: application/json; charset=utf-8');
if ($result) {
    echo json_encode([
        "result"  => "success",
        "message" => "메일이 전송되었습니다."
    ]);
} else {
    $error = error_get_last();
    http_response_code(500);
    echo json_encode([
        "result"  => "error",
        "message" => "mail() 함수 실패",
        "detail"  => $error
    ]);
}
?>