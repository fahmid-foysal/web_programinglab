<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Memberships - ULAB CLUBS</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #004080;
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        header h1 {
            margin: 0;
        }

        .nav-links button {
            margin-left: 10px;
            padding: 8px 15px;
            background-color: white;
            color: #004080;
            border: none;
            cursor: pointer;
            border-radius: 4px;
        }

        .container {
            padding: 30px;
        }

        h2 {
            color: #004080;
            margin-bottom: 20px;
        }

        .grid {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .card {
            background-color: white;
            width: 250px;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .card img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 5px;
        }
    </style>
</head>
<body>
<header>
    <h1>ULAB CLUBS</h1>
    <div class="nav-links">
        <?php if (isset($_SESSION['user_id'])): ?>
            <button onclick="location.href='home.php'">Home</button>
            <button onclick="location.href='see_membership.php'">See Membership</button>
            <button onclick="logoutUser()">Logout</button>
        <?php else: ?>
            <button onclick="location.href='home.php'">Home</button>
        <?php endif; ?>
    </div>
</header>

<div class="container">
    <h2>My Joined Clubs</h2>
    <div class="grid" id="joinedClubsContainer"></div>
</div>

<script>
function logoutUser() {
    fetch('logout.php', {
        method: 'POST'
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
        window.location.href = 'home.php';
    });
}

function fetchJoinedClubs() {
    fetch('get_membership.php') // Your GET API
        .then(res => res.json())
        .then(data => {
            const container = document.getElementById('joinedClubsContainer');
            container.innerHTML = '';

            if (data.status === 'success') {
                if (data.joined_clubs.length === 0) {
                    container.innerHTML = '<p>You have not joined any clubs yet.</p>';
                } else {
                    data.joined_clubs.forEach(club => {
                        const card = document.createElement('div');
                        card.className = 'card';
                        card.innerHTML = `
                            <img src="${club.img_path}" alt="${club.name}">
                            <h3>${club.name}</h3>
                        `;
                        container.appendChild(card);
                    });
                }
            } else {
                container.innerHTML = `<p>${data.message}</p>`;
            }
        });
}

fetchJoinedClubs();
</script>
</body>
</html>
