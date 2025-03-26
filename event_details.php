
<?php
require "config.php";

if (!isset($_GET['eventId'])) {
    die("Event ID is required.");
}

$eventId = $_GET['eventId'];

try {
    // Fetch event details
    $sql = "SELECT e.eventId, e.eventTitle, e.eventDescription, e.TariffNormal, e.TariffReduit, ed.dateEvent, ed.timeEvent, ed.image, ed.NumSalle
            FROM evenement e
            INNER JOIN edition ed ON e.eventId = ed.eventId
            WHERE e.eventId = :eventId";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':eventId' => $eventId]);
    $event = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$event) {
        die("Event not found.");
    }
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($event['eventTitle']) ?> - FarhaEvents</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <a href="main.php" class="logo">
                <img src="images/2.png" alt="FarhaEvents Logo">
            </a>
        </div>
    </nav>

    <div class="event-details-container">
        <img src="<?= htmlspecialchars($event['image']) ?>" alt="<?= htmlspecialchars($event['eventTitle']) ?>" class="event-details-image">
        <div class="event-details-content">
            <h1><?= htmlspecialchars($event['eventTitle']) ?></h1>
            <p><?= htmlspecialchars($event['eventDescription']) ?></p>
            <p><strong>Date:</strong> <?= htmlspecialchars($event['dateEvent']) ?> at <?= htmlspecialchars($event['timeEvent']) ?></p>
            <p><strong>Normal Tariff:</strong> $<?= htmlspecialchars($event['TariffNormal']) ?></p>
            <p><strong>Reduced Tariff:</strong> $<?= htmlspecialchars($event['TariffReduit']) ?></p>
            <p><strong>Room Number:</strong> <?= htmlspecialchars($event['NumSalle']) ?></p>

            <form action="process_tickets.php" method="POST" class="ticket-form">
                <input type="hidden" name="eventId" value="<?= htmlspecialchars($event['eventId']) ?>">
                <div class="form-group">
                    <label for="normalTickets">Number of Normal Tickets (Adults):</label>
                    <input type="number" name="normalTickets" id="normalTickets" min="0" value="0">
                </div>
                <div class="form-group">
                    <label for="reducedTickets">Number of Reduced Tickets (Minors/Students):</label>
                    <input type="number" name="reducedTickets" id="reducedTickets" min="0" value="0">
                </div>
                <button type="submit" class="submit-button">Purchase Tickets</button>
            </form>

            <a href="main.php" class="back-button">Back to Events</a>
        </div>
    </div>
</body>
</html>