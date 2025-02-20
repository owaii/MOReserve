<?php 
	include("static/src/php/conn.php");

	$id = $_GET['id'];
	$intNumber = null;
	$date = "";
	$holderName = "";
	$status = "";
	$newNumber = "";
	$balance = null;

	$CardStmt = $db->prepare("
		SELECT
			number,
			date,
			holderName,
			status
		FROM cards 
		WHERE userID = ?
		LIMIT 1
	");
	$CardStmt->bind_param("i",$id);

	if (!$CardStmt->execute()) {
	    die('Query failed: ' . $CardStmt->error);
	}

	$CardStmt->bind_result($intNumber, $date, $holderName, $status);

	if ($CardStmt->fetch()) {
		$number = (string)$intNumber;

		for ($i = 0; $i < strlen($number); $i++) {
			if ($i % 4 == 0) {
				$newNumber .= " ";
			} $newNumber .= $number[$i];
		}
	} else {
		$newNumber = "Unknown number";
	}

	$CardStmt->close();

	$balanceStmt = $db->prepare("
		SELECT 
			balance
		FROM users
		WHERE id = ?
	");
	$balanceStmt->bind_param("i",$id);

	if (!$balanceStmt->execute()) {
	    die('Query failed: ' . $balanceStmt->error);
	}

	$balanceStmt->bind_result($balance);

	$balanceStmt->fetch();

	$balanceStmt->close();

	$stmt = $db->prepare("
		SELECT 
			SUM(amount) AS avg_amount
		FROM 
			transactions
		WHERE 
			YEARWEEK(created, 1) = YEARWEEK(CURDATE(), 1)
			AND userID = ?
	");

	$stmt->bind_param("i", $id);
	$stmt->execute();

	$result = $stmt->get_result();
	$row = $result->fetch_assoc();
	$thisweekAvg = $row['avg_amount'] ?? 0;

	$stmt->close();

	$stmt1 = $db->prepare("
		SELECT 
			SUM(amount) AS avg_amount
		FROM 
			transactions
		WHERE 
			YEARWEEK(created, 1) = YEARWEEK(CURDATE(), 1) - 1
			AND userID = ?
	");

	$stmt1->bind_param("i", $id);
	$stmt1->execute();

	$result1 = $stmt1->get_result();
	$row1 = $result1->fetch_assoc();
	$lastweekAvg = $row1['avg_amount'] ?? 0;

	$stmt1->close();

	if ($lastweekAvg != 0) {
		$avg = (($lastweekAvg - $thisweekAvg) / $lastweekAvg) * 100;
		$avg = round($avg, 2) . "%";
	} else {
		$avg = "0%";
	}
?>	
<div class="grid grid-cols-3 gap-6">
	<!-- card -->
	<section class="col-span-1">
		<div class="animate-fade-up w-92 h-56 m-auto bg-gradient-to-r from-teal-700 from-10% to-gray-800 to-90% rounded-xl relative text-white shadow-lg transition-transform transform hover:scale-105">
			<div class="w-full px-8 absolute top-8">
				<div class="flex justify-between">
					<div>
						<p class="font-light">Name</p>
						<p class="font-medium tracking-widest"><?php echo htmlspecialchars($holderName);?></p>
					</div>
					<img class="w-14 h-14" src="https://i.imgur.com/bbPHJVe.png" />
				</div>
				<div class="pt-1">
					<p class="font-light">Card Number</p>
					<p class="font-medium tracking-more-wider"><?php echo htmlspecialchars($newNumber); ?></p>
				</div>
				<div class="pt-6 pr-6">
					<div class="flex justify-between">
						<div>
							<p class="font-light text-xs">Valid</p>
							<p class="font-medium tracking-wider text-sm" id="status"><?php echo $status;?></p>
						</div>
						<div>
							<p class="font-light text-xs">Expiry</p>
							<p class="font-medium tracking-wider text-sm" id="date"><?php echo htmlspecialchars($date); ?></p>
						</div>
						<div>
							<p class="font-light text-xs">CVV</p>
							<p class="font-bold tracking-more-wider text-sm">···</p>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="bg-gray-800 shadow p-6 rounded-lg mt-6 text-white" x-data="{ showContacts: false }">
			<h4 class="text-xl font-medium">Send Money</h4>
			<div class="mt-6 space-y-4">
				<div>
					<input id="money" type="text" placeholder="Enter the amount" class="w-full px-4 py-2 focus:border-none rounded-lg bg-gray-600 text-gray-200">
				</div>
				<div id="profileDropdown" class="relative">
					<div onclick="toggleContacts()" class="flex items-center gap-4 cursor-pointer">
						<img id="profilePic" src="static/img/users/pfp/astrid.webp" alt="Profile" class="w-10 h-10 rounded-full">
						<span id="profileName">Select user</span>
						<i class="fas fa-chevron-down"></i>
					</div>

					<ul id="contactsList" class="absolute bg-gray-700 rounded-lg mt-2 w-full z-10 hidden">
						<?php 
							$stmt = $db->prepare("
								SELECT u.id as id, u.name as name, u.surname as surname, u.icon as icon 
								FROM users u 
								JOIN friends ON u.id = friends.friendID
								WHERE friends.userID = ?
							");
							$stmt->bind_param("i", $id);
							$stmt->execute();
							$result = $stmt->get_result();

							$defaultContact = null; 
							if ($result->num_rows > 0) {
								$contacts = [];
								$isFirst = true; 

								while ($row = $result->fetch_assoc()) {
									if ($isFirst) {
										$defaultContact = $row;
										$isFirst = false;
									}
									echo '
									<li class="px-4 py-2 hover:bg-gray-600 cursor-pointer flex items-center gap-4" 
										onclick="setProfile(\'' . $row["name"] . '\', \'' . $row["surname"] . '\', \'' . $row["icon"] . '\', \'' . $row["id"] . '\')">
										<img src="static/img/users/pfp/' . $row["icon"] . '" alt="' . $row["name"] . ' ' . $row["surname"] . '" class="w-8 h-8 rounded-full">
										' . $row["name"] . ' ' . $row["surname"] . '
									</li>
									';
								}
							}
						?>
					</ul>
				</div>
				<input type="hidden" id="selectedUserId" value="0">
				<button onclick="sendMoney(document.getElementById('money').value, document.getElementById('selectedUserId').value)" 
						class="w-full text-white py-2 rounded-lg mt-4 bg-teal-600 transition duration-300 hover:bg-teal-700" 
						disabled>Send Money</button>
			</div>
		</div>
		<div class="bg-gray-800 shadow p-6 rounded-lg mt-6 flex justify-center">
			<h4 class="text-6xl font-medium text-white">
				<span class="typewriter-text bg-gradient-to-br from-teal-300 to-cyan-600 bg-clip-text text-transparent font-semibold" id="balance" delay="150" text="<?php echo htmlspecialchars("$" . (string)$balance); ?>">
					<!-- 10,532$ -->
				</span>
			</h4>
		</div>
	</section>
	<section class="col-span-2 bg-gray-800 shadow rounded-lg p-6" x-data="moneyFlow()">
		<h4 class="text-lg font-medium text-gray-200">Money Flow</h4>
		<div class="mt-6 flex justify-between items-center">
			<div class="flex items-center gap-4">
				<span class="text-sm font-medium text-gray-400">Savings</span>
				<span class="text-lg font-semibold text-green-500"><?php echo htmlspecialchars($avg); ?></span>
			</div>
			<div class="relative">
				<div @click="toggleView" class="flex items-center gap-4 cursor-pointer text-gray-400">
					<span x-text="view"></span>
					<i class="fas fa-chevron-down"></i>
				</div>
			</div>
		</div>
		<div class="mt-6">
			<canvas id="moneyFlowChart" class="w-full rounded-lg"></canvas>
		</div>
	</section>
	<section class="col-span-3 bg-gradient-to-b from-gray-800 to-gray-900 shadow rounded-lg p-6">
		<h4 class="text-lg font-medium text-gray-200">Recent Transactions</h4>
		<table class="mt-6 w-full text-gray-200">
			<thead class="text-gray-400">
				<tr>
					<th class="text-left">Date</th>
					<th class="text-left">Description</th>
					<th class="text-right">Amount</th>
				</tr>
			</thead>
			<tbody>
			<?php
	            $stmt = $db->prepare("
	                SELECT amount, description, created, toUserID 
	                FROM transactions 
	                WHERE userID = ? OR toUserID = ? 
					ORDER BY created DESC, time DESC
	                LIMIT 5
	            ");
	            $stmt->bind_param("ii", $id, $id);
	            $stmt->execute();
	            $result = $stmt->get_result();

	            if ($result->num_rows > 0) {
	                while ($row = $result->fetch_assoc()) {
	                    $isIncoming = ($row['toUserID'] == $id);
	                    $amountClass = $isIncoming ? 'text-green-500' : 'text-red-500';
	                    $amountPrefix = $isIncoming ? '+' : '-';

	                    echo '
	                    <tr class="border-b border-gray-700">
	                        <td class="py-2">'.htmlspecialchars($row["created"]).'</td>
	                        <td>'.htmlspecialchars($row["description"]).'</td>
	                        <td class="text-right '.$amountClass.'">'.
	                            $amountPrefix.' '.htmlspecialchars($row["amount"]).'
	                        </td>
	                    </tr>
	                    ';
	                }
	            } else {
	                echo '
	                <tr>
	                    <td colspan="3" class="py-4 text-center text-gray-400">No transactions found.</td>
	                </tr>
	                ';
	            }

	            $stmt->close();
	            $db->close();
	        ?>
			</tbody>
		</table>
	</section>
</div>
<script>
function toggleContacts() {
    const contactsList = document.getElementById("contactsList");
    contactsList.classList.toggle("hidden");
    const expanded = contactsList.classList.contains("hidden") ? "false" : "true";
    contactsList.setAttribute("aria-expanded", expanded);
}

function setProfile(name, surname, icon, userId) {
    document.getElementById("profileName").textContent = `${name} ${surname}`;
    document.getElementById("profilePic").src = `static/img/users/pfp/${icon}`;
    document.getElementById("selectedUserId").value = userId;
    toggleContacts();
    enableSendButton();
}

function enableSendButton() {
    const moneyInput = document.getElementById("money");
    const sendButton = document.querySelector("button");
    const selectedUserId = document.getElementById("selectedUserId").value;

    if (selectedUserId !== "0" && moneyInput.value && parseFloat(moneyInput.value) > 0) {
        sendButton.disabled = false;
    } else {
        sendButton.disabled = true;
    }
}

function sendMoney(amount, userId) {
    const urlParam = new URLSearchParams(window.location.search);
    const id = urlParam.get("id");

    if (userId === "0") {
        alert("Please select a user first.");
        return;
    }

    if (!amount || parseFloat(amount) <= 0) {
        alert("Please enter a valid amount.");
        return;
    }

    fetch("static/src/php/sendMoney.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
        },
        body: JSON.stringify({ id: id, value: amount, userID: userId }),
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
        console.error("Error:", error);
        alert("An error occurred. Please try again.");
    });
}

document.getElementById("money").addEventListener("input", function() {
    enableSendButton();
});

document.querySelector("button").disabled = true;
</script>