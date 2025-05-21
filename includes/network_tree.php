<?php
// Get current user's downline
$user_id = $_SESSION['user_id'];
$downline = $conn->query("
    SELECT id, username, sponsor_id, position 
    FROM users 
    WHERE sponsor_id = $user_id
");

if ($downline->num_rows > 0): ?>
    <ul class="network-tree">
        <?php while($member = $downline->fetch_assoc()): ?>
            <li>
                <span class="node"><?= $member['username'] ?> (ID: <?= $member['id'] ?>)</span>
                <?php 
                // Recursive function for multi-level (simplified)
                $sub_downline = $conn->query("SELECT id, username FROM users WHERE sponsor_id = {$member['id']}");
                if ($sub_downline->num_rows > 0): ?>
                    <ul>
                        <?php while($sub_member = $sub_downline->fetch_assoc()): ?>
                            <li><?= $sub_member['username'] ?> (ID: <?= $sub_member['id'] ?>)</li>
                        <?php endwhile; ?>
                    </ul>
                <?php endif; ?>
            </li>
        <?php endwhile; ?>
    </ul>
<?php else: ?>
    <p>No downline members yet. Share your referral link!</p>
<?php endif; ?>