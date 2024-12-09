var user = {
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
}

async function Init() {
    try {
        const urlParam = new URLSearchParams(window.location.search);
        const id = urlParam.get("id");

        console.log("User id: " + id);

        const response = await fetch('static/src/php/init.php?id='+id);

        if (!response.ok) {
            throw new Error('HTTP error! Status: ' + response.status);
        }

        const data = await response.json();

        if (data.error) {
            console.error("Error from server:", data.error);
            return;
        }

        console.log("Fetched Data:", data);

        Object.keys(user).forEach(key => {
            if (data.hasOwnProperty(key)) {
                user[key] = data[key];
            }
        });
        
    } catch (error) {
        console.error("Error fetching data:", error);
    }
}

function printUser() {
    console.log("User Object:", JSON.stringify(user, null, 2));
}

console.log("ds.js is on the way");

Init().then(() => printUser());