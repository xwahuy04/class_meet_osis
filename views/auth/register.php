<?php
// Validasi form dan logika submit akan ditambahkan di sini
$classes = [
    'X IPA 1',
    'X IPA 2',
    'X IPA 3',
    'X IPS 1',
    'X IPS 2',
    'XI IPA 1',
    'XI IPA 2',
    'XI IPA 3',
    'XI IPS 1',
    'XI IPS 2',
    'XII IPA 1',
    'XII IPA 2',
    'XII IPA 3',
    'XII IPS 1',
    'XII IPS 2'
];

// Data kategori (contoh)
$categorySettings = [
    'sports' => ['name' => 'Sports', 'participantCount' => 5],
    'arts' => ['name' => 'Arts', 'participantCount' => 3],
    'science' => ['name' => 'Science', 'participantCount' => 2],
    'music' => ['name' => 'Music', 'participantCount' => 4]
];

// Inisialisasi variabel
$selectedCategories = $_POST['categories'] ?? [];
$formData = $_POST['formData'] ?? [];
$photoPreviews = $_FILES['photos'] ?? []; range(0, 17);

// Data kategori (contoh)
$categorySettings = [
    'sports' => ['name' => 'Sports', 'participantCount' => 5],
    'arts' => ['name' => 'Arts', 'participantCount' => 3],
    'science' => ['name' => 'Science', 'participantCount' => 2],
    'music' => ['name' => 'Music', 'participantCount' => 4]
];
$categories = array_map(function($id, $data) {
    return ['id' => $id, 'name' => $data['name']];
}, array_keys($categorySettings), $categorySettings);

// Inisialisasi variabel
$selectedCategories = $_POST['categories'] ?? [];
$formData = $_POST['formData'] ?? [];
$photoPreviews = $_FILES['photos'] ?? [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register for Class Meeting</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/css/bootstrap.min.css">
    <!-- Font Awesome untuk ikon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        body {
            background-color: #fffbeb;
            min-height: 100vh;
        }
        .card-header-custom {
            background-color: #f59e0b;
            color: white;
        }
        .btn-amber {
            background-color: #f59e0b;
            color: white;
        }
        .btn-amber:hover {
            background-color: #d97706;
            color: white;
        }
        .border-amber {
            border-color: #fde68a;
        }
        .badge-amber {
            background-color: #f59e0b;
        }
        .text-amber-800 {
            color: #92400e;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="card mx-auto" style="max-width: 800px;">
            <div class="card-header card-header-custom">
                <h2 class="mb-0">Daftar ClassMeeting</h2>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <a href="../../index.php" class="btn btn-outline-amber border-amber text-amber-800">
                        <i class="fas fa-chevron-left mr-2"></i>Kembali ke Beranda
                    </a>
                </div>

                <form method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                    <!-- Representative Name -->
                    <div class="form-group">
                        <label for="name">Nama Ketua Kelas</label>
                        <input type="text" class="form-control border-amber" id="name" name="formData[name]" 
                               value="<?= htmlspecialchars($formData['name'] ?? '') ?>" required>
                        <div class="invalid-feedback">Nama minimal harus 5 karakter</div>
                    </div>

                    <!-- Class Selection -->
                    <div class="form-group">
                        <label for="class">Class</label>
                        <select class="form-control border-amber" id="class" name="formData[class]" required>
                            <option value="">Select your class</option>
                            <?php foreach ($classes as $class): ?>
                                <option value="<?= htmlspecialchars($class) ?>" 
                                    <?= isset($formData['class']) && $formData['class'] === $class ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($class) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">Please select your class.</div>
                    </div>

                    <!-- Event Categories -->
                    <div class="form-group">
                        <label>Event Categories</label>
                        <div class="row">
                            <?php foreach ($categories as $category): ?>
                                <div class="col-md-6 mb-2">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input category-checkbox" 
                                               id="category-<?= $category['id'] ?>" 
                                               name="categories[]" 
                                               value="<?= $category['id'] ?>"
                                               <?= in_array($category['id'], $selectedCategories) ? 'checked' : '' ?>>
                                        <label class="custom-control-label" for="category-<?= $category['id'] ?>">
                                            <?= htmlspecialchars($category['name']) ?> 
                                            (<?= $categorySettings[$category['id']]['participantCount'] ?> participants)
                                        </label>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="invalid-feedback d-block">Please select at least one category.</div>
                    </div>

                    <!-- Participant Details -->
                    <?php if (!empty($selectedCategories)): ?>
                        <div class="mt-4">
                            <h4 class="text-amber-800">Enter Participant Details</h4>
                            
                            <?php foreach ($selectedCategories as $categoryId): 
                                $category = $categorySettings[$categoryId];
                                $participantCount = $category['participantCount'];
                            ?>
                                <div class="card border-amber mb-4">
                                    <div class="card-body">
                                        <h5 class="card-title"><?= htmlspecialchars($category['name']) ?></h5>
                                        <p class="card-text text-muted">
                                            Please enter details for <?= $participantCount ?> participant<?= $participantCount > 1 ? 's' : '' ?>
                                        </p>

                                        <?php for ($i = 0; $i < $participantCount; $i++): ?>
                                            <div class="border border-amber rounded p-3 mb-3">
                                                <div class="d-flex align-items-center mb-2">
                                                    <?php if ($i === 0): ?>
                                                        <span class="badge badge-amber mr-2">Captain</span>
                                                    <?php endif; ?>
                                                    <h6 class="mb-0">Participant <?= $i + 1 ?></h6>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="participant-<?= $categoryId ?>-<?= $i ?>-name">Name</label>
                                                            <input type="text" class="form-control" 
                                                                   id="participant-<?= $categoryId ?>-<?= $i ?>-name" 
                                                                   name="participants[<?= $categoryId ?>][<?= $i ?>][name]" 
                                                                   value="<?= htmlspecialchars($formData['participants'][$categoryId][$i]['name'] ?? '') ?>" required>
                                                            <div class="invalid-feedback">Name must be at least 2 characters.</div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="participant-<?= $categoryId ?>-<?= $i ?>-photo">Photo</label>
                                                            <input type="file" class="form-control-file" 
                                                                   id="participant-<?= $categoryId ?>-<?= $i ?>-photo" 
                                                                   name="photos[<?= $categoryId ?>][<?= $i ?>]" accept="image/*">
                                                            <div class="invalid-feedback">Please upload a photo for this participant.</div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <?php if (!empty($photoPreviews[$categoryId][$i]['tmp_name'])): ?>
                                                    <div class="mt-2">
                                                        <img src="<?= $photoPreviews[$categoryId][$i]['tmp_name'] ?>" 
                                                             alt="Preview" class="img-thumbnail" style="width: 80px; height: 80px;">
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <button type="submit" class="btn btn-amber btn-block">Submit Registration</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/js/bootstrap.min.js"></script>
    
    <!-- Custom JavaScript -->
    <script>
        $(document).ready(function() {
            // Validasi form
            (function() {
                'use strict';
                window.addEventListener('load', function() {
                    var forms = document.getElementsByClassName('needs-validation');
                    var validation = Array.prototype.filter.call(forms, function(form) {
                        form.addEventListener('submit', function(event) {
                            if (form.checkValidity() === false) {
                                event.preventDefault();
                                event.stopPropagation();
                            }
                            form.classList.add('was-validated');
                        }, false);
                    });
                }, false);
            })();

            // Handle category selection
            $('.category-checkbox').change(function() {
                if ($('.category-checkbox:checked').length > 0) {
                    $('form').attr('action', '?categories_selected=true');
                    $('form').submit();
                }
            });
        });
    </script>
</body>
</html>