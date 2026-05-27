<?php
$data = $data ?? [];
$message = $data['message'] ?? '';

// Only render if there is an actual error message
if (empty($message)) {
    return; // nothing to display
}

$type = $data['type'] ?? '';
$file = $data['file'] ?? '';
$line = $data['line'] ?? '';
$trace = $data['trace'] ?? '';

// Read code snippet around the error line (5 lines above and below)
$codeSnippet = '';
if (is_file($file) && $line > 0) {
    $lines = file($file);
    $totalLines = count($lines);

    $start = max($line - 6, 0); // 5 lines above
    $end = min($line + 5, $totalLines); // 5 lines below
    $snippetLines = array_slice($lines, $start, $end - $start);

    foreach ($snippetLines as $num => $codeLine) {
        $codeLine = htmlspecialchars($codeLine);
        $actualLineNum = $start + $num + 1;
        $highlight = ($actualLineNum === $line) ? 'background:#ffe6e6;' : '';
        $codeSnippet .= "<span style='display:block;padding:0 5px;$highlight'>{$actualLineNum}: {$codeLine}</span>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Debug Error</title>
    <style>
        body { background:#f8d7da; color:#721c24; font-family:monospace; padding:20px; }
        .error-box { border:2px solid #f5c6cb; padding:15px; margin-bottom:20px; background:#fdd; }
        h3 { margin-top:0; }
        pre { background:#fff; color:#000; padding:10px; overflow-x:auto; }
        .code-snippet { background:#fff; border:1px solid #f5c6cb; padding:10px; margin-top:10px; overflow-x:auto; }
        .code-snippet span { display:block; }
    </style>
</head>
<body>
    <div class="error-box">
        <h3><?= htmlspecialchars($type) ?></h3>
        <strong>Message:</strong> <?= htmlspecialchars($message) ?><br>
        <strong>File:</strong> <?= htmlspecialchars($file) ?><br>
        <strong>Line:</strong> <?= $line ?><br>
    </div>

    <?php if ($codeSnippet): ?>
    <div class="error-box code-snippet">
        <strong>Code snippet around error:</strong>
        <?= $codeSnippet ?>
    </div>
    <?php endif; ?>

    <div class="error-box">
        <strong>Full stack trace:</strong>
        <pre><?= htmlspecialchars($trace) ?></pre>
    </div>
</body>
</html>
