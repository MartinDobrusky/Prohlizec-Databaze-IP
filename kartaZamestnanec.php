<?php
require_once ("include/db_connect.php");

$state = "OK";

$employeeId = filter_input(INPUT_GET, "employee_id", FILTER_VALIDATE_INT);

if ($employeeId === null) {
    http_response_code(400);
    $state = "BadRequest";
} else {
    $pdo = DB::connect();
    $query = "SELECT * FROM employee WHERE employee_id=:employeeId";
    $query2 = $pdo->query("SELECT * FROM `key` WHERE employee='$employeeId'");
    $query3 = $pdo->query("SELECT * FROM room");

    $stmta = $query2->fetchAll();
    $stmtb = $query3->fetchAll();

    $stmt = $pdo->prepare($query);
    $stmt->execute(["employeeId" => $employeeId]);

    if ($stmt->rowCount() == 0) {
        http_response_code(404);
        $state = "NotFound";
    } else {
        $employee = $stmt->fetch();
    }
}
?>
<!doctype html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <style>
        a { text-decoration: none; }
        @media (min-width: 1200px) {
            .container{
            max-width: 500px;
            }
        }
    </style>
    <title><?php if($state == "OK"){ echo "Detail zaměstnance " . $employee->surname . " " . $employee->name; }?></title>
</head>
<body>
<div class="container">
<?php
if ($state === "OK") {
    echo "<h2>Karta osoby: " . $employee->surname . " " . $employee->name . "</h2>";

    echo "<table class='table table-hover'>";

    echo "<tbody>";
    echo "<tr><th>Jméno</th>" . "<td>" . $employee->name . "</td>" . "</tr>";
    echo "<tr><th>Příjmení</th>" . "<td>" . $employee->surname . "</td>" . "</tr>";
    echo "<tr><th>Pozice</th>" . "<td>" . $employee->job . "</td>" . "</tr>";
    echo "<tr><th>Mzda</th>" . "<td>" . number_format($employee->wage, $decimals = 2, $decimal_separator = ".", $thousands_separator = ",") . "</td>" . "</tr>";
    echo "<tr><th>Místnost</th>";
    echo "<td>";
    foreach ($stmtb as $value) {
        if($value->room_id == $employee->room) {
            echo "<a href='kartaMistnost.php?room_id={$value->room_id}'>{$value->name}</a><br>";
        }
    }
    echo "</td></tr>";
    echo "<tr><th>Klíče</th>";
    echo "<td>";
    foreach ($stmta as $val1) {
        foreach ($stmtb as $val2) {
            if ($val2->room_id == $val1->room){
                echo "<a href='kartaMistnost.php?room_id={$val2->room_id}'>{$val2->name}</a><br>";
            }
        }
    }
    echo "</td></tr>";
    echo "</tbody>";

    echo "<tfoot><tr><td><a href='zamestnanci.php'>🢀 Zpět na seznam zaměstnanců</a></td><td></td></tr></tfoot>";

    } elseif ($state === "NotFound") {
        echo "<h2>Zaměstnanec nenalezen</h2>";

        echo "<table class='table table-hover'><tfoot><tr><td><a href='zamestnanci.php'>🢀 Zpět na seznam zaměstnanců</a></tr></td></tfoot></table>";
    } elseif ($state === "BadRequest") {
        echo "<h2>Chybný požadavek</h2>";

        echo "<table class='table table-hover'><tfoot><tr><td><a href='zamestnanci.php'>🢀 Zpět na seznam zaměstnanců</a></tr></td></tfoot></table>";
    }
?>
</div>
</body>
</html>