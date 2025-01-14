<div class="h-screen bg-gradient-to-b from-gray-900 to-black p-6 text-white" x-data="contactsApp()">
    <section class="max-w-4xl mx-auto">
        <div class="mb-6">
            <input type="text" placeholder="Search Contacts..." x-model="searchQuery" 
                class="w-full px-4 py-2 rounded-lg bg-gray-800 text-gray-200 focus:outline-none focus:ring-2 focus:ring-teal-500">
        </div>

        <div class="bg-gray-800 shadow rounded-lg p-4 overflow-y-auto max-h-96">
            <template x-if="contacts.length === 0">
                <div class="text-gray-400 text-center">No contacts found.</div>
            </template>
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
                        <input type="text" x-model="newContact.phone" placeholder="Enter phone number" 
                            class="w-full px-4 py-2 rounded-lg bg-gray-700 text-gray-200">
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
                        <input type="text" x-model="transaction.phone" placeholder="Enter phone number" class="w-full px-4 py-2 rounded-lg bg-gray-700 text-gray-200">
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
    function contactsApp() {
        return {
            searchQuery: '',
            contacts: [],
            newContact: { phone: '' },
            transaction: { phone: '', description: '', amount: '' },
            selectedContact: {},
            showContactModal: false,
            showTransactionModal: false,

            get filteredContacts() {
                return this.contacts.filter(contact =>
                    contact.name.toLowerCase().includes(this.searchQuery.toLowerCase())
                );
            },

            async fetchContacts() {
                try {
                    const urlParams = new URLSearchParams(window.location.search);
                    const userId = urlParams.get("id");

                    const response = await fetch("static/src/php/showContact.php", {
                        method: "POST",
                        headers: { "Content-Type": "application/json" },
                        body: JSON.stringify({ id: userId }),
                    });

                    const data = await response.json();

                    if (data.success) {
                        this.contacts = data.id.map((_, i) => ({
                            id: i,
                            name: data.fullName[i],
                            pfp: `static/img/users/pfp/${data.icon[i]}`,
                            transactions: data.transactions[i],
                        }));
                    }
                } catch (error) {
                    alert("An error occurred while fetching contacts.");
                }
            },

            async addContact() {
                const phone = this.newContact.phone.trim();
                if (!phone) {
                    alert("Please enter a valid phone number.");
                    return;
                }

                try {
                    const urlParams = new URLSearchParams(window.location.search);
                    const userId = urlParams.get("id");

                    const response = await fetch("static/src/php/checkContact.php", {
                        method: "POST",
                        headers: { "Content-Type": "application/json" },
                        body: JSON.stringify({ id: userId, val: phone }),
                    });

                    const data = await response.json();

                    if (data.success) {
                        alert("Contact added successfully!");
                        this.contacts.push({
                            id: this.contacts.length + 1,
                            name: data.fullName,
                            pfp: `static/img/users/pfp/${data.icon || 'default.webp'}`,
                            transactions: data.transactions || 0,
                        });
                        this.newContact.phone = '';
                    } else {
                        alert("Failed to add contact: " + data.error);
                    }
                } catch (error) {
                    alert("An error occurred while adding the contact.");
                }
            },

            sendMoney(amount, phone, description) {
                const urlParams = new URLSearchParams(window.location.search);
                const userId = urlParams.get("id");
                
                if (!amount || parseFloat(amount) <= 0) {
                    alert("Please enter a valid amount.");
                    return;
                }

                fetch("static/src/php/sendMoneyPhone.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ id: userId, value: amount, phone: phone, desc: description }),
                })
                    .then((response) => response.json())
                    .then((data) => {
                        if (data.success) {
                            alert("Transaction successful!");
                            location.reload();
                        } else {
                            alert("Transaction failed: " + data.error);
                        }
                    })
                    .catch((error) => {
                        alert("An error occurred. Please try again.");
                    });
            },

            viewContact(contact) {
                this.selectedContact = contact;
                this.showContactModal = true;
            },

            closeContactModal() {
                this.showContactModal = false;
            },

            openTransactionModal() {
                this.showTransactionModal = true;
            },

            closeTransactionModal() {
                this.showTransactionModal = false;
            },

            confirmTransaction() {
                console.log(this.transaction.amount + " " + this.transaction.phone + " " + this.transaction.description);
                this.sendMoney(this.transaction.amount, this.transaction.phone, this.transaction.description);
                this.transaction = { phone: '', description: '', amount: '' };
                this.closeTransactionModal();
            },

            init() {
                this.fetchContacts();
            },
        };
    }
</script>
