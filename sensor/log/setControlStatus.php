<?php
header("Content-Type: application/json");

$conn = new mysqli("localhost", "root", "", "smart_green_house");
if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "DB connect error"]);
    exit;
}

// Ambil input POST
$mode        = $_POST["mode"] ?? null;
$kipas       = $_POST["kipas"] ?? null;
$pompa       = $_POST["pompa"] ?? null;
$batas_suhu  = $_POST["batas_suhu"] ?? null;
$batas_soil  = $_POST["batas_soil"] ?? null;

// Update database
$sql = "UPDATE control_settings SET 
    mode = '$mode', 
    kipas = '$kipas', 
    pompa = '$pompa', 
    batas_suhu = '$batas_suhu', 
    batas_soil = '$batas_soil'
    WHERE id = 1";

if ($conn->query($sql)) {
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error", "message" => $conn->error]);
}

$conn->close();
?>
