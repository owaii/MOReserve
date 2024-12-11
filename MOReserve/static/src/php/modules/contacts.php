<div class="h-screen bg-gradient-to-b from-gray-900 to-black p-6 text-white" x-data="contactsApp()">
    <section class="max-w-4xl mx-auto">
        <div class="mb-6">
            <input type="text" placeholder="Search Contacts..." x-model="searchQuery" class="w-full px-4 py-2 rounded-lg bg-gray-800 text-gray-200 focus:outline-none focus:ring-2 focus:ring-teal-500">
        </div>

        <div class="bg-gray-800 shadow rounded-lg p-4 overflow-y-auto max-h-96">
            <template x-for="contact in filteredContacts" :key="contact.id">
                <div class="flex items-center justify-between py-2 border-b border-gray-700 cursor-pointer hover:bg-gray-700 px-4" @click="viewContact(contact)">
                    <div class="flex items-center gap-4">
                        <img :src="contact.pfp" alt="Profile" class="w-10 h-10 rounded-full">
                        <span class="text-gray-200 font-medium" x-text="contact.name"></span>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400"></i>
                </div>
            </template>
        </div>

        <div class="mt-6 bg-gray-800 p-4 rounded-lg shadow">
            <h3 class="text-lg font-medium mb-4">Add Contact</h3>
            <form @submit.prevent="addContact">
                <div class="space-y-4">
                    <div>
                        <label class="block text-gray-400">Phone Number</label>
                        <input type="text" x-model="newContact.phone" placeholder="Enter phone number" class="w-full px-4 py-2 rounded-lg bg-gray-700 text-gray-200" maxlength="9">
                    </div>
                    <button type="submit" class="w-full bg-teal-600 text-white py-2 rounded-lg hover:bg-teal-700 transition">Add Contact</button>
                </div>
            </form>
        </div>

        <div class="mt-6 bg-gray-800 p-4 rounded-lg shadow">
            <h3 class="text-lg font-medium mb-4">Make Transaction</h3>
            <button @click="openTransactionModal" class="w-full bg-teal-600 text-white py-2 rounded-lg hover:bg-teal-700 transition">Make Transaction</button>
        </div>

        <div class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center" x-show="showContactModal" @click.away="closeContactModal">
            <div class="bg-gray-800 p-6 rounded-lg shadow text-center w-96">
                <img :src="selectedContact.pfp" alt="Profile" class="w-20 h-20 mx-auto rounded-full mb-4">
                <h2 class="text-xl font-medium mb-2" x-text="selectedContact.name"></h2>
                <p class="text-gray-400 mb-4">Transactions: <span x-text="selectedContact.transactions"></span></p>
                <button @click="closeContactModal" class="px-6 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition">Close</button>
            </div>
        </div>

        <div class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center" x-show="showTransactionModal" @click.self="closeTransactionModal">
            <div class="bg-gray-800 p-6 rounded-lg shadow w-96">
                <h3 class="text-lg font-medium mb-4">Make a Transaction</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-gray-400">Phone Number</label>
                        <input maxlenght="9" type="text" x-model="transaction.phone" placeholder="Enter phone number" class="w-full px-4 py-2 rounded-lg bg-gray-700 text-gray-200">
                    </div>
                    <div>
                        <label class="block text-gray-400">Description</label>
                        <input type="text" x-model="transaction.description" placeholder="Enter description" class="w-full px-4 py-2 rounded-lg bg-gray-700 text-gray-200">
                    </div>
                    <div>
                        <label class="block text-gray-400">Amount</label>
                        <input type="number" x-model="transaction.amount" placeholder="Enter amount" class="w-full px-4 py-2 rounded-lg bg-gray-700 text-gray-200">
                    </div>
                    <button @click="confirmTransaction" class="w-full bg-teal-600 text-white py-2 rounded-lg hover:bg-teal-700 transition">Confirm</button>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
    const urlParam = new URLSearchParams(window.location.search);
    const id = urlParam.get("id");
    function contactsApp() {
        return {

            searchQuery: '',
            contacts: [],
            filteredContacts() {
                return this.contacts.filter(contact => contact.name.toLowerCase().includes(this.searchQuery.toLowerCase()));
            },
            newContact: { phone: '' },
            transaction: { phone: '', description: '', amount: '' },
            selectedContact: {},
            showContactModal: false,
            showTransactionModal: false,

            // Fetch and display contacts
            async ShowContacts() {
                try {
                    const response = await fetch(`static/src/php/ShowContacts.php?&id=${id}`);
                    if (!response.ok) throw new Error('HTTP error! Status: ' + response.status);

                    const text = await response.text();
                    console.log("Raw response text:", text);

                    const data = JSON.parse(text);
                    if (data.success === false) {
                        console.error("Error from server:", data.message || "Unknown error");
                        return;
                    }

                    this.contacts = data.data.map(contact => ({
                        name: `${contact.name} ${contact.surname}`,
                        transactions: contact.transactions,
                    }));

                    console.log("Contacts successfully updated:", this.contacts);
                } catch (error) {
                    console.error("Error fetching contacts:", error);
                }
            },

            // Add a new contact
            async addContact() {
                const phone = this.newContact.phone;
                if (!phone) {
                    alert("Please enter a phone number.");
                    return;
                }

                try {
                    const response = await fetch(`static/src/php/checkContact.php?val=${phone}&id=${id}`);
                    if (!response.ok) throw new Error('HTTP error! Status: ' + response.status);

                    const text = await response.text();
                    console.log("Raw response text:", text);

                    const data = JSON.parse(text);
                    if (data.success === false) {
                        console.error("Error from server:", data.error);
                        alert(`Error: ${data.error}`);
                        return;
                    }

                    this.contacts.push({
                        id: this.contacts.length + 1,
                        name: `${data.data["name"]} ${data.data["surname"]}`,
                        pfp: `static/img/users/pfp/${data.data["icon"] || 'astrid.webp'}`,
                        transactions: data.data["transactions"] || 0,
                    });

                    alert("Contact added successfully.");
                } catch (error) {
                    console.error("Error fetching data:", error);
                    alert("Failed to add contact. Please try again later.");
                } finally {
                    this.newContact.phone = ''; // Clear the phone input field
                }
            },

            // View contact details
            viewContact(contact) {
                this.selectedContact = contact;
                this.showContactModal = true;
            },

            // Close contact modal
            closeContactModal() {
                this.showContactModal = false;
            },

            // Open and close transaction modal
            openTransactionModal() {
                this.showTransactionModal = true;
            },
            closeTransactionModal() {
                this.showTransactionModal = false;
            },

            // Confirm transaction
            confirmTransaction() {
                alert(`Transaction confirmed for ${this.transaction.phone}`);
                
                this.transaction = { phone: '', description: '', amount: '' }; // Clear fields
                this.closeTransactionModal();
            },
        };
    }
</script>
