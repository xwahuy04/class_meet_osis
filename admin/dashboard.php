<?php
session_start();
require_once __DIR__ . '/../config.php';

// Cek apakah user sudah login
if (!isset($_SESSION['admin_id'])) {
    header("Location: " . BASE_URL . "admin/auth/login.php");
    exit();
}

// Mock data - in a real app, you would fetch this from your database
$categorySettings = [
    'cat1' => ['name' => 'Category 1', 'participantCount' => 5],
    'cat2' => ['name' => 'Category 2', 'participantCount' => 3],
    'cat3' => ['name' => 'Category 3', 'participantCount' => 4]
];

$mockRegistrations = [
    [
        'id' => 1,
        'class' => 'Class 1',
        'name' => 'John Doe',
        'status' => 'completed',
        'categories' => ['cat1', 'cat2'],
        'participants' => [
            [
                'categoryId' => 'cat1',
                'members' => [
                    ['name' => 'John Doe', 'photoUrl' => '', 'isCaptain' => true],
                    ['name' => 'Jane Smith', 'photoUrl' => '', 'isCaptain' => false]
                ]
            ],
            [
                'categoryId' => 'cat2',
                'members' => [
                    ['name' => 'John Doe', 'photoUrl' => '', 'isCaptain' => true],
                    ['name' => 'Mike Johnson', 'photoUrl' => '', 'isCaptain' => false]
                ]
            ]
        ]
    ],
    // Add more mock data as needed
];

// Initialize filter values
$filterClass = $_GET['filterClass'] ?? 'all';
$filterCategory = $_GET['filterCategory'] ?? 'all';
$activeTab = $_GET['tab'] ?? 'registered';
$editingCategory = $_GET['editingCategory'] ?? null;

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['saveParticipantCount'])) {
        $categoryId = $_POST['categoryId'];
        $count = (int)$_POST['participantCount'];
        if ($count > 0 && isset($categorySettings[$categoryId])) {
            $categorySettings[$categoryId]['participantCount'] = $count;
            // In a real app, you would save this to your database here
        }
        $editingCategory = null;
    }
}

// Generate classes list
$classes = array_merge(['all'], array_map(function($i) { return 'Class ' . $i; }, range(1, 18)));

// Prepare categories data
$categories = array_map(function($id, $settings) {
    return ['id' => $id, 'name' => $settings['name']];
}, array_keys($categorySettings), $categorySettings);

// Calculate registered classes by category
$registeredClassesByCategory = [];
foreach ($categories as $category) {
    $registeredClasses = [];
    foreach ($mockRegistrations as $reg) {
        if (in_array($category['id'], $reg['categories'])) {
            $registeredClasses[] = $reg['class'];
        }
    }
    $registeredClassesByCategory[$category['id']] = array_unique($registeredClasses);
}

// Calculate unregistered classes by category
$unregisteredClassesByCategory = [];
foreach ($categories as $category) {
    $registeredClasses = $registeredClassesByCategory[$category['id']] ?? [];
    $unregisteredClassesByCategory[$category['id']] = array_filter($classes, function($c) use ($registeredClasses) {
        return $c !== 'all' && !in_array($c, $registeredClasses);
    });
}

