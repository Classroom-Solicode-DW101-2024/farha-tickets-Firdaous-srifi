
<?php
require "config.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $eventId = $_POST['eventId'];
    $normalTickets = isset($_POST['normalTickets']) ? (int)$_POST['normalTickets'] : 0;
    $reducedTickets = isset($_POST['reducedTickets']) ? (int)$_POST['reducedTickets'] : 0;

    if ($normalTickets < 0 || $reducedTickets < 0) {
        die("Invalid ticket quantities.");
    }

    try {
        
        $sql = "INSERT INTO tickets (eventId, normalTickets, reducedTickets) VALUES (:eventId, :normalTickets, :reducedTickets)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':eventId' => $eventId,
            ':normalTickets' => $normalTickets,
            ':reducedTickets' => $reducedTickets,
        ]);

        echo "Tickets purchased successfully!";
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
} else {
    die("Invalid request.");
}
?>