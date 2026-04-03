<?php
/**
 * Property archive fallback — redirects to Projects page.
 */
wp_safe_redirect(home_url('/projects'), 301);
exit;
