var employeeListHolder = document.querySelector(".employee-list");
var interviewListHolder = document.querySelector(".interviews");
var addEmployeeModal = document.querySelector("#employeeModal");
var addInterviewModal = document.querySelector("#interviewModal");

addInterviewModal.style.display = "none";
addEmployeeModal.style.display = "none";

document.addEventListener('DOMContentLoaded', function() {
    loadEmployees();
    loadInterviews();
    
    document.getElementById('logoutLink').addEventListener('click', function(e) {
        e.preventDefault();
        logout();
    });
});

function showAddEmpModal() {
    addEmployeeModal.style.display = "block";
}

function showAddIntModal() {
    addInterviewModal.style.display = "block";
}

function closeEmployeeModal() {
    addEmployeeModal.style.display = "none";
}

function closeInterviewModal() {
    addInterviewModal.style.display = "none";
}

async function addEmployee() {
    var name = document.querySelector("#name").value;
    var surname = document.querySelector("#surname").value;
    var position = document.querySelector("#position").value;
    var department = document.querySelector("#department").value;
    var email = document.querySelector("#email").value;
    var salary = document.querySelector("#salary").value;

    // Validate input
    if (!name || !surname || !position || !department || !email || !salary) {
        alert('Please fill in all fields');
        return;
    }

    try {
        const response = await fetch('./api/add_employee.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                first_name: name,
                last_name: surname,
                position: position,
                department: department,
                email: email,
                salary: salary
            })
        });

        if (response.status === 401) {
            window.location.href = 'login.php';
            return;
        }
        const result = await response.json();

        if (result.success) {

            document.querySelector("#name").value = '';
            document.querySelector("#surname").value = '';
            document.querySelector("#position").value = '';
            document.querySelector("#department").value = '';
            document.querySelector("#email").value = '';
            document.querySelector("#salary").value = '';
            

            loadEmployees();
            closeEmployeeModal();
            alert('Employee added successfully!');
        } else {
            alert('Error: ' + result.error);
        }
    } catch (error) {
        alert('Error adding employee: ' + error.message);
    }
}

async function addInterview() {
    var departmentInterview = document.querySelector("#int-department").value;
    var positionInterview = document.querySelector("#int-position").value;

    // Validate input
    if (!departmentInterview || !positionInterview) {
        alert('Please fill in both department and position');
        return;
    }

    try {
        const response = await fetch('./api/add_interview.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                department: departmentInterview,
                position: positionInterview
            })
        });

        if (response.status === 401) {
            window.location.href = 'login.php';
            return;
        }
        const result = await response.json();

        if (result.success) {
            document.querySelector("#int-department").value = '';
            document.querySelector("#int-position").value = '';
            
            // Reload interviews to show new data
            loadInterviews();
            closeInterviewModal();
            alert('Interview added successfully!');
        } else {
            alert('Error: ' + result.error);
        }
    } catch (error) {
        alert('Error adding interview: ' + error.message);
    }
}

// Load employees from database
async function loadEmployees() {
    try {
        const response = await fetch('./api/get_employees.php');
        if (response.status === 401) {
            window.location.href = 'login.php';
            return;
        }
        const result = await response.json();

        if (result.success) {
            employeeListHolder.innerHTML = '';
            
            result.employees.forEach(employee => {
                const employeeCard = `
                    <li>
                        <div class="employee-card" id="employee${employee.id}">
                            <img class="user-img" src="Pics/${employee.profile_image}" alt="" onerror="this.src='Pics/hs1.png'">
                            <span>
                                <div class="employment-details" id="employee${employee.id}-details">
                                    <span class="name-format">${employee.first_name}</span>
                                    <span class="name-format">${employee.last_name}</span>
                                    <br>
                                    <span>${employee.position}</span>
                                    <br>
                                    <span>${employee.department_name}</span>
                                    <br>
                                    <span>${employee.email}</span>
                                    <br>
                                    <span>£${employee.salary}</span>
                                    <br>
                                    <button class="reject-btn" type="button" onclick="deleteEmployee(${employee.id})">Delete</button>
                                </div>
                            </span>
                        </div>
                    </li>
                `;
                employeeListHolder.insertAdjacentHTML('beforeend', employeeCard);
            });
        }
    } catch (error) {
        console.error('Error loading employees:', error);
    }
}

// Load interviews from database
async function loadInterviews() {
    try {
        const response = await fetch('./api/get_interviews.php');
        if (response.status === 401) {
            window.location.href = 'login.php';
            return;
        }
        const result = await response.json();

        if (result.success) {
            // Clear existing interviews
            const interviewsList = interviewListHolder.querySelectorAll('li');
            interviewsList.forEach(li => li.remove());
            
            result.interviews.forEach(interview => {
                const interviewItem = `
                    <li>
                        <div class="list-item">
                            <h3>${interview.department_name}</h3>
                            <p>${interview.position}</p>
                            <button class="accept-btn" type="button" onclick="updateInterviewStatus(${interview.id}, 'accepted')">Accept</button>
                            <button class="reject-btn" type="button" onclick="updateInterviewStatus(${interview.id}, 'rejected')">Reject</button>
                            <button class="reject-btn" type="button" onclick="deleteInterview(${interview.id})">Delete</button>
                        </div>
                    </li>
                `;
                interviewListHolder.insertAdjacentHTML('beforeend', interviewItem);
            });
        }
    } catch (error) {
        console.error('Error loading interviews:', error);
    }
}

// Update interview status
async function updateInterviewStatus(interviewId, status) {
    try {
        const response = await fetch('./api/update_interview_status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                interview_id: interviewId,
                status: status
            })
        });
        if (response.status === 401) {
            window.location.href = 'login.php';
            return;
        }
        const result = await response.json();

        if (result.success) {
            alert(`Interview ${status} successfully!`);
            loadInterviews();
        } else {
            alert('Error: ' + result.error);
        }
    } catch (error) {
        alert('Error updating interview status: ' + error.message);
    }
}

// Delete employee
async function deleteEmployee(employeeId) {
    if (!confirm('Are you sure you want to delete this employee?')) return;
    try {
        const response = await fetch('./api/delete_employee.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ employee_id: employeeId })
        });
        if (response.status === 401) {
            window.location.href = 'login.php';
            return;
        }
        const result = await response.json();
        if (result.success) {
            alert('Employee deleted successfully');
            loadEmployees();
        } else {
            alert('Error: ' + result.error);
        }
    } catch (error) {
        alert('Error deleting employee: ' + error.message);
    }
}

// Delete interview
async function deleteInterview(interviewId) {
    if (!confirm('Are you sure you want to delete this interview?')) return;
    try {
        const response = await fetch('./api/delete_interview.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ interview_id: interviewId })
        });
        if (response.status === 401) {
            window.location.href = 'login.php';
            return;
        }
        const result = await response.json();
        if (result.success) {
            alert('Interview deleted successfully');
            loadInterviews();
        } else {
            alert('Error: ' + result.error);
        }
    } catch (error) {
        alert('Error deleting interview: ' + error.message);
    }
}

// Logout function
async function logout() {
    try {
        const response = await fetch('./api/logout.php', {
            method: 'POST'
        });
        
        const result = await response.json();
        
        if (result.success) {
            window.location.href = 'login.php';
        } else {
            alert('Error logging out');
        }
    } catch (error) {
        window.location.href = 'login.php';
    }
}
