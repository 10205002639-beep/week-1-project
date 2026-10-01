<?php
declare(strict_types=1);

function e(string $text): string
{
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

$records = [
    ["name" => "Pema Dorji",     "dzongkhag" => "Thimphu",   "age" => 24, "status" => "Active",   "type" => "Registration"],
    ["name" => "Tashi Wangmo",   "dzongkhag" => "Paro",      "age" => 67, "status" => "Active",   "type" => "Verification"],
    ["name" => "Sonam Choden",   "dzongkhag" => "Punakha",   "age" => 45, "status" => "Inactive", "type" => "Correction"],
    ["name" => "Karma Tenzin",   "dzongkhag" => "Thimphu",   "age" => 15, "status" => "Active",   "type" => "Registration"],
    ["name" => "Dorji Namgyel",  "dzongkhag" => "Thimphu",   "age" => 62, "status" => "Inactive", "type" => "Update"],
    ["name" => "Kinley Lham",    "dzongkhag" => "Bumthang",  "age" => 28, "status" => "Active",   "type" => "Registration"],
    ["name" => "Choki Wangchuk", "dzongkhag" => "Trashigang","age" => 37, "status" => "Active",   "type" => "Verification"],
    ["name" => "Dechen Pelden",  "dzongkhag" => "Paro",      "age" => 52, "status" => "Inactive", "type" => "Update"],
    ["name" => "Jigme Norbu",    "dzongkhag" => "Thimphu",   "age" => 8,  "status" => "Active",   "type" => "Registration"],
    ["name" => "Yeshey Zangmo",  "dzongkhag" => "Haa",       "age" => 71, "status" => "Active",   "type" => "Correction"],
    ["name" => "Ugyen Dema",     "dzongkhag" => "Punakha",   "age" => 33, "status" => "Active",   "type" => "Verification"],
    ["name" => "Rinchen Gyeltshen", "dzongkhag" => "Wangdue", "age" => 19, "status" => "Inactive", "type" => "Registration"],
];

// Decision: one generic counter; the named functions below wrap it so each
// report section reads clearly while the counting logic exists only once.
function countByField(array $records, string $field): array
{
    $counts = [];
    foreach ($records as $record) {
        $value = $record[$field];
        $counts[$value] = ($counts[$value] ?? 0) + 1;
    }
    arsort($counts);
    return $counts;
}

function countByStatus(array $records): array     { return countByField($records, "status"); }
function countByDzongkhag(array $records): array  { return countByField($records, "dzongkhag"); }
function countByRecordType(array $records): array { return countByField($records, "type"); }

function percentage(int $part, int $total): float
{
    return $total > 0 ? ($part / $total) * 100 : 0.0;
}

function countAgeGroups(array $records): array
{
    $adults = 0;
    $seniors = 0;
    foreach ($records as $record) {
        if ($record["age"] >= 60) {
            $seniors++;
        } elseif ($record["age"] >= 18) {
            $adults++;
        }
    }
    return ["adults" => $adults, "seniors" => $seniors];
}

function renderCountTable(string $heading, array $counts, int $total): void
{
    echo '<h3>' . e($heading) . '</h3><table><tr><th>' . e($heading) . '</th><th>Count</th><th>Share</th></tr>';
    foreach ($counts as $label => $count) {
        echo '<tr><td>' . e((string) $label) . '</td><td>' . $count . '</td><td>'
            . number_format(percentage($count, $total), 1) . '%</td></tr>';
    }
    echo '</table>';
}

$total = count($records);
$statusCounts = countByStatus($records);
$dzongkhagCounts = countByDzongkhag($records);
$typeCounts = countByRecordType($records);
$ageGroups = countAgeGroups($records);
$active = $statusCounts["Active"] ?? 0;
$inactive = $statusCounts["Inactive"] ?? 0;

// arsort() puts the largest count first, so the first key is the most common.
$topDzongkhag = array_key_first($dzongkhagCounts);
$topType = array_key_first($typeCounts);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Exercise 9 - B-PIS Records Report</title>
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
<h1>Exercise 9: B-PIS Records Report</h1>

<h2>Summary</h2>
<table>
    <tr><th>Measure</th><th>Value</th></tr>
    <tr><td>Total records</td><td><?= $total ?></td></tr>
    <tr><td>Active records</td><td><?= $active ?></td></tr>
    <tr><td>Inactive records</td><td><?= $inactive ?></td></tr>
    <tr><td>Percentage active</td><td><?= number_format(percentage($active, $total), 1) ?>%</td></tr>
    <tr><td>Adults (18-59)</td><td><?= $ageGroups["adults"] ?></td></tr>
    <tr><td>Senior citizens (60+)</td><td><?= $ageGroups["seniors"] ?></td></tr>
</table>

<h2>Breakdowns</h2>
<?php
renderCountTable("Dzongkhag", $dzongkhagCounts, $total);
renderCountTable("Record type", $typeCounts, $total);
?>

<h2>Interpretation</h2>
<p>
    <strong><?= e((string) $topDzongkhag) ?></strong> is the most frequent dzongkhag
    (<?= $dzongkhagCounts[$topDzongkhag] ?> of <?= $total ?> records), and
    <strong><?= e((string) $topType) ?></strong> is the most frequent record type
    (<?= $typeCounts[$topType] ?> of <?= $total ?> records).
</p>

<h2>All records</h2>
<table>
    <tr><th>#</th><th>Name</th><th>Dzongkhag</th><th>Age</th><th>Status</th><th>Record type</th></tr>
    <?php foreach ($records as $index => $record): ?>
        <tr>
            <td><?= $index + 1 ?></td>
            <td><?= e($record["name"]) ?></td>
            <td><?= e($record["dzongkhag"]) ?></td>
            <td><?= $record["age"] ?></td>
            <td><?= e($record["status"]) ?></td>
            <td><?= e($record["type"]) ?></td>
        </tr>
    <?php endforeach; ?>
</table>
</body>
</html>
