<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/url.php';
start_secure_session();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>403 Forbidden</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="<?php echo htmlspecialchars(url('/assets/css/app.css')); ?>" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container py-5">
    <div class="card table-card p-4">
      <h1 class="h4 mb-2">Access denied</h1>
      <p class="mb-3 text-secondary">You don’t have permission to view this page.</p>
      <a class="btn btn-primary" href="<?php echo htmlspecialchars(url('/index.php')); ?>">Go to Dashboard</a>
    </div>
  </div>
</body>
</html>


