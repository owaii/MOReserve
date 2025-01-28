<div x-data="cardViewer()" x-init="fetchContacts()" class="flex flex-col items-center justify-center min-h-screen">
    <div class="relative">
        <!-- arrow l -->
        <button 
            @click="prevCard" 
            :disabled="isFirstCard" 
            class="absolute left-[-60px] top-1/2 transform -translate-y-1/2 p-3 text-gray-300 hover:text-white disabled:opacity-50 z-10"
        >
            <i class="fas fa-chevron-left text-2xl"></i>
        </button>

        <div>
            <!-- card -->
            <template x-if="!isLastCard">
                <div 
                    class="animate-fade-up w-96 h-64 m-auto bg-gradient-to-r from-teal-700 to-gray-800 rounded-xl relative text-white shadow-lg transition-transform transform hover:scale-105 cursor-pointer"
                    @click="openCardPopup"
                >
                    <div class="px-8 absolute top-8">
                        <div class="flex justify-between">
                            <div>
                                <p class="font-light">Name</p>
                                <p class="font-medium tracking-widest" x-text="currentCard.name"></p>
                            </div>
                            <img class="w-14 h-14" :src="currentCard.logo" />
                        </div>
                        <div class="pt-1">
                            <p class="font-light">Card Number</p>
                            <p class="font-medium tracking-more-wider" x-text="currentCard.number"></p>
                        </div>
                        <div class="pt-6 pr-6">
                            <div class="flex justify-between">
                                <div>
                                    <p class="font-light text-xs">Valid</p>
                                    <p class="font-medium tracking-wider text-sm" x-text="currentCard.status"></p>
                                </div>
                                <div>
                                    <p class="font-light text-xs">Expiry</p>
                                    <p class="font-medium tracking-wider text-sm" x-text="currentCard.expiry"></p>
                                </div>
                                <div>
                                    <p class="font-light text-xs">CVV</p>
                                    <p class="font-bold tracking-more-wider text-sm">123</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            <!-- add card -->
            <template x-if="isLastCard">
                <div 
                    class="animate-fade-up w-96 h-64 m-auto bg-gradient-to-r from-blue-600 to-blue-800 rounded-xl relative text-white shadow-lg transition-transform transform hover:scale-105 cursor-pointer flex items-center justify-center text-lg font-semibold"
                    @click="openAddCardPopup"
                >
                    + Add Card
                </div>
            </template>
        </div>

        <!-- arrow r -->
        <button 
            @click="nextCard" 
            :disabled="isLastCard" 
            class="absolute right-[-60px] top-1/2 transform -translate-y-1/2 p-3 text-gray-300 hover:text-white disabled:opacity-50 z-10"
        >
            <i class="fas fa-chevron-right text-2xl"></i>
        </button>
    </div>

    <!-- card details popup -->
    <div 
        x-show="showPopup" 
        class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50"
    >
        <div class="bg-gradient-to-r from-gray-900 to-gray-800 p-10 rounded-lg text-gray-200 w-[500px] max-w-full">
            <h2 class="text-3xl font-extrabold text-white text-center mb-6">Card Details</h2>
            <div class="flex flex-col gap-4">
                <p><strong>Name:</strong> <span x-text="currentCard.name"></span></p>
                <p><strong>Card Number:</strong> <span x-text="currentCard.number"></span></p>
                <p><strong>Valid From:</strong> <span x-text="currentCard.validFrom"></span></p>
                <p><strong>Expiry:</strong> <span x-text="currentCard.expiry"></span></p>
                <p><strong>CVV:</strong> <span>123</span></p>
            </div>
            <div class="mt-8 flex flex-col gap-4">
                <button @click="blockCard" class="bg-red-600 text-white px-4 py-3 rounded hover:bg-red-700" x-text="currentCard.status == 'active' ? 'Block Card' : 'Unblock Card'"></button>
                <button @click="openLimitsPopup" class="bg-blue-600 text-white px-4 py-3 rounded hover:bg-blue-700">Change Limits</button>
                <button @click="openExpiryPopup" class="bg-yellow-600 text-white px-4 py-3 rounded hover:bg-yellow-700">Change Expiry</button>
            </div>
            <button @click="closePopup" class="mt-6 text-center text-gray-400 hover:text-gray-200 w-full">Close</button>
        </div>
    </div>

    <!-- add card popup -->
    <div 
        x-show="showAddCardPopup" 
        class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50"
    >
        <div class="bg-gradient-to-r from-gray-800 to-gray-700 p-8 rounded-lg text-gray-200 w-[400px] max-w-full">
            <h2 class="text-2xl font-bold text-white text-center mb-6">Verify Identity</h2>
            <p class="text-gray-300 text-sm text-center mb-4">
                Please enter your password.
            </p>
            <input 
                type="password" 
                class="w-full p-3 rounded bg-gray-900 text-white mb-6 text-center"
                x-model="verificationCode"
            >
            <div class="flex justify-between">
                <button 
                    @click="closeAddCardPopup" 
                    class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700"
                >
                    Cancel
                </button>
                <button 
                    @click="completeAddCard" 
                    class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700"
                >
                    Verify
                </button>
            </div>
        </div>
    </div>

    <!-- limits popup -->
    <div 
        x-show="showLimitsPopup" 
        class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50"
    >
        <div class="bg-gray-800 p-6 rounded-lg text-gray-200 max-w-md w-full">
            <h2 class="text-2xl font-bold mb-4">Change Limits</h2>
            <div class="flex items-center gap-4">
                <input type="range" x-model="newLimit" min="0" max="10000" step="100" class="w-full">
                <input type="number" x-model="newLimit" min="0" max="10000" step="100" class="w-24 p-2 rounded bg-gray-700 text-white">
            </div>
            <div class="flex justify-between mt-4">
                <button @click="closeLimitsPopup" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">Cancel</button>
                <button @click="saveLimits" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Save</button>
            </div>
        </div>
    </div>

    <!-- expiry popup -->
    <div 
        x-show="showExpiryPopup" 
        class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50"
    >
        <div class="bg-gray-800 p-6 rounded-lg text-gray-200 max-w-md w-full">
            <h2 class="text-2xl font-bold mb-4">Change Expiry Date</h2>
            <input 
                type="text" 
                placeholder="MM/YY"
                x-model="newExpiryDate" 
                class="w-full p-2 rounded bg-gray-700 text-white mb-4"
            >
            <div class="flex justify-between">
                <button @click="closeExpiryPopup" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">Cancel</button>
                <button @click="saveExpiry" class="bg-yellow-600 text-white px-4 py-2 rounded hover:bg-yellow-700">Save</button>
            </div>
        </div>
    </div>
