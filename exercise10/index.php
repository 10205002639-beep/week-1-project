<?php
declare(strict_types=1);

function e(string $text): string
{
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

// ---------- Datasets (nested arrays) ----------
$datasets = [
    "valid" => [
        ["name" => "Pema Dorji",     "age" => 24, "dzongkhag" => "Thimphu",   "status" => "Active"],
        ["name" => "Tashi Wangmo",   "age" => 67, "dzongkhag" => "Paro",      "status" => "Active"],
        ["name" => "Sonam Choden",   "age" => 45, "dzongkhag" => "Punakha",   "status" => "Inactive"],
        ["name" => "Karma Tenzin",   "age" => 15, "dzongkhag" => "Thimphu",   "status" => "Active"],
        ["name" => "Dorji Namgyel",  "age" => 62, "dzongkhag" => "Thimphu",   "status" => "Inactive"],
        ["name" => "Kinley Lham",    "age" => 28, "dzongkhag" => "Bumthang",  "status" => "Active"],
        ["name" => "Choki Wangchuk", "age" => 37, "dzongkhag" => "Trashigang", "status" => "Active"],
        ["name" => "Dechen Pelden",  "age" => 52, "dzongkhag" => "Paro",      "status" => "Inactive"],
    ],
    "problems" => [
        ["name" => "Pema Dorji",     "age" => 24,       "dzongkhag" => "Thimphu",  "status" => "Active"],
        ["name" => "",               "age" => 67,       "dzongkhag" => "Paro",     "status" => "Active"],
        ["name" => "Sonam Choden",   "age" => 150,      "dzongkhag" => "Punakha",  "status" => "Inactive"],
        ["name" => "Karma Tenzin",   "age" => 15,       "dzongkhag" => "",         "status" => "Active"],
        ["name" => "Dorji Namgyel",  "age" => "sixty",  "dzongkhag" => "Thimphu",  "status" => "Pending"],
        ["name" => "Kinley Lham",    "age" => 28,       "dzongkhag" => "Bumthang", "status" => "Active"],
        ["name" => "",               "age" => -3,       "dzongkhag" => "",         "status" => "Unknown"],
        ["name" => "Dechen Pelden",  "age" => 52,       "dzongkhag" => "Paro",     "status" => "Inactive"],
    ],
];

// Challenge answer is data: the permission map is an associative array.
$permissions = [
    "administrator" => ["create", "read", "update", "delete", "export"],
    "data officer"  => ["create", "read", "update"],
    "viewer"        => ["read"],
];

// ---------- Reusable functions ----------

// Returns a list of problems for one profile (empty list = clean profile).
function findProfileIssues(array $profile): array
{
    $issues = [];
    if (trim((string) ($profile["name"] ?? "")) === "") {
        $issues[] = "Missing name";
    }
    $age = $profile["age"] ?? null;
    // Strict check: the text "sixty" or a float must not pass as a valid age.
    if (!is_int($age) || $age < 0 || $age > 120) {
        $issues[] = "Invalid age";
    }
    if (trim((string) ($profile["dzongkhag"] ?? "")) === "") {
        $issues[] = "Missing dzongkhag";
    }
    if (!in_array($profile["status"] ?? "", ["Active", "Inactive"], true)) {
        $issues[] = "Invalid status";
    }
    return $issues;
}

// Each issue costs 25 points from a starting score of 100.
function dataQualityScore(array $issues): int
{
    return max(0, 100 - 25 * count($issues));
}

function filterProfiles(array $profiles, string $dzongkhag, string $status): array
{
    $filtered = [];
    foreach ($profiles as $profile) {
        $dzongkhagOk = ($dzongkhag === "All") || ($profile["dzongkhag"] === $dzongkhag);
        $statusOk = ($status === "All") || ($profile["status"] === $status);
        if ($dzongkhagOk && $statusOk) {
            $filtered[] = $profile;
        }
    }
    return $filtered;
}

function canPerformAction(array $permissions, string $role, string $action): bool
{
    $role = strtolower(trim($role));
    $action = strtolower(trim($action));
    return array_key_exists($role, $permissions) && in_array($action, $permissions[$role], true);
}

// Decision: statistics only use profiles with a valid age, so one bad record
// cannot distort the youngest, oldest or average values.
function ageStatistics(array $profiles): array
{
    $youngest = null;
    $oldest = null;
    $sum = 0;
    $counted = 0;
    foreach ($profiles as $profile) {
        if (!is_int($profile["age"]) || $profile["age"] < 0 || $profile["age"] > 120) {
            continue;
        }
        if ($youngest === null || $profile["age"] < $youngest["age"]) {
            $youngest = $profile;
        }
        if ($oldest === null || $profile["age"] > $oldest["age"]) {
            $oldest = $profile;
        }
        $sum += $profile["age"];
        $counted++;
    }
    return [
        "youngest" => $youngest,
        "oldest"   => $oldest,
        "average"  => $counted > 0 ? $sum / $counted : null,
        "counted"  => $counted,
    ];
}

function describeProfile(?array $profile): string
{
    if ($profile === null) {
        return "n/a";
    }
    $name = trim($profile["name"]) === "" ? "(no name)" : $profile["name"];
    return $name . ", age " . $profile["age"];
}

// ---------- Read the selections from the URL, with safe defaults ----------
$datasetKey = (string) ($_GET["dataset"] ?? "valid");
if (!array_key_exists($datasetKey, $datasets)) {
    $datasetKey = "valid";
}
$profiles = $datasets[$datasetKey];

$selectedDzongkhag = (string) ($_GET["dzongkhag"] ?? "All");
$selectedStatus    = (string) ($_GET["status"] ?? "All");
$selectedRole      = (string) ($_GET["role"] ?? "viewer");
$selectedAction    = (string) ($_GET["action"] ?? "read");

// ---------- Calculations ----------
$total = count($profiles);
$active = 0;
$inactive = 0;
$scores = [];
$imperfect = 0;
$worstIndex = null;
$worstIssueCount = 0;
$allIssues = [];
$dzongkhagOptions = [];

foreach ($profiles as $index => $profile) {
    if ($profile["status"] === "Active") {
        $active++;
    } elseif ($profile["status"] === "Inactive") {
        $inactive++;
    }

    $issues = findProfileIssues($profile);
    $allIssues[$index] = $issues;
    $scores[$index] = dataQualityScore($issues);

    if (count($issues) > 0) {
        $imperfect++;
    }
    if (count($issues) > $worstIssueCount) {
        $worstIssueCount = count($issues);
        $worstIndex = $index;
    }
    if (trim((string) $profile["dzongkhag"]) !== "" && !in_array($profile["dzongkhag"], $dzongkhagOptions, true)) {
        $dzongkhagOptions[] = $profile["dzongkhag"];
    }
}
sort($dzongkhagOptions);

$averageScore = $total > 0 ? array_sum($scores) / $total : 0;
$ageStats = ageStatistics($profiles);
$filtered = filterProfiles($profiles, $selectedDzongkhag, $selectedStatus);
$allowed = canPerformAction($permissions, $selectedRole, $selectedAction);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Exercise 10 - B-PIS Command Dashboard</title>
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
<h1>Exercise 10: B-PIS Command Dashboard</h1>

<form method="get">
    <p>
        Dataset:
        <select name="dataset">
            <option value="valid" <?= $datasetKey === "valid" ? "selected" : "" ?>>Complete valid dataset</option>
            <option value="problems" <?= $datasetKey === "problems" ? "selected" : "" ?>>Dataset with problems</option>
        </select>
        Dzongkhag:
        <select name="dzongkhag">
            <option value="All">All</option>
            <?php foreach ($dzongkhagOptions as $option): ?>
                <option value="<?= e($option) ?>" <?= $selectedDzongkhag === $option ? "selected" : "" ?>><?= e($option) ?></option>
            <?php endforeach; ?>
        </select>
        Status:
        <select name="status">
            <?php foreach (["All", "Active", "Inactive"] as $option): ?>
                <option value="<?= $option ?>" <?= $selectedStatus === $option ? "selected" : "" ?>><?= $option ?></option>
            <?php endforeach; ?>
        </select>
    </p>
    <p>
        Role:
        <select name="role">
            <?php foreach (["administrator", "data officer", "viewer", "guest"] as $option): ?>
                <option value="<?= $option ?>" <?= strtolower($selectedRole) === $option ? "selected" : "" ?>><?= $option ?></option>
            <?php endforeach; ?>
        </select>
        Action:
        <select name="action">
            <?php foreach (["create", "read", "update", "delete", "export"] as $option): ?>
                <option value="<?= $option ?>" <?= strtolower($selectedAction) === $option ? "selected" : "" ?>><?= $option ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Apply</button>
    </p>
</form>

<h2>Overview</h2>
<table>
    <tr><th>Total profiles</th><td><?= $total ?></td></tr>
    <tr><th>Active</th><td><?= $active ?></td></tr>
    <tr><th>Inactive</th><td><?= $inactive ?></td></tr>
    <tr><th>Youngest</th><td><?= e(describeProfile($ageStats["youngest"])) ?></td></tr>
    <tr><th>Oldest</th><td><?= e(describeProfile($ageStats["oldest"])) ?></td></tr>
    <tr><th>Average age</th>
        <td>
            <?= $ageStats["average"] === null ? "n/a" : number_format($ageStats["average"], 1) ?>
            (from <?= $ageStats["counted"] ?> profile(s) with a valid age)
        </td>
    </tr>
</table>

<h2>Permission check</h2>
<p class="<?= $allowed ? 'ok' : 'warn' ?>">
    <?= $allowed
        ? "Allowed: " . e($selectedRole) . " may " . e($selectedAction) . "."
        : "Denied: " . e($selectedRole) . " may not " . e($selectedAction) . "." ?>
</p>

<h2>Filtered profiles
    (<?= e($selectedDzongkhag) ?> / <?= e($selectedStatus) ?>): <?= count($filtered) ?> found</h2>
<?php if (count($filtered) === 0): ?>
    <p class="warn">No matching records found</p>
<?php else: ?>
    <table>
        <tr><th>Name</th><th>Age</th><th>Dzongkhag</th><th>Status</th></tr>
        <?php foreach ($filtered as $profile): ?>
            <tr>
                <td><?= e((string) $profile["name"]) ?></td>
                <td><?= e((string) $profile["age"]) ?></td>
                <td><?= e((string) $profile["dzongkhag"]) ?></td>
                <td><?= e((string) $profile["status"]) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>

<h2>Data-quality report</h2>
<table>
    <tr><th>#</th><th>Name</th><th>Score</th><th>Warnings</th></tr>
    <?php foreach ($profiles as $index => $profile): ?>
        <tr>
            <td><?= $index + 1 ?></td>
            <td><?= e(trim((string) $profile["name"]) === "" ? "(no name)" : (string) $profile["name"]) ?></td>
            <td class="<?= $scores[$index] === 100 ? 'ok' : 'warn' ?>"><?= $scores[$index] ?></td>
            <td>
                <?php if (count($allIssues[$index]) === 0): ?>
                    None
                <?php else: ?>
                    <span class="warn"><?= e(implode("; ", $allIssues[$index])) ?></span>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
<ul>
    <li>Average data-quality score: <strong><?= number_format($averageScore, 1) ?></strong></li>
    <li>Imperfect profiles: <strong><?= $imperfect ?></strong></li>
    <li>Record needing the most correction:
        <strong>
            <?= $worstIndex === null
                ? "none, all profiles are clean"
                : "record #" . ($worstIndex + 1) . " (" . $worstIssueCount . " issues)" ?>
        </strong>
    </li>
</ul>
</body>
</html>
