<?php
declare(strict_types=1);

function e(string $text): string
{
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

$requests = [
    "Thimphu", "Paro", "Thimphu",
    "Punakha", "Paro", "Thimphu",
];

// Challenge: counters are built dynamically, one array key per dzongkhag.
$counts = [];
foreach ($requests as $dzongkhag) {
    if (!isset($counts[$dzongkhag])) {
        $counts[$dzongkhag] = 0;
    }
    $counts[$dzongkhag]++;
}

$total = count($requests);

// Decision: collect ALL dzongkhags sharing the top count so ties are reported.
$highestCount = max($counts);
$topDzongkhags = array_keys($counts, $highestCount, true);

$thimphuCount = $counts["Thimphu"] ?? 0;
$thimphuPercent = $total > 0 ? ($thimphuCount / $total) * 100 : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Exercise 4 - Dzongkhag Service Counter</title>
<style>
body{font-family:system-ui,sans-serif;max-width:900px;margin:2rem auto;padding:0 1rem;line-height:1.5}
table{border-collapse:collapse;width:100%;margin:1rem 0}
th,td{border:1px solid #ccc;padding:.4rem .6rem;text-align:left}
th{background:#f2f2f2}
.warn{color:#b00020;font-weight:bold}
.ok{color:#0a7d2c;font-weight:bold}
section{border-top:2px solid #ddd;margin-top:1.5rem;padding-top:.5rem}
</style>
</head>
<body>
<h1>Exercise 4: Dzongkhag Service Counter</h1>

<h2>All requests</h2>
<table>
    <tr><th>Request no.</th><th>Dzongkhag</th></tr>
    <?php foreach ($requests as $index => $dzongkhag): ?>
        <tr><td><?= $index + 1 ?></td><td><?= e($dzongkhag) ?></td></tr>
    <?php endforeach; ?>
</table>

<h2>Requests per dzongkhag</h2>
<ul>
    <?php foreach ($counts as $dzongkhag => $count): ?>
        <li><?= e($dzongkhag) ?>: <?= $count ?></li>
    <?php endforeach; ?>
</ul>

<p>Total requests: <strong><?= $total ?></strong></p>
<p>Highest number of requests: <strong><?= e(implode(", ", $topDzongkhags)) ?></strong> (<?= $highestCount ?>)</p>
<p>Thimphu share: <strong><?= number_format($thimphuPercent, 1) ?>%</strong></p>
</body>
</html>
