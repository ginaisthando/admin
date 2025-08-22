<?php
require_once 'config/auth.php';

// User authentication
if (!is_user_authenticated()) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="HandheldFriendly" content="true">
    <meta charset="utf-8">
    <title>Joe's Coaches</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="nav-wrapper">
        <nav>
            <ul class="nav-list">
                <img id="icon" src="Pics/icon.png" alt="">
                <li class="nav-item"><a href="index.php">Account</a></li>
                <li><a href="#" id="logoutLink">Logout</a></li>
            </ul>
        </nav>
    </div>

    <div class="employees">
        <h2>Employees</h2>
        <div class="employee-list">
            <li>
                <div class="employee-card" id="employee1">
                    <img class="user-img" src="Pics/hs1.png" alt="">
                    <span>
                        <div class="employment-details" id="employee1-details">
                            <span class="name-format">Ashton</span>
                            <span class="name-format">Jacobs</span>
                            <br>
                            <span>Developer</span>
                            <br>
                            <span>Tech Department</span>
                            <br>
                            <span>ajacobs@jcoaches.com</span>
                            <br>
                            <span>£35000</span>
                        </div>
                    </span>
                </div>
            </li>
            <li>
                <div class="employee-card" id="employee2">
                    <img class="user-img" src="Pics/hs2.png" alt="">
                    <span>
                        <div class="employment-details" id="employee2-details">
                            <span class="name-format">Pamela</span>
                            <span class="name-format">Peterson</span>
                            <br>
                            <span>Head of Marketing</span>
                            <br>
                            <span>Marketing Department</span>
                            <br>
                            <span>ppeterson@jcoaches.com</span>
                            <br>
                            <span>£45000</span>
                        </div>
                    </span>
                </div>
            </li>
            <li>
                <div class="employee-card" id="employee3">
                    <img class="user-img" src="Pics/hs3.png" alt="">
                    <span>
                        <div class="employment-details" id="employee3-details">
                            <span class="name-format">Joe</span>
                            <span class="name-format">Adams</span>
                            <br>
                            <span>CEO</span>
                            <br>
                            <span>Executive Department</span>
                            <br>
                            <span>jadams@jcoaches.com</span>
                            <br>
                            <span>£80000</span>
                        </div>
                    </span>
                </div>
            </li>
            <li>
                <div class="employee-card" id="employee4">
                    <img class="user-img" src="Pics/hs4.png" alt="">
                    <span>
                        <div class="employment-details" id="employee4-details">
                            <span class="name-format">Jessica </span>
                            <span class="name-format">Bloom</span>
                            <br>
                            <span>Head of Transport</span>
                            <br>
                            <span>Transport Department</span>
                            <br>
                            <span>gmail.com</span>
                            <br>
                            <span>£55000</span>
                        </div>
                    </span>
                </div>
            </li>
        </div>
    </div>

    <div class="interviews">
        <h2>Upcoming Interviews</h2>
        <li>
            <div class="list-item">
                <h3>Marketing</h3>
                <p>Social Media Content Specialist</p>
                <button class="accept-btn" type="button" name="button">Accept</button>
                <button class="reject-btn" type="button" name="button">Reject</button>
            </div>
        </li>
        <li>
            <div class="list-item">
                <h3>Driver</h3>
                <p>Advanced Coach Driver</p>
                <button class="accept-btn" type="button" name="button">Accept</button>
                <button class="reject-btn" type="button" name="button">Reject</button>
            </div>
        </li>
        <li>
            <div class="list-item">
                <h3>Sofware Developer</h3>
                <p>Full-stack Developer</p>
                <button class="accept-btn" type="button" name="button">Accept</button>
                <button class="reject-btn" type="button" name="button">Reject</button>
            </div>
        </li>
    </div>

    <div id="employeeModal" class="modal">
        <div class="modal-content">
            <header class="modal-header">
                <div class="modal-header-content">
                    <span>Add employee</span>
                    <button onclick="closeEmployeeModal()" class="close-btn">X</button>
                </div>
            </header>
            <div class="modal-container">
                <label>First Name</label>
                <input type="text" id="name"><br>
                <label>Last Name</label>
                <input id="surname" type="text"><br>
                <label>Position</label>
                <input id="position" type="text"><br>
                <label>Department</label>
                <input id="department" type="text"><br>
                <label>Email</label>
                <input id="email" type="text"><br>
                <label>Salary</label>
                <input id="salary" type="text"><br>
                <button id="confirmBtn" type="button" onclick="addEmployee()">Confirm</button>
            </div>
        </div>
    </div>

    <div id="interviewModal" class="modal">
        <div class="modal-content">
            <header class="modal-header">
                <div class="modal-header-content">
                    <span>Add interview</span>
                    <button onclick="closeInterviewModal()" class="close-btn">X</button>
                </div>
            </header>
            <div class="modal-container">
                <label>Department</label>
                <input type="text" id="int-department"><br>
                <label>Position</label>
                <input id="int-position" type="text"><br>
                <button id="confirmIntBtn" type="button" onclick="addInterview()">Confirm</button>
            </div>
        </div>
    </div>

    <button class="btn" type="button" onclick="showAddIntModal()">Add Interview</button>
    <button class="btn" type="button" onclick="showAddEmpModal()">Add Employee</button>

    <script src="script.js"></script>
    <script>
    document.getElementById('logoutLink').addEventListener('click', async function(e) {
        e.preventDefault();
        try {
            const res = await fetch('api/logout.php', { method: 'POST', headers: { 'Content-Type': 'application/json' } });
            await res.json();
        } catch (err) {}
        window.location.href = 'login.php';
    });
    </script>
</body>
</html>