</div>
<script>
function cardViewer() {
    return {
        cards: [
            { name: 'Karthik P', number: '4642 3489 9867 7632', status: "active", validFrom: '11/15', expiry: '03/25', logo: 'https://i.imgur.com/bbPHJVe.png' },
            { name: 'Karthik P', number: '4642 3489 9867 7632', status: "active", validFrom: '11/15', expiry: '03/25', logo: 'https://i.imgur.com/bbPHJVe.png' }
        ],
        currentIndex: 0,
        showPopup: false,
        showAddCardPopup: false,
        verificationCode: '',
        showLimitsPopup: false,
        showExpiryPopup: false,
        confirmAddingCard: false,
        newLimit: 0,
        newExpiryDate: '',

        async fetchContacts() {
            console.log("Fetching contacts is being intitialized");
            try {
                const urlParams = new URLSearchParams(window.location.search);
                const userId = urlParams.get("id");

                console.log("for userId: " + userId)

                const response = await fetch("static/src/php/showCard.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ id: userId }),
                });

                const data = await response.json();

                console.log("success: " + data.success)

                if (data.success) {
                    this.cards = data.name.map((_, i) => ({
                        name: data.name[i],
                        number: data.number[i],
                        status: data.status[i],
                        validFrom: data.created[i],
                        expiry: data.date[i],
                        logo: 'https://i.imgur.com/bbPHJVe.png',
                    }));

                    console.log(this.cards);
                } else {
                    this.showNotification('Failed to fetch card data.', 'error');
                }
            } catch (error) {
                console.error("Error fetching contacts:", error);
            }
        },

        async NewCard(name) {
            try {
                const urlParams = new URLSearchParams(window.location.search);
                const userId = urlParams.get("id");

                const response = await fetch("static/src/php/addCard.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ id: userId, name: name }),
                });

                const data = await response.json();

                if (data.success) {
                    alert("Card added successfully");
                    await this.fetchContacts(); // Reload cards after adding
                } else {
                    alert("Failed to add contact: " + data.message);
                }
            } catch (error) {
                console.error("An error occurred while adding the contact." + error );
            }
        },

        async BlockSomeCard(number, txt) {
            try {
                const urlParams = new URLSearchParams(window.location.search);
                const userId = urlParams.get("id");

                const response = await fetch("static/src/php/blockCard.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ number: number, txt: txt}),
                });

                const data = await response.json();

                if (data.success) {
                    alert("Card blocked successfully");
                    await this.fetchContacts();
                } else {
                    alert("Failed to add contact: " + data.message);
                }
            } catch (error) {
                console.error("An error occurred while adding the contact." + error );
            }
        },

        async LimitSomeCard(number, limit) {
            try {
                const urlParams = new URLSearchParams(window.location.search);
                const userId = urlParams.get("id");

                const response = await fetch("static/src/php/limitCard.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ number: number, limit: limit}),
                });

                const data = await response.json();

                if (data.success) {
                    alert("Card limited successfully");
                } else {
                    alert("Failed to add contact: " + data.message);
                }
            } catch (error) {
                console.error("An error occurred while adding the contact." + error );
            }
        },

        async DateSomeCard(number, limit) {
            try {
                const urlParams = new URLSearchParams(window.location.search);
                const userId = urlParams.get("id");

                const response = await fetch("static/src/php/dateCard.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ number: number, date: limit}),
                });

                const data = await response.json();
            } catch (error) {
                console.error("An error occurred while adding the contact." + error );
            }
        },

        async CheckPass(pass) {
            try {
                const urlParams = new URLSearchParams(window.location.search);
                const userId = urlParams.get("id");

                const response = await fetch("static/src/php/checkPass2.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ password: pass, id: userId}),
                });

                const data = await response.json();

                console.log(data.mess);

                if(data.success) {
                    return true;
                } else {
                    return false;
                }
            } catch (error) {
                console.error("An error occurred while adding the contact." + error );
            }
        },

        get currentCard() {
            return this.cards[this.currentIndex] || { name: '', number: '', validFrom: '', expiry: '', logo: 'https://i.imgur.com/bbPHJVe.png' };
        },
        get isFirstCard() {
            return this.currentIndex === 0;
        },
        get isLastCard() {
            return this.currentIndex === this.cards.length - 1;
        },
        prevCard() {
            if (!this.isFirstCard) this.currentIndex--;
        },
        nextCard() {
            if (!this.isLastCard) this.currentIndex++;
        },
        openCardPopup() {
            this.showPopup = true;
        },
        closePopup() {
            this.showPopup = false;
        },
        blockCard() {
            if (this.cards[this.currentIndex].status == 'active')
                this.BlockSomeCard(this.cards[this.currentIndex].number, "inactive");
            else 
                this.BlockSomeCard(this.cards[this.currentIndex].number, "active");
        },
        openLimitsPopup() {
            this.showLimitsPopup = true;
        },
        closeLimitsPopup() {
            this.showLimitsPopup = false;
        },
        openAddCardPopup() {
            this.showAddCardPopup = true;
            this.verificationCode = '';
        },
        closeAddCardPopup() {
            this.showAddCardPopup = false;
        },
        saveLimits() {
            this.LimitSomeCard(this.cards[this.currentIndex].number, this.newLimit);
            alert(`Limits Changed Successfully to ${this.newLimit}`);
            this.closeLimitsPopup();
        },
        openExpiryPopup() {
            this.showExpiryPopup = true;
        },
        closeExpiryPopup() {
            this.showExpiryPopup = false;
        },
        saveExpiry() {
            if (/^\d{2}\/\d{2}$/.test(this.newExpiryDate)) {
                this.currentCard.expiry = this.newExpiryDate;
                alert(`Expiry Date Changed Successfully to ${this.newExpiryDate}`);
                this.DateSomeCard(this.cards[this.currentIndex].number, this.newExpiryDate);
                this.closeExpiryPopup();
            } else {
                alert('Invalid Expiry Date Format. Please use MM/YY.');
            }
        },
        completeAddCard() {
            if (this.CheckPass(this.verificationCode)) {
                this.confirmAddingCard = true;
                console.log(this.cards[this.currentIndex].name);
                if (confirm('Are you sure you want to add a new card?')) {
                    this.NewCard(this.cards[this.currentIndex].name);
                }
                this.closeAddCardPopup();
            } else {
                alert('Invalid verification code. Please try again.');
            }
        },

        reload() {
            this.fetchContacts(); // Call fetchContacts with the correct context
        },
    };
}
</script>
