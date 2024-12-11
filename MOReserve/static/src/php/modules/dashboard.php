<?php 
	$db = new mysqli('localhost', 'root', '', 'more');

	// Check if the connection was successful
	if ($db->connect_error) {
	    die("Connection failed: " . $db->connect_error);
	}

	$id = $_GET['id'];
?>
<div class="grid grid-cols-3 gap-6">
	<!-- card -->
	<section class="col-span-1">
		<div class="animate-fade-up w-92 h-56 m-auto bg-gradient-to-r from-teal-700 from-10% to-gray-800 to-90% rounded-xl relative text-white shadow-lg transition-transform transform hover:scale-105">
			<div class="w-full px-8 absolute top-8">
				<div class="flex justify-between">
					<div>
						<p class="font-light">Name</p>
						<p class="font-medium tracking-widest" id="cardHolderName">Karthik P</p>
					</div>
					<img class="w-14 h-14" src="https://i.imgur.com/bbPHJVe.png" />
				</div>
				<div class="pt-1">
					<p class="font-light">Card Number</p>
					<p class="font-medium tracking-more-wider" id="cardNumber">4642 3489 9867 7632</p>
				</div>
				<div class="pt-6 pr-6">
					<div class="flex justify-between">
						<div>
							<p class="font-light text-xs">Valid</p>
							<p class="font-medium tracking-wider text-sm" id="status">11/15</p>
						</div>
						<div>
							<p class="font-light text-xs">Expiry</p>
							<p class="font-medium tracking-wider text-sm" id="date">03/25</p>
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
					<input type="text" placeholder="Enter the amount" value="$800.00" class="w-full px-4 py-2 focus:border-none rounded-lg bg-gray-600 text-gray-200">
				</div>
				<div class="relative">
					<div @click="showContacts = !showContacts" class="flex items-center gap-4 cursor-pointer">
						<img src="static/img/users/pfp/astrid.webp" alt="Profile" class="w-10 h-10 rounded-full">
						<span>Astrid Hayes</span>
						<i class="fas fa-chevron-down"></i>
					</div>
					<ul x-show="showContacts" class="absolute bg-gray-700 rounded-lg mt-2 w-full z-10" @click.away="showContacts = false">
						<?php 
							$stmt = $db->prepare("
				                SELECT u.name as name, u.surname as surname, u.icon as icon FROM users u JOIN friends ON u.id=friends.friendID
				                WHERE friends.userID = ?
				            ");
				            $stmt->bind_param("i", $id);
				            $stmt->execute();
				            $result = $stmt->get_result();

				            if ($result->num_rows > 0) {
				            	while ($row = $result->fetch_assoc()) {
				            		echo '
				            		<li class="px-4 py-2 hover:bg-gray-600 cursor-pointer flex items-center gap-4">
										<img src="static/img/users/pfp/'.$row["icon"].'" alt="Astrid Hayes" class="w-8 h-8 rounded-full"> '.$row["name"] .' '. $row["surname"].'
									</li>
				            		';

				            	}
				            }
						?>
					</ul>
				</div>
				<button class="w-full text-white py-2 rounded-lg mt-4 bg-teal-600 transition duration-300 hover:bg-teal-700">Send Money</button>
			</div>
		</div>
		<div class="bg-gray-800 shadow p-6 rounded-lg mt-6 flex justify-center">
			<h4 class="text-6xl font-medium text-white">
				<span class="typewriter-text bg-gradient-to-br from-teal-300 to-cyan-600 bg-clip-text text-transparent font-semibold" id="balance" delay="150">
					<!--10,532$-->
				</span>
			</h4>
		</div>
	</section>
	<section class="col-span-2 bg-gray-800 shadow rounded-lg p-6" x-data="moneyFlow()">
		<h4 class="text-lg font-medium text-gray-200">Money Flow</h4>
		<div class="mt-6 flex justify-between items-center">
			<div class="flex items-center gap-4">
				<span class="text-sm font-medium text-gray-400">Savings</span>
				<span class="text-lg font-semibold text-green-500">+6.79%</span>
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
	            ");
	            $stmt->bind_param("ii", $id, $id);
	            $stmt->execute();
	            $result = $stmt->get_result();

	            if ($result->num_rows > 0) {
	                while ($row = $result->fetch_assoc()) {
	                    $isIncoming = ($row['toUserID'] == $id); // Check if the transaction is incoming
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

	            // Close the statement and database connection
	            $stmt->close();
	            $db->close();
	        ?>
			</tbody>
		</table>
	</section>
</div>