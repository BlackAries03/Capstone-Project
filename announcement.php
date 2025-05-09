<?php include 'getName.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="sidebarStyle.css">
    <title>Announcement</title>
    <style>
        .content {
            margin-left: 300px;
            height: 100vh;
        }

        #title {
            display: flex;
            align-items: center;
            width: 100%;
        }

        #title h1 {
            margin-left: 10px;
            font-size: 2.5em;
        }

        .announcement-container {
            border: 1px solid #ccc;
            border-radius: 10px;
            padding: 20px;
            margin-top: 10px;
            width: 100%;
            height: 700px;
            position: relative;
            background: grey;
            overflow-y: auto;
        }

        .white-container {
            width: 100%;
            height: calc(100% - 50px);
            border-radius: 10px;
            border: 1px solid #ccc;
            padding: 10px;
            background: white;
            overflow-y: auto;
        }

        .announcement {
            border-bottom: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 10px;
        }

        .announcement h2 {
            margin: 0;
            font-size: 2em;
        }

        .announcement p {
            margin: 5px 0;
        }

        .update-type {
            text-decoration: underline;
        }

        /* Modal styles */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        .modal {
            background: white;
            padding: 20px;
            border-radius: 10px;
            width: 80%;
            max-width: 600px;
            max-height: 80%;
            overflow-y: auto;
            position: relative;
            padding-bottom: 60px;
        }

        .modal table {
            width: 100%;
            border-collapse: collapse;
        }

        .modal th, .modal td {
            padding: 8px;
            text-align: left;
            border: 1px solid #ccc;
            word-wrap: break-word;
            white-space: normal;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .modal tbody {
            display: block;
            max-height: 300px;
            overflow-y: auto;
        }

        .modal thead, .modal tbody tr {
            display: table;
            width: 100%;
            table-layout: fixed;
            height: 50px;
            overflow-y: auto;
        }

        .close-button {
            background: red;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            cursor: pointer;
            position: fixed;
            top: 20px;
            right: 20px;
        }

        .update-button {
            background: blue;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            cursor: pointer;
            position: absolute;
            bottom: 10px;
            right: 20px;
        }

        .row-checkbox {
            width: 20px;
            height: 20px;
            cursor: pointer;
        }

        .delete-button {
            background: darkgreen;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            cursor: pointer;
            position: absolute;
            bottom: 10px;
            right: 500px;
        }

        .announcement-title {
            padding: 15px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .announcement-title:hover {
            background-color: #f5f5f5;
        }

        .announcement-modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .announcement-modal {
            background: white;
            padding: 20px;
            border-radius: 10px;
            width: 80%;
            max-width: 600px;
            max-height: 80vh;
            overflow-y: auto;
            position: relative;
        }

        .announcement-modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .announcement-modal-content {
            overflow-x: auto;
            word-wrap: break-word;
            white-space: normal;
            margin-bottom: 60px;
        }

        .modal-close {
            position: absolute;
            top: 10px;
            right: 10px;
            padding: 5px 10px;
            background: red;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .modal-button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            color: white;
        }

        .update-btn {
            background: #007bff;
        }

        .delete-btn {
            background: #dc3545;
        }

        .modal td:nth-child(5) { 
            display: -webkit-box;
            -webkit-line-clamp: 5; 
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: normal;
        }
    </style>
    <script>
        window.onload = function () {
            fetch('fetch_announcements.php')
                .then(response => response.text())
                .then(data => {
                    document.getElementById('announcementBox').innerHTML = data;
                });
        }

        function openEditModal() {
            document.getElementById('editModalOverlay').style.display = 'flex';

            fetch('fetch_announcements2.php')
                .then(response => response.json())
                .then(data => {
                    const tableBody = document.getElementById('editTableBody');
                    tableBody.innerHTML = '';

                    data.forEach(item => {
                        const row = `<tr>
                            <td><input type="checkbox" class="row-checkbox"></td>
                            <td contenteditable="true">${item.aID}</td>
                            <td contenteditable="true">${item.title}</td>
                            <td contenteditable="true">${item.updateType}</td>
                            <td contenteditable="true" class="description-cell">${item.description}</td>
                        </tr>`;
                        tableBody.insertAdjacentHTML('beforeend', row);
                    });

                    // Add event listeners for focus and blur
                    document.querySelectorAll('.description-cell').forEach(cell => {
                        cell.addEventListener('focus', function() {
                            this.style.display = 'block';
                            this.style.overflow = 'visible';
                            this.style.whiteSpace = 'normal';
                            this.style.webkitLineClamp = 'none';
                        });

                        cell.addEventListener('blur', function() {
                            this.style.display = '-webkit-box';
                            this.style.overflow = 'hidden';
                            this.style.whiteSpace = 'normal';
                            this.style.webkitLineClamp = '5';
                        });
                    });
                });
        }

        function closeModal() {
            document.getElementById('editModalOverlay').style.display = 'none';
        }

        function updateAnnouncements() {
            const tableBody = document.getElementById('editTableBody');
            const rows = tableBody.getElementsByTagName('tr');
            const updates = [];

            for (let row of rows) {
                const cells = row.getElementsByTagName('td');
                const update = {
                    aID: cells[1].innerText,
                    title: cells[2].innerText,
                    updateType: cells[3].innerText,
                    description: cells[4].innerText
                };
                updates.push(update);
            }

            fetch('update_announcements.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(updates)
            })
            .then(response => response.text())
            .then(data => {
                alert(data);
                fetch('fetch_announcements.php')
                    .then(response => response.text())
                    .then(data => {
                        document.getElementById('announcementBox').innerHTML = data;
                    });
                closeModal();
            });
        }

        function deleteAnnouncements() {
            const checkboxes = document.querySelectorAll('.row-checkbox');
            const idsToDelete = []; 

            checkboxes.forEach((checkbox, index) => {
                if (checkbox.checked) {
                    const row = checkbox.closest('tr');
                    const aID = row.querySelector('td:nth-child(2)').innerText;
                    idsToDelete.push(aID); 
                    row.remove(); 
                }
            });

            if (idsToDelete.length > 0) {
                fetch('delete_announcements.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ ids: idsToDelete }),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Selected announcements deleted successfully!');
                    } 
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while deleting announcements.');
                });
            } else {
                alert('No announcements selected for deletion.');
            }
        }

        let currentAnnouncementId = null;

        function showAnnouncementDetails(aID) {
            currentAnnouncementId = aID;
            fetch(`get_announcement_details.php?id=${aID}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('modalTitle').textContent = data.title;
                    document.getElementById('modalUpdateType').textContent = data.updateType;
                    document.getElementById('modalTimestamp').textContent = data.timestamp;
                    document.getElementById('modalDescription').textContent = data.description;
                    document.getElementById('announcementModalOverlay').style.display = 'flex';
                });
        }

        function closeAnnouncementModal() {
            document.getElementById('announcementModalOverlay').style.display = 'none';
            currentAnnouncementId = null;
        }

        function editAnnouncement(aID) {
            openEditModal();
            closeAnnouncementModal();
        }

        function deleteAnnouncement(aID) {
            if (confirm('Are you sure you want to delete this announcement?')) {
                fetch('delete_announcements.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ ids: [aID] }),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Announcement deleted successfully!');
                        closeAnnouncementModal();
                        fetch('fetch_announcements.php')
                            .then(response => response.text())
                            .then(data => {
                                document.getElementById('announcementBox').innerHTML = data;
                            });
                    }
                });
            }
        }

        document.addEventListener('DOMContentLoaded', (event) => {
            const theme = localStorage.getItem('theme') || 'default';
            if (theme === 'dark') {
                document.body.classList.add('dark');
            }
        });
    </script>
</head>
<body>
    <?php include("sidebar.php"); ?>
    <div class="content">
        <div id="title">
            <h1>Announcement</h1>
        </div>
        <div class="announcement-container">
            <div class="white-container" id="announcementBox"></div>
        </div>
    </div>

    <div id="editModalOverlay" class="modal-overlay">
        <div class="modal">
            <table>
                <thead>
                    <tr>
                        <th></th>
                        <th>aID</th>
                        <th>Title</th>
                        <th>Update Type</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody id="editTableBody"></tbody>
            </table>
            <button class="update-button" onclick="updateAnnouncements()">Update</button>
            <button class="delete-button" onclick="deleteAnnouncements()">Delete</button>
        </div>
        <button class="close-button" onclick="closeModal()">Close</button>
    </div>

    <div id="announcementModalOverlay" class="announcement-modal-overlay">
        <div class="announcement-modal">
            <button class="modal-close" onclick="closeAnnouncementModal()">×</button>
            <div class="announcement-modal-header">
                <h2 id="modalTitle"></h2>
            </div>
            <div class="announcement-modal-content">
                <p><strong>Update Type: </strong><span id="modalUpdateType"></span></p>
                <p><strong>Time: </strong><span id="modalTimestamp"></span></p>
                <p><strong>Description: </strong></p>
                <p id="modalDescription"></p>
            </div>
        </div>
    </div>

    <style>
    body.dark {
        background: #2e2e2e;
        color: #e1e1e1;
    }

    .dark .content {
        background: #2e2e2e;
    }

    .dark #title h1 {
        color: #e1e1e1;
    }

    .dark .announcement-container {
        background: #4c4c4c;
        border-color: #555;
    }

    .dark .white-container {
        background: #3c3c3c;
        border-color: #555;
        color: #e1e1e1;
    }

    .dark .announcement {
        border-bottom-color: #555;
    }

    .dark .announcement-title {
        border-color: #555;
    }

    .dark .announcement-title:hover {
        background-color: #4c4c4c;
    }

    .dark .modal {
        background: #3c3c3c;
        color: #e1e1e1;
    }

    .dark .modal th, 
    .dark .modal td {
        border-color: #555;
        color: #e1e1e1;
    }

    .dark .modal td[contenteditable="true"]:focus {
        background: #4c4c4c;
    }

    .dark .announcement-modal {
        background: #3c3c3c;
        color: #e1e1e1;
    }

    .dark .modal-close,
    .dark .close-button {
        background: #dc3545;
        color: #fff;
    }

    .dark .update-button {
        background: #007bff;
        color: #fff;
    }

    .dark .delete-button {
        background: #28a745;
        color: #fff;
    }

    .dark .announcement-modal-content {
        color: #e1e1e1;
    }

    .dark .modal td:nth-child(5) { 
        background: #3c3c3c;
    }

    .dark .modal td:nth-child(5):focus { 
        background: #4c4c4c;
    }

    .dark .announcement-title {
        background: #3c3c3c;
        color: #e1e1e1;
    }

    .dark .announcement-title:hover {
        background: #4c4c4c;
    }
    </style>
</body>
</html>
