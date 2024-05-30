<?php
header("Content-Type: application/json");

$host = 'localhost';
$db = 'employee_management';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

$pdo = new PDO($dsn, $user, $pass, $options);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {


    if (isset($_GET['department'])) {
        $stmt = $pdo->prepare("SELECT * FROM department");
        $stmt->execute();
        $department = $stmt->fetchAll();
        echo json_encode($department);
    }

    else if (isset($_GET['employee'])) {
        $stmt = $pdo->query("SELECT e.emp_id, e.emp_name, e.emp_contact_number, e.emp_address, d.dep_name, a.username, a.email
        FROM employee e 
        JOIN department d ON e.dep_id = d.dep_id 
        LEFT JOIN accounts a ON e.emp_id = a.emp_id 
        ORDER BY e.emp_id ASC");
        $employees = $stmt->fetchAll();
        echo json_encode($employees);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $input = json_decode(file_get_contents('php://input'), true);
    $sql = "INSERT INTO employee (emp_name, emp_address, emp_contact_number, dep_id) VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$input['emp_name'], $input['emp_address'], $input['emp_contact_number'], $input['dep_id']]);
    echo json_encode(['message' => 'Employee added successfully']);
}
?>