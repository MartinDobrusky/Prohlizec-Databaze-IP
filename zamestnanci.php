<?php
    require_once ("include/db_connect.php");
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <style>
        a { text-decoration: none; }
    </style>
    <title>Výpis zaměstnanců</title>
</head>
<body><div class="container"><?php

$sortBy = filter_input(INPUT_GET, "sortBy");

$pdo = DB::connect();

function SortFunct($orderRow, $orderBy, $pdo) {
    return $pdo->query('SELECT *, e.name AS empName FROM employee AS e INNER JOIN room ON e.room = room.room_id ORDER BY' . ' ' . $orderRow . ' ' . $orderBy);
}

switch($sortBy){
    case "name_asc": $stmt = SortFunct("e.surname", "ASC", $pdo); break;
    case "name_desc": $stmt = SortFunct("e.surname", "DESC", $pdo); break;
    case "room_asc": $stmt = SortFunct("room.name", "ASC", $pdo); break;
    case "room_desc": $stmt = SortFunct("room.name", "DESC", $pdo); break;
    case "phone_asc": $stmt = SortFunct("room.phone", "ASC", $pdo); break;
    case "phone_desc": $stmt = SortFunct("room.phone", "DESC", $pdo); break;
    case "job_asc": $stmt = SortFunct("e.job", "ASC", $pdo); break;
    case "job_desc": $stmt = SortFunct("e.job", "DESC", $pdo); break;
    default: $stmt = SortFunct("e.surname", "ASC", $pdo); break;
}

if ($stmt->rowCount() == 0){
    echo "Databáze neobsahuje žádná data";
} else {
    echo "<h2>Seznam zaměstnanců</h2>";

    echo "<table class='table table-hover'>";
    
    echo "<thead><tr><th>Jméno <a href='zamestnanci.php?sortBy=name_asc'>🢃</a> <a href='zamestnanci.php?sortBy=name_desc'>🢁</a></th><th>Místnost <a href='zamestnanci.php?sortBy=room_asc'>🢃</a> <a href='zamestnanci.php?sortBy=room_desc'>🢁</a></th><th>Telefon <a href='zamestnanci.php?sortBy=phone_asc'>🢃</a> <a href='zamestnanci.php?sortBy=phone_desc'>🢁</a></th><th>Pozice <a href='zamestnanci.php?sortBy=job_asc'>🢃</a> <a href='zamestnanci.php?sortBy=job_desc'>🢁</a></th></tr></thead>";

    echo "<tbody>";
    while ($row = $stmt->fetch()) {
        echo "<tr>";
        echo "<td><a href='kartaZamestnanec.php?employee_id={$row->employee_id}'>{$row->surname} {$row->empName}</a></td>";
        echo "<td>{$row->name}</td>";
        echo "<td>" . ($row->phone ?: "&mdash;") . "</td>";
        echo "<td>{$row->job}</td>";
        echo "</tr>";
    }
    echo "</tbody>";

    echo "<tfoot><tr><td><a href='index.php'>🢀 Zpět na úvodní stránku</a></td><td></td><td></td><td></td></tr></tfoot>";

    echo "</table>";
}
?></div>
</body>
</html>