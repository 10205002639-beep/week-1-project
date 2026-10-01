<?php
declare(strict_types=1);

function e(string $text): string
{
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

// Challenge: permissions live in data, not in a long chain of if/elseif.
$permissions = [
    "administrator" => ["create", "read", "update", "delete", "export"],
    "data officer"  => ["create", "read", "update"],
    "viewer"        => ["read"],
];

function canPerformAction(array $permissions, string $role, string $action): bool
{
    // Decision: normalise once so "ADMINISTRATOR " and "administrator" behave alike.
    $role = strtolower(trim($role));
    $action = strtolower(trim($action));

    // An unknown role has no entry, so it gets no permissions.
    if (!array_key_exists($role, $permissions)) {
        return false;
    }
    return in_array($action, $permissions[$role], true);
}

function explainResult(array $permissions, string $role, string $action): string
{
    $roleKey = strtolower(trim($role));
    if (!array_key_exists($roleKey, $permissions)) {
        return "Denied: '$role' is not a recognised role, so it has no permissions.";
    }
    if (canPerformAction($permissions, $role, $action)) {
        return "Allowed: the $role role may $action records.";
    }
    return "Denied: the $role role is not permitted to $action records.";
}

$tests = [
    ["Administrator", "export"],
    ["administrator", "DELETE"],
    ["Data Officer", "update"],
    ["Data Officer", "delete"],
    ["Data Officer", "export"],
    ["Viewer", "read"],
    ["VIEWER", "create"],
    ["Guest", "read"],
    ["Viewer", "approve"],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Exercise 8 - Role Permission Checker</title>
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
<h1>Exercise 8: Role Permission Checker</h1>
<table>
    <tr><th>Role</th><th>Action</th><th>Result</th><th>Explanation</th></tr>
    <?php foreach ($tests as [$role, $action]): ?>
        <?php $allowed = canPerformAction($permissions, $role, $action); ?>
        <tr>
            <td><?= e($role) ?></td>
            <td><?= e($action) ?></td>
            <td class="<?= $allowed ? 'ok' : 'warn' ?>"><?= $allowed ? 'true' : 'false' ?></td>
            <td><?= e(explainResult($permissions, $role, $action)) ?></td>
        </tr>
    <?php endforeach; ?>
</table>
</body>
</html>
