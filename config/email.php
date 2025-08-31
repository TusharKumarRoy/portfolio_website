
<?php
$env = require __DIR__ . '/../.env.php';

return [
    // Email addresses
    'from_email' => $env['SMTP_USER'] ?? 'tusharkumarroy.portfolio@gmail.com',
    'from_name' => 'Tushar Kumar Roy - Portfolio',
    
    // SMTP settings (read from environment)
    'smtp_host' => $env['SMTP_HOST'] ?? 'smtp.gmail.com',
    'smtp_port' => $env['SMTP_PORT'] ?? 587,
    'smtp_user' => $env['SMTP_USER'] ?? 'tusharkumarroy.portfolio@gmail.com',
    'smtp_pass' => $env['SMTP_PASS'] ?? '',
    
    // Additional settings
    'timeout' => 60,
    'smtp_secure' => 'tls',
];