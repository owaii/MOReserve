<div class="grid grid-cols-3 gap-6 h-full">
    <!-- Add Contact & Most Popular Contact -->
    <section class="col-span-1 flex flex-col gap-6">
        <!-- Add Contact -->
        <div class="bg-gray-800 p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-medium mb-4">Add Contact</h2>
            <div class="flex gap-4">
                <input type="text" placeholder="Phone Number" class="w-full px-4 py-2 rounded-lg bg-gray-700 text-gray-200 focus:outline-none">
                <button class="px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700">Add</button>
            </div>
        </div>

        <!-- Most Popular Contact -->
        <div class="bg-gray-800 p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-medium mb-4">Most Popular Contact</h2>
            <div class="flex items-center gap-4">
                <img src="static/img/users/pfp/astrid.webp" alt="Popular Contact" class="w-16 h-16 rounded-full">
                <div>
                    <p class="text-lg font-medium">Astrid Hayes</p>
                    <p class="text-sm text-gray-400">+123456789</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Contacts List -->
    <section class="col-span-2 bg-gray-800 p-6 rounded-lg shadow-md flex flex-col gap-4">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-medium">Your Contacts</h2>
            <input type="text" placeholder="Search Contacts" class="w-1/2 px-4 py-2 rounded-lg bg-gray-700 text-gray-200 focus:outline-none">
        </div>
        <div class="h-[400px] overflow-y-auto border-t border-gray-600 pt-4">
            <ul class="space-y-4">
                <!-- Example Contact Item -->
                <li @click="showPopup = true; name = 'Astrid Hayes'; phone = '+123456789'; transfers = 5" class="flex items-center gap-4 p-4 bg-gray-700 rounded-lg shadow-md cursor-pointer">
                    <img src="static/img/users/pfp/astrid.webp" alt="Contact" class="w-12 h-12 rounded-full">
                    <div>
                        <p class="text-lg font-medium">Astrid Hayes</p>
                        <p class="text-sm text-gray-400">+123456789</p>
                    </div>
                </li>
            </ul>
        </div>
    </section>

    <!-- Contact Details Popup -->
    <div x-data="{ showPopup: false, name: '', phone: '', transfers: 0 }" x-show="showPopup" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50">
        <div class="bg-gray-800 p-6 rounded-lg shadow-lg w-96 text-center relative">
            <button class="absolute top-4 right-4 text-gray-400" @click="showPopup = false">&times;</button>
            <img :src="'static/img/users/pfp/' + name.toLowerCase().replace(' ', '') + 'astrid.webp'" alt="Profile Picture" class="w-24 h-24 rounded-full mx-auto mb-4">
            <h3 class="text-xl font-semibold" x-text="name"></h3>
            <p class="text-sm text-gray-400" x-text="phone"></p>
            <p class="text-lg mt-4">Transfers: <span class="font-bold" x-text="transfers"></span></p>
        </div>
    </div>
</div>
