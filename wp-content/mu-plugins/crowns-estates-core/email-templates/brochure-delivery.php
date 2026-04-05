<?php
// wp-content/mu-plugins/crowns-estates-core/email-templates/brochure-delivery.php
/** @var string $name @var string $property_name @var string $download_url */
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Your Brochure — Crowns Estates</title></head>
<body style="font-family:Arial,sans-serif;color:#0A0A0A;max-width:600px;margin:0 auto;padding:20px">
  <div style="border-top:3px solid #C4973A;padding-top:20px;margin-bottom:24px">
    <h2 style="color:#0A0A0A;margin:0">Crowns Estates</h2>
  </div>
  <p>Dear <?php echo esc_html($name); ?>,</p>
  <p>Your brochure for <strong><?php echo esc_html($property_name); ?></strong> is ready to download. This link is valid for 24 hours.</p>
  <p><a href="<?php echo esc_url($download_url); ?>" style="background:#C4973A;color:#fff;padding:12px 24px;text-decoration:none;border-radius:4px;display:inline-block;font-weight:bold">Download Brochure</a></p>
  <p style="font-size:13px;color:#666">If the button above doesn't work, copy and paste this link into your browser:<br>
  <a href="<?php echo esc_url($download_url); ?>"><?php echo esc_url($download_url); ?></a></p>
  <hr style="border:none;border-top:1px solid #E0E0E0;margin:24px 0">
  <p style="font-size:12px;color:#666">Crowns Estates · www.crownsestates.co.uk<br>
  Prices, specifications, and completion dates are indicative and subject to change.</p>
</body>
</html>
