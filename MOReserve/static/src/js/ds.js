const user = {
    id: "",
    balance: "",
    createdAt: "",
    lastLogin: "",
    cardNumber: "",
    expDate: "",
    cardHolderName: "",
    cvv: "",
    status: "",
    username: "",
    name: "",
    surname: "",
    fullName: "",
    pesel: "",
    email: "",
    passwordHash: "",
    phoneNumber: "",
    mName: "",
    country: "",
    city: "",
    street: "",
    buildingNumber: "",
    apNumber: "",
    postal: "",
};

const Init = async () => {
    try {
        const urlParam = new URLSearchParams(window.location.search);
        const id = urlParam.get("id");

        if (!id) {
            console.error("User ID is missing in the URL.");
            return;
        }

        console.log("User ID: " + id);

        const response = await fetch(`static/src/php/init.php?id=${id}`);

        if (!response.ok) {
            throw new Error('HTTP error! Status: ' + response.status);
        }

        const data = await response.json();

        if (data.success === false) {
            console.error("Error from server:", data.error);
            return;
        }

        console.log("Fetched Data:", data);

        Object.keys(user).forEach(key => {
            if (data.data[key] !== undefined) {
                user[key] = data.data[key];
            }
        });

        user.fullName = user.name + " " + user.surname;

        let oldNum = String(user.cardNumber);
        let newNum = "";

        for (let i = 0; i < oldNum.length; i++) {
            if (i % 4 == 0) {
                newNum += " ";
            } newNum += oldNum[i];
        }

        user.cardNumber = newNum;

        console.log("Updated User Object:", JSON.stringify(user, null, 2));

        updateUI();
    } catch (error) {
        console.error("Error fetching data:", error);
    }
};

function updateUI() {
    Object.keys(user).forEach(key => {
        const element = document.getElementById(key);
        if (element) {
            element.textContent = user[key] || "N/A";
        }
    });
}

// Initialize the profile page once the DOM is loaded
document.addEventListener("DOMContentLoaded", () => {
    Init();
});
