<?php
// Generate proper bcrypt hash for Laravel
echo "Password: stms123\n";
echo "Bcrypt hash: " . password_hash('stms123', PASSWORD_BCRYPT) . "\n";
echo "Length: " . strlen(password_hash('stms123', PASSWORD_BCRYPT)) . "\n";
?>
