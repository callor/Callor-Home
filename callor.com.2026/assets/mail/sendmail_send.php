<?php
/**
 * send_mail.php
 * callor.com 홈페이지 문의 메일 발송
 * qmail 환경 / PHP mail() 사용
 */

error_reporting(E_ALL);
ini_set("display_errors", 1);

// ── CORS (필요 시) ────────────────────────────────────
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

// POST 요청만 허용
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["result" => "error", "message" => "POST 요청만 허용됩니다."]);
    exit;
}



// ── 입력값 검증 ──────────────────────────────────────
$errors = [];
if (empty($_POST['userName']))    $errors[] = "이름을 입력해 주세요.";
if (empty($_POST['userEmail']))   $errors[] = "이메일을 입력해 주세요.";
if (empty($_POST['userTel']))   $errors[] = "연락처를 입력해 주세요.";
if (empty($_POST['description'])) $errors[] = "문의 내용을 입력해 주세요.";
if (!empty($_POST['userEmail']) && !filter_var($_POST['userEmail'], FILTER_VALIDATE_EMAIL)) {
    $errors[] = "올바른 이메일 형식이 아닙니다.";
}

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode([
        "result"  => "error",
        "message" => implode(" ", $errors)
    ]);
    exit;
}


// ── 입력값 sanitize ──────────────────────────────────
$userName    = strip_tags(htmlspecialchars(trim($_POST['userName']),    ENT_QUOTES, 'UTF-8'));
$userEmail   = strip_tags(htmlspecialchars(trim($_POST['userEmail']),   ENT_QUOTES, 'UTF-8'));
$userTel   = strip_tags(htmlspecialchars(trim($_POST['userTel']),   ENT_QUOTES, 'UTF-8'));
$description = strip_tags(htmlspecialchars(trim($_POST['description']), ENT_QUOTES, 'UTF-8'));

// ── 수신 메일 주소 ────────────────────────────────────
$to = 'callor@daum.net';

// ── 제목 (UTF-8 base64 인코딩) ───────────────────────
$subject = '=?UTF-8?B?' . base64_encode("[callor.com] 홈페이지 문의 - {$userName}") . '?=';

// ── HTML 메일 본문 ────────────────────────────────────
$now  = date('Y-m-d H:i:s');
$ip   = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

$body = "
<!DOCTYPE html>
<html lang='ko'>
<head><meta charset='UTF-8'></head>
<body style='margin:0;padding:0;background:#f4f4f4;font-family:Arial,sans-serif'>
  <table width='100%' cellpadding='0' cellspacing='0' style='background:#f4f4f4;padding:30px 0'>
    <tr><td align='center'>
      <table width='600' cellpadding='0' cellspacing='0' style='background:#ffffff;border-radius:6px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,.1)'>

        <!-- 헤더 -->
        <tr>
          <td style='background:#E85D2F;padding:28px 36px'>
            <h1 style='margin:0;color:#ffffff;font-size:22px;letter-spacing:-.5px'>callor.com</h1>
            <p style='margin:6px 0 0;color:rgba(255,255,255,.8);font-size:13px'>홈페이지 문의가 접수되었습니다</p>
          </td>
        </tr>

        <!-- 내용 테이블 -->
        <tr>
          <td style='padding:20px 36px 28px'>
            <table width='100%' cellpadding='0' cellspacing='0' style='border-collapse:collapse'>
              <tr>
                <td style='padding:12px 14px;background:#f9f9f9;border:1px solid #e8e8e8;width:90px;font-size:13px;font-weight:bold;color:#555'>이름</td>
                <td style='padding:12px 14px;border:1px solid #e8e8e8;font-size:14px;color:#222'>{$userName}</td>
              </tr>
              <tr>
                <td style='padding:12px 14px;background:#f9f9f9;border:1px solid #e8e8e8;font-size:13px;font-weight:bold;color:#555'>이메일</td>
                <td style='padding:12px 14px;border:1px solid #e8e8e8;font-size:14px;color:#222'><a href='mailto:{$userEmail}' style='color:#E85D2F;text-decoration:none'>{$email}</a></td>
              </tr>
              <tr>
                <td style='padding:12px 14px;background:#f9f9f9;border:1px solid #e8e8e8;font-size:13px;font-weight:bold;color:#555'>연락처</td>
                <td style='padding:12px 14px;border:1px solid #e8e8e8;font-size:14px;color:#222'>{$userTel}</td>
              </tr>
              <tr>
                <td style='padding:12px 14px;background:#f9f9f9;border:1px solid #e8e8e8;font-size:13px;font-weight:bold;color:#555;vertical-align:top'>문의내용</td>
                <td style='padding:12px 14px;border:1px solid #e8e8e8;font-size:14px;color:#222;line-height:1.7'>{$description}</td>
              </tr>
            </table>
          </td>
        </tr>

        <!-- 푸터 -->
        <tr>
          <td style='background:#f9f9f9;padding:16px 36px;border-top:1px solid #eee'>
            <p style='margin:0;font-size:11px;color:#aaa'>접수 시각: {$now} &nbsp;|&nbsp; IP: {$ip}</p>
            <p style='margin:4px 0 0;font-size:11px;color:#aaa'>callor.com &mdash; callor@callor.com</p>
          </td>
        </tr>
      </table>
    </td></tr>
  </table>
</body>
</html>";

// ── qmail 호환 헤더 (\r\n 아닌 \n) ───────────────────
$headers  = "From: callor.com <callor@callor.com>\n";
$headers .= "Reply-To: {$userName} <{$userEmail}>\n";
$headers .= "MIME-Version: 1.0\n";
$headers .= "Content-Type: text/html; charset=UTF-8\n";
$headers .= "Content-Transfer-Encoding: base64\n";
$headers .= "X-Mailer: PHP/" . PHP_VERSION . "\n";

// ── 발송 ─────────────────────────────────────────────
$encoded_body = chunk_split(base64_encode($body));
$result = mail($to, $subject, $encoded_body, $headers);

// ── JSON 응답 ─────────────────────────────────────────
if ($result) {
    http_response_code(200);
    echo json_encode([
        "result"  => "success",
        "message" => "문의가 접수되었습니다. 빠른 시간 내 답변드리겠습니다."
    ]);
} else {
    $error = error_get_last();
    http_response_code(500);
    echo json_encode([
        "result"  => "error",
        "message" => "메일 발송에 실패했습니다. 직접 callor@callor.com 으로 문의해 주세요.",
        "detail"  => $error['message'] ?? 'unknown error'
    ]);
}
?>