// Filter registrations
$filteredRegistrations = array_filter($mockRegistrations, function($reg) use ($filterClass, $filterCategory) {
    return ($filterClass === 'all' || $reg['class'] === $filterClass) &&
           ($filterCategory === 'all' || in_array($filterCategory, $reg['categories']));
});
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .bg-amber-50 { background-color: #fffbeb; }
        .bg-amber-100 { background-color: #fef3c7; }
        .bg-amber-200 { background-color: #fde68a; }
        .bg-amber-500 { background-color: #f59e0b; }
        .bg-amber-600 { background-color: #d97706; }
        .text-amber-800 { color: #92400e; }
        .border-amber-200 { border-color: #fde68a; }
    </style>
</head>
<body class="min-h-screen bg-amber-50">
    <header class="bg-amber-500 p-6 shadow-md">
        <div class="container mx-auto flex flex-col justify-between space-y-4 md:flex-row md:items-center md:space-y-0">
            <h1 class="text-3xl font-bold text-white">Admin Dashboard</h1>
            <div class="flex space-x-2">
                <!-- <a href="javascript:history.back()" class="inline-flex items-center px-4 py-2 border border-white rounded-md text-white hover:bg-amber-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back
                </a> -->
                   <a href="logout.php" class="inline-flex items-center px-4 py-2 border border-white rounded-md text-white hover:bg-amber-600">
                     Logout
                 </a>
            </div>
        </div>
    </header>

    <main class="container mx-auto py-8 px-4">
        <!-- Registration Status Overview -->
        <div class="card mb-8 border border-amber-200 bg-white shadow-lg rounded-lg">
            <div class="bg-amber-100 p-4 rounded-t-lg">
                <h2 class="text-amber-800 font-bold text-xl">Registration Status Overview</h2>
            </div>
            <div class="p-6">
                <div class="grid gap-4 md:grid-cols-3">
                    <div class="rounded-lg bg-green-100 p-4 text-center">
                        <h3 class="text-lg font-semibold text-green-800">Registered Classes</h3>
                        <p class="text-3xl font-bold text-green-600"><?= count($mockRegistrations) ?></p>
                    </div>
                    <div class="rounded-lg bg-red-100 p-4 text-center">
                        <h3 class="text-lg font-semibold text-red-800">Total Categories</h3>
                        <p class="text-3xl font-bold text-red-600"><?= count($categories) ?></p>
                    </div>
                    <div class="rounded-lg bg-blue-100 p-4 text-center">
                        <h3 class="text-lg font-semibold text-blue-800">Total Participants</h3>
                        <p class="text-3xl font-bold text-blue-600">
                            <?php
                            $total = 0;
                            foreach ($mockRegistrations as $reg) {
                                foreach ($reg['categories'] as $catId) {
                                    $total += $categorySettings[$catId]['participantCount'];
                                }
                            }
                            echo $total;
                            ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Category Settings -->
        <div class="card mb-8 border border-amber-200 bg-white shadow-lg rounded-lg">
            <div class="bg-amber-100 p-4 rounded-t-lg">
                <h2 class="text-amber-800 font-bold text-xl">Category Settings</h2>
            </div>
            <div class="p-6 overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="border-b border-amber-200">
                            <th class="py-2 px-4 text-left">Category</th>
                            <th class="py-2 px-4 text-left">Participant Count</th>
                            <th class="py-2 px-4 text-left">Registered Classes</th>
                            <th class="py-2 px-4 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $category): ?>
                        <tr class="border-b border-amber-100">
                            <td class="py-2 px-4"><?= htmlspecialchars($category['name']) ?></td>
                            <td class="py-2 px-4">
                                <?php if ($editingCategory === $category['id']): ?>
                                    <form method="POST" class="inline">
                                        <input type="hidden" name="categoryId" value="<?= $category['id'] ?>">
                                        <input type="number" name="participantCount" min="1" 
                                               value="<?= $categorySettings[$category['id']]['participantCount'] ?>"
                                               class="w-24 border border-amber-200 rounded px-2 py-1">
                                <?php else: ?>
                                    <?= $categorySettings[$category['id']]['participantCount'] ?>
                                <?php endif; ?>
                            </td>
                            <td class="py-2 px-4">
                                <?= count($registeredClassesByCategory[$category['id']] ?? []) ?> / <?= count($classes) - 1 ?>
                            </td>
                            <td class="py-2 px-4">
                                <?php if ($editingCategory === $category['id']): ?>
                                        <button type="submit" name="saveParticipantCount" 
                                                class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-sm">
                                            Save
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <a href="?editingCategory=<?= $category['id'] ?>&tab=<?= $activeTab ?>&filterClass=<?= $filterClass ?>&filterCategory=<?= $filterCategory ?>" 
                                       class="bg-amber-500 hover:bg-amber-600 text-white px-3 py-1 rounded text-sm">
                                        Edit
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tabs Navigation -->
        <div class="mb-6 bg-amber-100 rounded-lg p-1 grid grid-cols-3 gap-1 w-full">
            <a href="?tab=registered&filterClass=<?= $filterClass ?>&filterCategory=<?= $filterCategory ?><?= $editingCategory ? '&editingCategory='.$editingCategory : '' ?>" 
               class="text-center py-2 px-4 rounded <?= $activeTab === 'registered' ? 'bg-amber-500 text-white' : '' ?>">
                Registered Classes
            </a>
            <a href="?tab=unregistered&filterClass=<?= $filterClass ?>&filterCategory=<?= $filterCategory ?><?= $editingCategory ? '&editingCategory='.$editingCategory : '' ?>" 
               class="text-center py-2 px-4 rounded <?= $activeTab === 'unregistered' ? 'bg-amber-500 text-white' : '' ?>">
                Unregistered Classes
            </a>
            <a href="?tab=by-category&filterClass=<?= $filterClass ?>&filterCategory=<?= $filterCategory ?><?= $editingCategory ? '&editingCategory='.$editingCategory : '' ?>" 
               class="text-center py-2 px-4 rounded <?= $activeTab === 'by-category' ? 'bg-amber-500 text-white' : '' ?>">
                By Category
            </a>
        </div>

        <!-- Tab Contents -->
        <?php if ($activeTab === 'registered'): ?>
            <!-- Registered Classes Tab -->
            <div class="space-y-6">
                <div class="card border border-amber-200 bg-white shadow-lg rounded-lg">
                    <div class="bg-amber-100 p-4 rounded-t-lg">
                        <h2 class="text-amber-800 font-bold text-xl">Filter Registrations</h2>
                    </div>
                    <div class="p-6">
                        <form method="GET" class="grid gap-4 md:grid-cols-2">
                            <input type="hidden" name="tab" value="registered">
                            <div>
                                <label class="mb-2 block text-sm font-medium">Filter by Class</label>
                                <select name="filterClass" class="border border-amber-200 rounded w-full p-2">
                                    <?php foreach ($classes as $className): ?>
                                        <option value="<?= $className ?>" <?= $filterClass === $className ? 'selected' : '' ?>>
                                            <?= $className === 'all' ? 'All Classes' : $className ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-medium">Filter by Category</label>
                                <select name="filterCategory" class="border border-amber-200 rounded w-full p-2">
                                    <option value="all" <?= $filterCategory === 'all' ? 'selected' : '' ?>>All Categories</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?= $category['id'] ?>" <?= $filterCategory === $category['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($category['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white px-4 py-2 rounded">
                                    Apply Filters
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    <?php foreach ($filteredRegistrations as $registration): ?>
                    <div class="card border border-amber-200 bg-white shadow-lg rounded-lg">
                        <div class="bg-amber-100 p-4 rounded-t-lg">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg text-amber-800 font-bold"><?= htmlspecialchars($registration['class']) ?></h3>
                                <span class="bg-green-500 text-white text-xs px-2 py-1 rounded-full"><?= htmlspecialchars($registration['status']) ?></span>
                            </div>
                        </div>
                        <div class="p-4">
                            <div class="space-y-4">
                                <div>
                                    <p class="font-semibold">Representative: <?= htmlspecialchars($registration['name']) ?></p>
                                    <div class="mt-2">
                                        <p class="font-semibold">Registered Categories:</p>
                                        <div class="mt-1 flex flex-wrap gap-2">
                                            <?php foreach ($registration['categories'] as $catId): ?>
                                                <span class="bg-amber-500 text-white text-xs px-2 py-1 rounded-full">
                                                    <?= htmlspecialchars($categorySettings[$catId]['name']) ?>
                                                </span>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>

                                <?php if (isset($registration['participants'])): ?>
                                <div>
                                    <p class="font-semibold mb-2">Participants:</p>
                                    <?php foreach ($registration['participants'] as $group): ?>
                                        <div class="mb-3">
                                            <p class="text-sm font-medium text-amber-700 mb-1">
                                                <?= htmlspecialchars($categorySettings[$group['categoryId']]['name']) ?>:
                                            </p>
                                            <div class="grid grid-cols-2 gap-2">
                                                <?php foreach ($group['members'] as $member): ?>
                                                    <div class="flex items-center gap-2 text-sm">
                                                        <img src="<?= $member['photoUrl'] ?: '/placeholder.svg?height=40&width=40' ?>" 
                                                             alt="<?= htmlspecialchars($member['name']) ?>" 
                                                             class="h-8 w-8 rounded-full object-cover">
                                                        <span>
                                                            <?= htmlspecialchars($member['name']) ?>
                                                            <?php if ($member['isCaptain']): ?>
                                                                <span class="bg-amber-600 text-white text-xs px-1 rounded">Captain</span>
                                                            <?php endif; ?>
                                                        </span>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>

                    <?php if (empty($filteredRegistrations)): ?>
                    <div class="col-span-full rounded-lg bg-gray-100 p-8 text-center">
                        <p class="text-gray-500">No registrations found matching the selected filters.</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

        <?php elseif ($activeTab === 'unregistered'): ?>
            <!-- Unregistered Classes Tab -->
            <div class="card border border-amber-200 bg-white shadow-lg rounded-lg">
                <div class="bg-amber-100 p-4 rounded-t-lg">
                    <h2 class="text-amber-800 font-bold text-xl">Unregistered Classes</h2>
                </div>
                <div class="p-6">
                    <form method="GET" class="mb-4">
                        <input type="hidden" name="tab" value="unregistered">
                        <input type="hidden" name="filterClass" value="<?= $filterClass ?>">
                        <label class="mb-2 block text-sm font-medium">Filter by Category</label>
                        <select name="filterCategory" class="border border-amber-200 rounded w-full p-2">
                            <option value="all" <?= $filterCategory === 'all' ? 'selected' : '' ?>>All Categories</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= $category['id'] ?>" <?= $filterCategory === $category['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($category['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" class="mt-2 bg-amber-500 hover:bg-amber-600 text-white px-4 py-2 rounded">
                            Apply Filter
                        </button>
                    </form>

                    <div class="grid gap-4 md:grid-cols-3 lg:grid-cols-4">
                        <?php if ($filterCategory === 'all'): ?>
                            <!-- Show classes that haven't registered for any category -->
                            <?php foreach ($classes as $className): ?>
                                <?php if ($className !== 'all' && !in_array($className, array_column($mockRegistrations, 'class'))): ?>
                                    <div class="flex items-center justify-center rounded-lg bg-red-50 p-4 text-center">
                                        <p class="text-lg font-medium text-red-600"><?= $className ?></p>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <!-- Show classes that haven't registered for the selected category -->
                            <?php foreach ($unregisteredClassesByCategory[$filterCategory] as $className): ?>
                                <div class="flex items-center justify-center rounded-lg bg-red-50 p-4 text-center">
                                    <p class="text-lg font-medium text-red-600"><?= $className ?></p>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        <?php elseif ($activeTab === 'by-category'): ?>
            <!-- By Category Tab -->
            <div class="space-y-6">
                <?php foreach ($categories as $category): ?>
                <div class="card border border-amber-200 bg-white shadow-lg rounded-lg">
                    <div class="bg-amber-100 p-4 rounded-t-lg">
                        <h2 class="text-amber-800 font-bold text-xl">
                            <?= htmlspecialchars($category['name']) ?> (<?= $categorySettings[$category['id']]['participantCount'] ?> participants)
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="mb-4">
                            <h3 class="mb-2 text-lg font-medium">
                                Registered Classes (<?= count($registeredClassesByCategory[$category['id']] ?? []) ?>)
                            </h3>
                            <div class="grid gap-2 md:grid-cols-4 lg:grid-cols-6">
                                <?php foreach ($registeredClassesByCategory[$category['id']] ?? [] as $className): ?>
                                    <div class="flex items-center justify-center rounded-lg bg-green-50 p-3 text-center">
                                        <p class="font-medium text-green-600"><?= $className ?></p>
                                    </div>
                                <?php endforeach; ?>
                                <?php if (empty($registeredClassesByCategory[$category['id']])): ?>
                                    <p class="col-span-full text-gray-500">No classes registered yet.</p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div>
                            <h3 class="mb-2 text-lg font-medium">
                                Unregistered Classes (<?= count($unregisteredClassesByCategory[$category['id']] ?? []) ?>)
                            </h3>
                            <div class="grid gap-2 md:grid-cols-4 lg:grid-cols-6">
                                <?php foreach ($unregisteredClassesByCategory[$category['id']] as $className): ?>
                                    <div class="flex items-center justify-center rounded-lg bg-red-50 p-3 text-center">
                                        <p class="font-medium text-red-600"><?= $className ?></p>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

    <footer class="bg-amber-500 p-6 text-center text-white">
        <p>© <?= date('Y') ?> Class Meeting Registration System</p>
    </footer>
</body>
</html>