<?php
include '../includes/config.php';
include '../includes/auth.php';
adminOnly();

// User growth chart data
$growthData = $conn->query("
    SELECT DATE(created_at) as date, COUNT(*) as count 
    FROM users 
    GROUP BY DATE(created_at) 
    ORDER BY date
");

// Commission data
$commissionData = $conn->query("
    SELECT type, SUM(amount) as total 
    FROM commissions 
    GROUP BY type
");

include '../includes/header.php';
?>

<div class="container-fluid">
    <h2>System Reports</h2>
    
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    User Growth
                </div>
                <div class="card-body">
                    <canvas id="growthChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    Commission Types
                </div>
                <div class="card-body">
                    <canvas id="commissionChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// User Growth Chart
new Chart(document.getElementById('growthChart'), {
    type: 'line',
    data: {
        labels: [<?php while($row = $growthData->fetch_assoc()) echo "'".$row['date']."',"; ?>],
        datasets: [{
            label: 'New Users',
            data: [<?php $growthData->data_seek(0); 
                   while($row = $growthData->fetch_assoc()) echo $row['count'].','; ?>],
            borderColor: 'rgb(75, 192, 192)',
            tension: 0.1
        }]
    }
});

// Commission Chart
new Chart(document.getElementById('commissionChart'), {
    type: 'pie',
    data: {
        labels: [<?php while($row = $commissionData->fetch_assoc()) echo "'".$row['type']."',"; ?>],
        datasets: [{
            data: [<?php $commissionData->data_seek(0); 
                   while($row = $commissionData->fetch_assoc()) echo $row['total'].','; ?>],
            backgroundColor: [
                'rgb(255, 99, 132)',
                'rgb(54, 162, 235)',
                'rgb(255, 205, 86)'
            ]
        }]
    }
});
</script>

<?php include '../includes/footer.php'; ?>