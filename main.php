<?php
require "config.php";

$searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';
$category = isset($_GET['category']) ? trim($_GET['category']) : '';
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : '';

try {
    // Prepare the base SQL query
    $sql = "SELECT e.eventId, e.eventTitle, e.eventDescription, e.TariffNormal, e.TariffReduit, ed.dateEvent, ed.timeEvent, ed.image 
            FROM evenement e
            INNER JOIN edition ed ON e.eventId = ed.eventId
            WHERE e.eventTitle LIKE :searchTerm";

    // Add category filter if selected
    if (!empty($category)) {
        $sql .= " AND e.eventType = :category";
    }

    // Add date range filter if provided
    if (!empty($startDate)) {
        $sql .= " AND ed.dateEvent >= :startDate";
    }
    if (!empty($endDate)) {
        $sql .= " AND ed.dateEvent <= :endDate";
    }

    $sql .= " ORDER BY ed.dateEvent ASC";

    // Prepare the statement
    $stmt = $pdo->prepare($sql);

    // Bind parameters
    $params = [
        ':searchTerm' => '%' . $searchTerm . '%',
    ];
    if (!empty($category)) {
        $params[':category'] = $category;
    }
    if (!empty($startDate)) {
        $params[':startDate'] = $startDate;
    }
    if (!empty($endDate)) {
        $params[':endDate'] = $endDate;
    }

    // Execute the query
    $stmt->execute($params);
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events - FarhaEvents</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    

<nav class="navbar">
    <div class="container">
        <a href="main.php" class="logo">
            <img src="images/logo.png" alt="FarhaEvents Logo">
        </a>
        <div class="nav-links">
            <?php if (isset($_SESSION['user_name'])): ?>
                <span class="nav-user">Welcome, <?= htmlspecialchars($_SESSION['user_name']) ?></span>
                <a href="logout.php" class="nav-link">Logout</a>
            <?php else: ?>
                <a href="login.php" class="nav-link">Login</a>
                <a href="register.php" class="nav-link">Register</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<div class="hero-section">
    <div class="hero-content">
        <h1>Welcome to FarhaEvents</h1>
        <p>Discover and book your favorite events with ease.</p>
        <form action="main.php" method="GET" class="filter-form">
            <input type="text" name="search" placeholder="Search for events..." class="filter-input">
            <select name="category" class="filter-select">
                <option value="">All Categories</option>
                <option value="Music">Music</option>
                <option value="Theater">Theater</option>
                <option value="Cinema">Cinema</option>
                <option value="Meetings">Meetings</option>
            </select>
            <input type="date" name="start_date" class="filter-date">
            <input type="date" name="end_date" class="filter-date">
            <button type="submit" class="filter-button">Search</button>
        </form>
    </div>
</div>

   
<div class="events-container">
    <h1>Upcoming Events</h1>
    <?php if (!empty($events)): ?>
        <div class="events-list">
            <?php foreach ($events as $event): ?>
                <div class="event-card">
                    <a href="event_details.php?eventId=<?= htmlspecialchars($event['eventId']) ?>">
                        <img src="<?= htmlspecialchars($event['image']) ?>" alt="<?= htmlspecialchars($event['eventTitle']) ?>" class="event-image">
                    </a>
                    <div class="event-details">
                        <h2>
                            <a href="event_details.php?eventId=<?= htmlspecialchars($event['eventId']) ?>">
                                <?= htmlspecialchars($event['eventTitle']) ?>
                            </a>
                        </h2>
                        <p><?= htmlspecialchars($event['eventDescription']) ?></p>
                        <p><strong>Date:</strong> <?= htmlspecialchars($event['dateEvent']) ?> at <?= htmlspecialchars($event['timeEvent']) ?></p>
                        <p><strong>Normal Tariff:</strong> $<?= htmlspecialchars($event['TariffNormal']) ?></p>
                        <p><strong>Reduced Tariff:</strong> $<?= htmlspecialchars($event['TariffReduit']) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>No events found for your search.</p>
    <?php endif; ?>
</div>
</body>
</html>