<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ULAB CLUBS</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
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
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }
        .card {
            background-color: white;
            padding: 20px;
            width: 250px;
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
        .modal {
            display: none;
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background-color: rgba(0,0,0,0.6);
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background-color: white;
            padding: 20px;
            border-radius: 6px;
            width: 400px;
            max-width: 90%;
        }
        .modal-content input {
            width: 100%;
            margin: 10px 0;
            padding: 8px;
        }
        .modal-content button {
            padding: 10px;
            background-color: #004080;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 4px;
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
            <button onclick="openModal('loginModal')">Login</button>
            <button onclick="openModal('registerModal')">Register</button>
            <button onclick="location.href='home.php'">Home</button>
        <?php endif; ?>
    </div>
</header>

<div class="container" id="clubsContainer"></div>

<!-- Login Modal -->
<div class="modal" id="loginModal">
    <div class="modal-content">
        <h3>Login</h3>
        <input type="email" id="loginEmail" placeholder="Email">
        <input type="password" id="loginPassword" placeholder="Password">
        <button onclick="loginUser()">Login</button>
    </div>
</div>

<!-- Register Modal -->
<div class="modal" id="registerModal">
    <div class="modal-content">
        <h3>Register</h3>
        <input type="text" id="regName" placeholder="Name">
        <input type="email" id="regEmail" placeholder="Email">
        <input type="password" id="regPassword" placeholder="Password">
        <input type="text" id="regPhone" placeholder="Phone">
        <button onclick="registerUser()">Register</button>
    </div>
</div>

<!-- Club Details Modal -->
<div class="modal" id="detailsModal">
    <div class="modal-content" id="clubDetailsContent"></div>
</div>

<script>
function openModal(id) {
    document.getElementById(id).style.display = 'flex';
}
function closeModal(id) {
    document.getElementById(id).style.display = 'none';
}

window.onclick = function(event) {
    document.querySelectorAll('.modal').forEach(modal => {
        if (event.target === modal) {
            modal.style.display = "none";
        }
    });
}

function loginUser() {
    fetch('login.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({
            email: document.getElementById('loginEmail').value,
            password: document.getElementById('loginPassword').value
        })
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
        if (data.status === 'success') location.reload();
    });
}

function registerUser() {
    fetch('signup.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({
            name: document.getElementById('regName').value,
            email: document.getElementById('regEmail').value,
            password: document.getElementById('regPassword').value,
            phone: document.getElementById('regPhone').value
        })
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
        if (data.status === 'success') closeModal('registerModal');
    });
}

function fetchClubs() {
    fetch('get_all_clubs.php')
    .then(res => res.json())
    .then(data => {
        const container = document.getElementById('clubsContainer');
        data.clubs.forEach(club => {
            const card = document.createElement('div');
            card.className = 'card';
            card.innerHTML = `
                <img src="${club.img_path}" alt="${club.name}">
                <h3>${club.name}</h3>
                <button onclick="showClubDetails(${club.id})">Details</button>
            `;
            container.appendChild(card);
        });
    });
}

function showClubDetails(clubId) {
    fetch(`get_club_by_id.php?club_id=${clubId}`)
    .then(res => res.json())
    .then(data => {
        const modalContent = document.getElementById('clubDetailsContent');
        if (data.status === 'success') {
            const c = data.club;
            modalContent.innerHTML = `
                <h2>${c.name}</h2>
                <img src="${c.img_path}" style="width: 100%; height: 200px; object-fit: cover;">
                <p><strong>Motto:</strong> ${c.moto}</p>
                <p><strong>Advisor:</strong> ${c.advisor}</p>
                <p><strong>Total Members:</strong> ${c.total_member}</p>
                ${data.response_code === 1 ? '<p>✅ Already joined</p>' : `<button onclick="joinClub(${clubId})">Join Now</button>`}
            `;
        } else {
            modalContent.innerHTML = `<p>${data.message}</p>`;
        }
        openModal('detailsModal');
    });
}

function joinClub(clubId) {
    fetch('join.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ club_id: clubId })
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
        closeModal('detailsModal');
    });
}

fetchClubs();


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

</script>
</body>
</html>