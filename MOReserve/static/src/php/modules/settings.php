<script>
    const urlParam = new URLSearchParams(window.location.search);
    const id = urlParam.get('id');
</script>
<?php 
    include("static/src/php/conn.php");

    $id = $_GET["id"];
    $name = "";
    $surname = "";
    $username = "";
    $icon = "";
    $login = "";

    $stmt = $db->prepare("
        SELECT 
            username,
            name,
            surname,
            icon,
            login
        FROM users
        WHERE id = ?;
    ");
    $stmt->bind_param("i", $id);

    if (!$stmt->execute()) {
        die('Query failed: ' . $stmt->error);
    }

    $stmt->bind_result($username, $name, $surname, $icon, $login);

    if ($stmt->fetch()) {
        $fullName = $name . " " . $surname;
    } else $fullName = "Unknown name";

    $stmt->close();
?>
<div class="p-6 space-y-6">
    <section class="bg-gray-800 shadow rounded-lg p-6 space-y-4 text-gray-200">
        <h4 class="text-lg font-medium">Profile Settings</h4>
        <div class="flex items-center gap-6">
            <div class="relative group" x-data="{ isDragging: false }" 
                 @dragover.prevent="isDragging = true" 
                 @dragleave="isDragging = false" 
                 @drop.prevent="isDragging = false">
                <img src="static/img/users/pfp/<?php echo $icon; ?>" alt="Profile Picture" class="w-20 h-20 rounded-full border-4 border-gray-700 shadow">
                <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition">
                    <button class="text-sm bg-teal-500 px-3 py-1 rounded" @click="$refs.pfpInput.click()">Change</button>
                </div>
                <input type="file" x-ref="pfpInput" class="hidden" @change="alert('Profile picture updated!')">
                <div x-show="isDragging" class="absolute inset-0 bg-teal-500 bg-opacity-20 flex items-center justify-center rounded-full">
                    <p class="text-sm font-medium">Drop to upload</p>
                </div>
            </div>
            <div>
                <p class="text-xl font-medium" id="fullName"><?php echo $fullName; ?></p>
                <p class="text-sm text-gray-400" id="username"><?php echo $username; ?></p>
            </div>
        </div>
    </section>

    <section class="bg-gray-800 shadow rounded-lg p-6 space-y-4">
        <h4 class="text-lg font-medium">Change Password</h4>
        <form id="password-form" class="space-y-4">
            <div>
                <label for="current-password" class="block text-sm font-medium">Current Password:</label>
                <input id="current-password" type="password" class="w-full px-4 py-2 bg-gray-600 rounded-lg">
            </div>
            <div>
                <label for="new-password" class="block text-sm font-medium">New Password:</label>
                <input id="new-password" type="password" class="w-full px-4 py-2 bg-gray-600 rounded-lg">
            </div>
            <button type="button" class="w-full bg-teal-600 py-2 rounded-lg hover:bg-teal-700" onclick="updatePassword()">Update Password</button>
        </form>
    </section>

    <section class="bg-gray-800 shadow rounded-lg p-6 space-y-4">
        <h4 class="text-lg font-medium">Update Email</h4>
        <div class="space-y-4" x-data="{ showPopup: false, code: '' }">
            <div>
                <label for="new-email" class="block text-sm font-medium">New Email:</label>
                <input id="new-email" type="email" class="w-full px-4 py-2 bg-gray-600 rounded-lg">
            </div>
            <button type="button" class="w-full bg-teal-600 py-2 rounded-lg hover:bg-teal-700" onclick="updateEmail()">Change Email</button>
        </div>

        <h4 class="text-lg font-medium mt-6">Update Username</h4>
        <div class="space-y-4">
            <div>
                <label for="new-username" class="block text-sm font-medium">New Username:</label>
                <input id="new-username" type="text" class="w-full px-4 py-2 bg-gray-600 rounded-lg">
            </div>
            <button type="button" class="w-full bg-teal-600 py-2 rounded-lg hover:bg-teal-700" onclick="updateUsername()">Change Username</button>
        </div>
    </section>

    <section class="bg-gray-800 shadow rounded-lg p-6 space-y-4">
        <h4 class="text-lg font-medium">Account Management</h4>
        <div class="space-y-4">
            <button class="w-full bg-red-600 py-2 rounded-lg hover:bg-red-700" onclick="Logout();">Log Out</button>
            <button class="w-full bg-red-800 py-2 rounded-lg hover:bg-red-900" onclick="Delete();">Delete Account</button>
        </div>
    </section>
</div>

<footer class="mt-6 text-center text-gray-400 text-sm">
    <p>Last Logged In: <span id="lastLogin"><?php echo htmlspecialchars($login); ?></span></p>
</footer>

<script>
    async function updatePassword() {
        const currentPassword = document.getElementById('current-password').value;
        const newPassword = document.getElementById('new-password').value;

        if (!currentPassword || !newPassword) {
            alert("Please fill in both fields.");
            return;
        }

        const response = await fetch('static/src/php/pass.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                id: id,
                currentPassword: currentPassword,
                newPassword: newPassword,
            }),
        });

        const result = await response.json();
        if (result.success) {
            alert("Password updated successfully!");
            document.getElementById('current-password').value = "";
            document.getElementById('new-password').value = "";
            location.reload();
        } else {
            alert("Error: " + result.error);
            document.getElementById('current-password').value = "";
            document.getElementById('new-password').value = "";
        }
    }

    async function updateEmail() {
        const newEmail = document.getElementById('new-email').value;

        if (!newEmail) {
            alert("Please enter a new email.");
            return;
        }

        const response = await fetch('static/src/php/email.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                id: id,
                newEmail: newEmail,
            }),
        });

        const result = await response.json();
        if (result.success) {
            alert("Email updated successfully!");
            document.getElementById("new-email").value = "";
            location.reload();
        } else {
            alert("Error: " + result.error);
        }
    }

    async function updateUsername() {
        const newUsername = document.getElementById('new-username').value;

        if (!newUsername) {
            alert("Please enter a new username.");
            return;
        }

        const response = await fetch('static/src/php/user.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                id: id,
                newUsername: newUsername,
            }),
        });

        const result = await response.json();
        if (result.success) {
            alert("Username updated successfully!");
            document.getElementById("new-username").value = "";
            location.reload();
        } else {
            alert("Error: " + result.error);
        }
    }

    async function Logout() {
        const response = await fetch('static/src/php/logout.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                id: id
            }),
        });

        const result = await response.json();
        if (result.success) {
            alert("You are being logged out");
            window.location.href = "index.html";
        } else {
            alert("Error: " + result.error);
        }
    }

    async function Delete() {
        const response = await fetch('static/src/php/del.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                id: id
            }),
        });

        const result = await response.json();
        if (result.success) {
            alert("Your account has been deleted");
            window.location.href = "index.html";
        } else {
            alert("Error: " + result.error);
        }
    }
</script>
