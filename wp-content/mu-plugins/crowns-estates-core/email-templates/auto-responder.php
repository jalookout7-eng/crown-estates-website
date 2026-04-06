<?php
// wp-content/mu-plugins/crowns-estates-core/email-templates/auto-responder.php
/** @var string $name @var string $property_name */
$site_url = home_url('/');
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Thank you — Crowns Estates</title></head>
<body style="font-family:Arial,sans-serif;color:#0A0A0A;max-width:600px;margin:0 auto;padding:20px">
  <div style="border-top:3px solid #C4973A;padding-top:20px;margin-bottom:24px">
    <h2 style="color:#0A0A0A;margin:0">Crowns Estates</h2>
  </div>
  <p>Dear <?php echo esc_html($name); ?>,</p>
  <p>Thank you for your interest<?php if ($property_name): ?> in <strong><?php echo esc_html($property_name); ?></strong><?php endif; ?>. Our team will be in touch within 24 hours.</p>
  <p>In the meantime, explore our latest opportunities:</p>
  <p><a href="<?php echo esc_url($site_url . 'projects'); ?>" style="background:#C4973A;color:#fff;padding:10px 20px;text-decoration:none;border-radius:4px;display:inline-block">View All Properties</a></p>
  <hr style="border:none;border-top:1px solid #E0E0E0;margin:24px 0">
  <p style="font-size:12px;color:#666">Crowns Estates · www.crownsestates.co.uk<br>
  This email does not constitute financial advice. Please seek independent advice before making investment decisions.</p>
</body>
</html>
