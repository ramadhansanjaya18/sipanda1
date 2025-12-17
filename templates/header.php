<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css?v=1.2">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css?v=1.2">
    <?php if (isset($page_css)): ?>
        <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/<?= $page_css ?>?v=1.2">
    <?php endif; ?>
    <title><?= $page_title ?? 'SIPANDA' ?></title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const JS_BASE_URL = '<?= BASE_URL ?>';
    </script>
</head>

<body class="<?= isset($page_css) ? 'dashboard-body' : 'login-body' ?>">