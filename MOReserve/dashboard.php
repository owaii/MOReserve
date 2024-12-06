<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>MOReserve Dashboard</title>
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
		<link href="https://fonts.googleapis.com/css2?family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
		<link rel="stylesheet" href="static/src/css/styles.css">
		<script src="https://cdn.tailwindcss.com"></script>
		<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
		<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
	</head>
	<body class="bg-gradient-to-r from-gray-950 via-gray-900 to-gray-950 text-gray-200">
		<div class="w-[1440px] mx-auto bg-gray-800">
			<!-- sidebar -->
			<aside class="w-64 h-screen bg-gradient-to-b from-gray-800 to-gray-900 text-white fixed flex flex-col">
				<div class="p-6">
					<div class="flex items-center gap-3">
						<img src="static/img/logo.png" alt="Logo">
					</div>
					<nav class="mt-10 space-y-6">
						<a href="?page=dashboard" class="flex items-center gap-4 
												<?= (isset($_GET['page']) && $_GET['page'] == 'dashboard') || !isset($_GET['page']) ? 'text-white font-bold' : 'text-gray-300 hover:text-white' ?>">
							<i class="fas fa-th-large"></i>
							<span>Dashboard</span>
						</a>
						<a href="?page=contacts" class="flex items-center gap-4 
												<?= isset($_GET['page']) && $_GET['page'] == 'contacts' ? 'text-white font-bold' : 'text-gray-300 hover:text-white' ?>">
							<i class="fas fa-comment-dots"></i>
							<span>Contacts</span>
						</a>
						<a href="?page=mycards" class="flex items-center gap-4 
												<?= isset($_GET['page']) && $_GET['page'] == 'mycards' ? 'text-white font-bold' : 'text-gray-300 hover:text-white' ?>">
							<i class="fas fa-wallet"></i>
							<span>My Cards</span>
						</a>
						<a href="?page=activity" class="flex items-center gap-4 
												<?= isset($_GET['page']) && $_GET['page'] == 'activity' ? 'text-white font-bold' : 'text-gray-300 hover:text-white' ?>">
							<i class="fas fa-chart-bar"></i>
							<span>Activity</span>
						</a>
				</div>
				<div class="p-6 space-y-4">
					<a href="?page=settings" class="flex items-center gap-4 
												<?= isset($_GET['page']) && $_GET['page'] == 'settings' ? 'text-white font-bold' : 'text-gray-300 hover:text-white' ?>">
						<i class="fas fa-cog"></i>
						<span>Settings</span>
					</a>
					<a href="?page=gethelp" class="flex items-center gap-4 
												<?= isset($_GET['page']) && $_GET['page'] == 'gethelp' ? 'text-white font-bold' : 'text-gray-300 hover:text-white' ?>">
						<i class="fas fa-question-circle"></i>
						<span>Get Help</span>
					</a>
				</div>
				<div class="p-6 mt-auto flex items-center gap-4 mb-6">
					<img src="static/img/users/pfp/astrid.webp" alt="Profile Picture" class="w-10 h-10 rounded-full">
					<div>
						<p class="text-sm font-medium">Michael Jordan</p>
						<p class="text-xs text-gray-400">michijordan</p>
						<!-- username -->
					</div>
				</div>
			</aside>
			<!-- main -->
			<main class="ml-64 p-6 bg-gradient-to-b from-gray-900 to-black"> <?php
                $page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
                $modulePath = "static/src/php/modules/$page.php";
                if (file_exists($modulePath)) {
                    include $modulePath;
                } else {
                    echo "
                    <h1 class='text-2xl font-medium text-red-500'>Page not found</h1>";
                }
        ?> </main>
		</div>
		<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
		<script src="static/src/js/chart_main.js"></script>
		<script src="static/src/js/typewriter.js"></script>
	</body>
</html>