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
    <title>Výpis místností</title>
</head>
<body><div class="container"><?php

$sortBy = filter_input(INPUT_GET, "sortBy");

$pdo = DB::connect();

function SortFunct($orderRow, $orderBy, $pdo) {
    return $pdo->query('SELECT * FROM room ORDER BY' . ' ' . $orderRow . ' ' . $orderBy);
}

switch($sortBy){
    case "name_asc": $stmt = SortFunct("name", "ASC", $pdo); break;
    case "name_desc": $stmt = SortFunct("name", "DESC", $pdo); break;
    case "num_asc": $stmt = SortFunct("no", "ASC", $pdo); break;
    case "num_desc": $stmt = SortFunct("no", "DESC", $pdo); break;
    case "phone_asc": $stmt = SortFunct("phone", "ASC", $pdo); break;
    case "phone_desc": $stmt = SortFunct("phone", "DESC", $pdo); break;
    default: $stmt = SortFunct("name", "ASC", $pdo); break;
}

if ($stmt->rowCount() == 0){
    echo "Databáze neobsahuje žádná data";
} else {
    echo "<h2>Seznam místností</h2>";

    echo "<table class='table table-hover'>";
    
    echo "<thead><tr><th>Název <a href='mistnosti.php?sortBy=name_asc'>🢃</a> <a href='mistnosti.php?sortBy=name_desc'>🢁</a></th><th>Číslo <a href='mistnosti.php?sortBy=num_asc'>🢃</a> <a href='mistnosti.php?sortBy=num_desc'>🢁</a></th><th>Telefon <a href='mistnosti.php?sortBy=phone_asc'>🢃</a> <a href='mistnosti.php?sortBy=phone_desc'>🢁</a></th></tr></thead>";

    echo "<tbody>";
    while ($row = $stmt->fetch()) {
        echo "<tr>";
        echo "<td><a href='kartaMistnost.php?room_id={$row->room_id}'>{$row->name}</a></td>";
        echo "<td>{$row->no}</td>";
        echo "<td>" . ($row->phone ?: "&mdash;&mdash;") . "</td>";
        echo "</tr>";
    }
    echo "</tbody>";

    echo "<tfoot><tr><td><a href='index.php'>🢀 Zpět na úvodní stránku</a></td><td></td><td></td></tr></tfoot>";

    echo "</table>";
}
?></div>
</body>
</html>