<?php
// wp-content/mu-plugins/crowns-estates-core/email-templates/daily-digest.php
/** @var array $enquiries @var int $count */
$admin_url = admin_url('admin.php?page=ce-enquiries');
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Daily Digest — Crowns Estates</title></head>
<body style="font-family:Arial,sans-serif;color:#0A0A0A;max-width:600px;margin:0 auto;padding:20px">
  <h2 style="border-bottom:2px solid #C4973A;padding-bottom:8px">
    <?php echo $count; ?> New <?php echo $count === 1 ? 'Enquiry' : 'Enquiries'; ?> Today
  </h2>
  <table style="width:100%;border-collapse:collapse;font-size:13px">
    <tr style="background:#f5f5f5">
      <th style="padding:7px 10px;text-align:left">Name</th>
      <th style="padding:7px 10px;text-align:left">Email</th>
      <th style="padding:7px 10px;text-align:left">Source</th>
      <th style="padding:7px 10px;text-align:left">Time</th>
    </tr>
    <?php foreach ($enquiries as $i => $e): ?>
    <tr<?php echo $i % 2 === 0 ? '' : ' style="background:#f9f9f9"'; ?>>
      <td style="padding:7px 10px"><?php echo esc_html($e->name); ?></td>
      <td style="padding:7px 10px"><?php echo esc_html($e->email); ?></td>
      <td style="padding:7px 10px"><?php echo esc_html($e->source); ?></td>
      <td style="padding:7px 10px"><?php echo esc_html(date_i18n('H:i', strtotime($e->created_at))); ?></td>
    </tr>
    <?php endforeach; ?>
  </table>
  <p style="margin-top:20px"><a href="<?php echo esc_url($admin_url); ?>" style="background:#C4973A;color:#fff;padding:10px 20px;text-decoration:none;border-radius:4px;display:inline-block">View All Enquiries</a></p>
</body>
</html>
