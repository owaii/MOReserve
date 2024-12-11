<script>
    const urlParam = new URLSearchParams(window.location.search);
    const id = urlParam.get('id');
</script>
<div class="p-6 space-y-6">
        <section class="bg-gray-800 shadow rounded-lg p-6 space-y-4 text-gray-200">
            <h4 class="text-lg font-medium">Profile Settings</h4>
            <div class="flex items-center gap-6">
                <div class="relative group" x-data="{ isDragging: false }" 
                     @dragover.prevent="isDragging = true" 
                     @dragleave="isDragging = false" 
                     @drop.prevent="isDragging = false">
                    <img src="static/img/users/pfp/astrid.webp" alt="Profile Picture" class="w-20 h-20 rounded-full border-4 border-gray-700 shadow">
                    <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition">
                        <button class="text-sm bg-teal-500 px-3 py-1 rounded" @click="$refs.pfpInput.click()">Change</button>
                    </div>
                    <input type="file" x-ref="pfpInput" class="hidden" @change="alert('Profile picture updated!')">
                    <div x-show="isDragging" class="absolute inset-0 bg-teal-500 bg-opacity-20 flex items-center justify-center rounded-full">
                        <p class="text-sm font-medium">Drop to upload</p>
                    </div>
                </div>
                <div>
                    <p class="text-xl font-medium" id="fullName">Michael Jordan</p>
                    <p class="text-sm text-gray-400" id="username">mjordan</p>
                </div>
            </div>
        </section>

        <section class="bg-gray-800 shadow rounded-lg p-6 space-y-4">
            <h4 class="text-lg font-medium">Change Password</h4>
            <form class="space-y-4" action="static/src/php/pass.php">
                <div>
                    <label for="current-password" class="block text-sm font-medium">Current Password:</label>
                    <input id="current-password" type="password" class="w-full px-4 py-2 bg-gray-600 rounded-lg">
                </div>
                <div>
                    <label for="new-password" class="block text-sm font-medium">New Password:</label>
                    <input id="new-password" type="password" class="w-full px-4 py-2 bg-gray-600 rounded-lg">
                </div>
                <button type="submit" class="w-full bg-teal-600 py-2 rounded-lg hover:bg-teal-700 " onclick="
                window.location.href = 'static/src/php/pass.php?newVal=' + document.getElementById('new-password').value + '&oldVal=' + document.getElementById('current-password').value + '&id=' + id">Update Password</button>
            </form>
        </section>

        <section class="bg-gray-800 shadow rounded-lg p-6 space-y-4">
            <h4 class="text-lg font-medium">Update Email</h4>
            <div class="space-y-4" x-data="{ showPopup: false, code: '' }">
                <div>
                    <label for="new-email" class="block text-sm font-medium">New Email:</label>
                    <input id="new-email" type="email" class="w-full px-4 py-2 bg-gray-600 rounded-lg">
                </div>
                <button class="w-full bg-teal-600 py-2 rounded-lg hover:bg-teal-700" @click="showPopup = true; $dispatch('popup', { type: 'email' })" onclick="
                window.location.href = 'static/src/php/email.php?newVal=' + document.getElementById('new-email').value + '&id=' + id">Change Email</button>
            </div>

            <h4 class="text-lg font-medium mt-6">Update Username</h4>
            <div class="space-y-4" x-data="{ showPopup: false, code: '' }">
                <div>
                    <label for="new-username" class="block text-sm font-medium">New Username:</label>
                    <input id="new-username" type="text" class="w-full px-4 py-2 bg-gray-600 rounded-lg">
                </div>
                <button class="w-full bg-teal-600 py-2 rounded-lg hover:bg-teal-700" @click="showPopup = true; $dispatch('popup', { type: 'username' })" onclick="
                window.location.href = 'static/src/php/user.php?newVal=' + document.getElementById('new-username').value + '&id=' + id">Change Username</button>
            </div>
        </section>

        <section class="bg-gray-800 shadow rounded-lg p-6 space-y-4">
            <h4 class="text-lg font-medium">Account Management</h4>
            <div class="space-y-4">
                <button class="w-full bg-red-600 py-2 rounded-lg hover:bg-red-700">Log Out</button>
                <button class="w-full bg-red-800 py-2 rounded-lg hover:bg-red-900">Delete Account</button>
            </div>
        </section>
    </div>

    <footer class="mt-6 text-center text-gray-400 text-sm">
        <p>Last Logged In: <span id="lastLogin">12/10/2024</span></p>
    </footer>

    <!-- popup -->
    <div x-data="{ showPopup: false, type: '', code: '' }" 
         @popup.window="showPopup = true; type = $event.detail.type;" 
         x-show="showPopup" 
         x-cloak
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
        <div class="bg-gray-800 p-6 rounded-lg shadow">
            <h4 class="text-lg font-medium mb-4" x-text="type === 'email' ? 'Email Confirmation' : 'Username Confirmation'"></h4>
            <label for="confirmation-code" class="block text-sm font-medium">Enter Confirmation Code:</label>
            <input id="confirmation-code" x-model="code" type="text" class="w-full px-4 py-2 mt-2 bg-gray-600 rounded-lg">
            <div class="mt-4 flex justify-end gap-2">
                <button class="bg-gray-600 px-4 py-2 rounded-lg hover:bg-gray-700" @click="showPopup = false">Cancel</button>
                <button class="bg-teal-600 px-4 py-2 rounded-lg hover:bg-teal-700" @click="alert(`${type} updated successfully!`); showPopup = false;">Confirm</button>
            </div>
        </div>
    </div>
</div>
<script>

</script>