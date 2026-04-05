<?php
// wp-content/mu-plugins/crowns-estates-core/email-templates/admin-notification.php
/** @var string $name @var string $email @var string $phone @var string $message @var string $source @var string $property_name */
$admin_url = admin_url('admin.php?page=ce-enquiries');
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>New Enquiry — Crowns Estates</title></head>
<body style="font-family:Arial,sans-serif;color:#0A0A0A;max-width:600px;margin:0 auto;padding:20px">
  <h2 style="border-bottom:2px solid #C4973A;padding-bottom:8px">New Enquiry Received</h2>
  <table style="width:100%;border-collapse:collapse">
    <tr><td style="padding:8px 0;font-weight:bold;width:140px">Name</td><td style="padding:8px 0"><?php echo esc_html($name); ?></td></tr>
    <tr style="background:#f5f5f5"><td style="padding:8px 0;font-weight:bold">Email</td><td style="padding:8px 0"><?php echo esc_html($email); ?></td></tr>
    <tr><td style="padding:8px 0;font-weight:bold">Phone</td><td style="padding:8px 0"><?php echo esc_html($phone ?: '—'); ?></td></tr>
    <tr style="background:#f5f5f5"><td style="padding:8px 0;font-weight:bold">Source</td><td style="padding:8px 0"><?php echo esc_html($source); ?></td></tr>
    <?php if ($property_name): ?>
    <tr><td style="padding:8px 0;font-weight:bold">Property</td><td style="padding:8px 0"><?php echo esc_html($property_name); ?></td></tr>
    <?php endif; ?>
    <?php if ($message): ?>
    <tr style="background:#f5f5f5"><td style="padding:8px 0;font-weight:bold;vertical-align:top">Message</td><td style="padding:8px 0"><?php echo nl2br(esc_html($message)); ?></td></tr>
    <?php endif; ?>
  </table>
  <p style="margin-top:20px"><a href="<?php echo esc_url($admin_url); ?>" style="background:#C4973A;color:#fff;padding:10px 20px;text-decoration:none;border-radius:4px;display:inline-block">View in Admin</a></p>
</body>
</html>
