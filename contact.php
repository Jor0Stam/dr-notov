<?php
// ==============================
// Sunny Smile Dental - contact.php
// ==============================
//
// Purpose: Handle contact form submission securely.
// Works on SiteGround using PHP's mail() function.
// You can later switch to PHPMailer/SMTP for reliability.
//
// ------------------------------

// Return JSON responses
header('Content-Type: application/json');

// Allow preflight for CORS (optional if hosting same domain)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  header('Access-Control-Allow-Methods: POST, OPTIONS');
  header('Access-Control-Allow-Headers: Content-Type');
  exit(0);
}

// Reject anything except POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo json_encode(['message' => 'Method not allowed']);
  exit;
}

// --- Anti-spam honeypot ---
if (!empty($_POST['website'])) {
  // If the hidden "website" field is filled, it’s a bot
  echo json_encode(['message' => 'Thanks!']);
  exit;
}

// --- Sanitize helper ---
function clean($v) {
  return trim(strip_tags($v ?? ''));
}

// --- Collect inputs ---
$name = clean($_POST['name'] ?? '');
$phone = clean($_POST['phone'] ?? '');
$email = clean($_POST['email'] ?? '');
$service = clean($_POST['service'] ?? '');
$message = clean($_POST['message'] ?? '');
$consent = isset($_POST['consent']);

// --- Validate required fields ---
if (!$name || !$phone || !$consent) {
  http_response_code(422);
  echo json_encode(['message' => 'Please provide name, phone and consent.']);
  exit;
}

if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
  http_response_code(422);
  echo json_encode(['message' => 'Invalid email address.']);
  exit;
}

// --- Configure recipient ---
$to = 'appointments@example.com'; // <-- change this to your actual mailbox
$subject = 'New appointment request from ' . $name;

// --- Compose email body ---
$bodyLines = [
  "Name: $name",
  "Phone: $phone",
  "Email: " . ($email ?: '—'),
  "Service: " . ($service ?: '—'),
  "Message: " . ($message ?: '—'),
  "Consent: " . ($consent ? 'Yes' : 'No'),
  "IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'),
  "When: " . gmdate('c')
];
$body = implode("\r\n", $bodyLines);

// --- Headers ---
$headers = "From: Website <no-reply@example.com>\r\n"; // change domain to match your site
if ($email) {
  $headers .= "Reply-To: $email\r\n";
}
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

// --- Send email ---
$sent = @mail($to, $subject, $body, $
