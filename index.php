<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'config.php';

// Tentang Table
$sql = "SELECT filename, filepath, file_size, upload_time FROM uploads WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $_SESSION['username']);
$stmt->execute();
$result = $stmt->get_result();

// Select Email
$sql1 = "SELECT email FROM users WHERE username = ?";
$stmt = $conn->prepare($sql1);
$stmt->bind_param("s", $_SESSION['username']);
$stmt->execute();
$result1 = $stmt->get_result();

// Fungsi untuk mengambil total ruang penyimpanan yang telah digunakan
function getTotalUsedStorage($conn, $username) {
    $sql = "SELECT SUM(file_size) AS total_size FROM uploads WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Total file_size dalam bytes
        $total_size = (int) $row['total_size'];
        return $total_size;
    } else {
        return 0; // Return 0 jika tidak ada data atau terjadi kesalahan
    }
}

// Hitung persentase penggunaan ruang penyimpanan
$used_storage = getTotalUsedStorage($conn, $_SESSION['username']);
$total_storage = 21474836480; // Misalnya, total kapasitas penyimpanan adalah 20 GB
$storage_percentage = ($used_storage / $total_storage) * 100;

$stmt->close();
$conn->close();


$trial_start_date = strtotime('2024-05-19');
$trial_days = 31;
$trial_end_date = strtotime("+$trial_days days", $trial_start_date);

// Hitung jumlah hari yang tersisa sampai akhir masa trial
$current_date = time();
$days_remaining = ceil(($trial_end_date - $current_date) / 86400);


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Files ss - BiJi Cloud</title>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>

        *:where(:not(iframe, canvas, img, svg, video):not(svg *)) {
          all: unset;
          display: revert;
        }

        /* Preferred box-sizing value */
        *,
        *::before,
        *::after {
          box-sizing: border-box;
        }

        /*
          Remove list styles (bullets/numbers)
          in case you use it with normalize.css
        */
        ol, ul {
          list-style: none;
        }

        /* For images to not be able to exceed their container */
        img {
          max-width: 100%;
        }

        /* Removes spacing between cells in tables */
        table {
          border-collapse: collapse;
        }

        /* Revert the 'white-space' property for textarea elements on Safari */
        textarea {
          white-space: revert;
        }

        /* CSS kustom untuk sidebar */
        .sidebar {
            min-width: 250px; /* Lebar minimum sidebar */
            height: 100vh; /* Tinggi sesuai jendela pandangan */
            position: fixed; /* Tetap di samping */
            top: 0; /* Posisi di atas */
            left: 0; /* Posisi di samping */
            background-color: #212529; /* Warna latar belakang sidebar */
            overflow-y: auto; /* Biarkan menggulir saat konten melebihi ukuran */
            z-index: 1030; /* Tingkatkan z-index untuk menutupi elemen lain */
        }

        .content-area {
            margin-left: 250px; /* Biarkan konten terletak di samping sidebar */
            margin-top: 56px;
            padding: 20px;
            transition: margin-left 0.3s ease; /* Efek transisi saat membesar atau menyusut */
        }

        // Source mixin
        @mixin make-container($padding-x: $container-padding-x) {
          width: 10%;
          padding-right: $padding-x;
          padding-left: $padding-x;
          margin-right: auto;
          margin-left: auto;
        }

        // Usage
        .custom-container {
          @include make-container();



        @media (max-width: 768px) {
            .sidebar {
                position: relative; /* Kembalikan ke posisi normal pada layar kecil */
                min-width: 100%; /* Sidebar memenuhi lebar layar pada layar kecil */
                height: auto; /* Tinggi sesuai konten */
                margin-bottom: 20px; /* Berikan jarak bawah */
            }

            .content-area {
                margin-left: 0; /* Konten tetap di tengah pada layar kecil */

            }
        }


        /* Tambahkan padding pada navbar untuk menghindari overlap dengan sidebar */
        .navbar .container-fluid {
            padding-left: 270px; /* Lebar sidebar + sedikit padding */
        }

        /* Hapus padding-left pada navbar saat dalam mode kecil */
        @media (max-width: 768px) {
            .navbar .container-fluid {
                padding-left: 15px; /* Padding standar */
            }
        }

    </style>
</head>
<body class="bg-dark">

   <!-- Navbar -->
    <nav class="navbar navbar-expand-lg bg-dark fixed-top bg-body-dark rounded" data-bs-theme="dark" style="margin-left: 250px;">
        <div class="container-fluid justify-content-start" style="width: 90%;">

		<!-- Search -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <form class="d-flex me-5">
		    <button class="btn bi bi-search-heart btn-outline-success me-2" style="height: ;" type="submit"></button>
                    <input class=" form-control bg-secondary" type="search" style="width: 400px; height: 50%;" placeholder="Search drive" aria-label="Search">
                </form>

		<!-- Welcome {user} -->
		<div class="text-end btn navbar-brand nav-link text-light badge text-wrap ms-5" style="font-family: sans-serif; width: 9rem;" href="#">
                        <?php echo $_SESSION['username']; ?>
			<span class="text-secondary">
			<?php while ($usermail = $result1->fetch_assoc()): ?>
				<?= $usermail['email']; ?>
			<?php endwhile; ?>
			</span>
                </div>

		<!-- Settings -->
		<button class="d-flex btn navbar-brand nav-link text-light" title="Settings" id="settingsBtn">
                                <i class="bi bi-gear"></i>
                </button>

		<!-- Log Out -->
		<a class="d-flex btn navbar-brand nav-link text-light" href="logout.php">
                           <button class="bi bi-door-open btn btn-outline-danger "> Logout</button>
		</a>
            </div>

        </div>
    </nav>

   <div class="container-fluid">
        <div class="row">
            <!-- Side Navbar -->
            <nav class="col-md-2 d-none d-md-block sidebar border-end border-secondary border-1">
                <div class="sidebar-sticky">
                    <ul class="nav nav-pills flex-column">
                        <!-- Logo -->
                        <li class="nav-item mt-3">
                            <a class="bi bi-cloud-lightning-fill nav-link text-light fw-bold font-sans text-start mb-2" href="#">
                                 BiJi Cloud
                            </a>
                        </li>


                        <!-- Upload Section -->
                        <li class="nav-item ">
                            <a class="nav-link btn text-light btn-secondary shadow-lg p-3 mb-2 rounded" type="button" id="navUpload" data-toggle="collapse" href="#uploadSection" role="button" aria-expanded="false" aria-controls="uploadSection">
                            <i class="bi bi-plus-circle mr-2"></i> Upload
                            </a>
                            <div class="collapse" id="uploadSection">
                                <ul class="nav flex-column">

                                    <!-- Upload File -->
                                    <li class="nav-item">
                                        <a class="nav-link pl-4 text-start btn text-light btn-secondary border border-bottom-0 " id="uploadBtn">
                                            <i class="bi bi-file-earmark-arrow-up"></i> Upload File
                                        </a>
                                    </li>

                                    <!-- Upload Folder -->
                                    <li class="nav-item">
                                        <a class="nav-link pl-4 text-start btn text-light btn-secondary" id="uploadFBtn">
                                            <i class="bi bi-folder-plus mr-2"></i> Upload Folder
                                        </a>
                                    </li>

                                    <!-- Create Folder -->
                                    <li class="nav-item">
                                        <a id="underdevBtnCrf" class="nav-link pl-4 mb-2 text-start btn text-light btn-secondary border border-top-0 " href="#">
                                            <i class="bi bi-folder mr-2 "></i> Create Folder
                                        </a>
                                    </li>

                                </ul>
                            </div>
                        </li>


                        <!-- My Files Dropdown -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-light bg-success active" aria-current="page" href="index.php" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-folder mr-2"></i> My Files
                            </a>
                            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <!-- List of Uploaded Directories -->
                                <a class="dropdown-item" href="#">Directory 1</a>
                                <a class="dropdown-item" href="#">Directory 2</a>
                                <!-- Add more items as needed -->
                            </div>
                        </li>
                        <!-- Computers -->
                        <li class="nav-item">
                            <a id="underdevBtnComp" class="nav-link text-light" >
                                <i class="bi bi-display  me-2"></i>  Computers
                            </a>
                        </li>
                        <!-- Photos -->
                        <li class="nav-item">
                            <a id="underdevBtnPho" class="nav-link text-light" href="#">
                                <i class="bi bi-camera me-2"></i>  Photos
                            </a>
                        </li>
                        <!-- Shared -->
                        <li class="nav-item">
                            <a id="underdevBtnShar" class="nav-link text-light" href="#">
                                <i class="bi bi-link-45deg me-2"></i>  Shared
                            </a>
                        </li>
                        <!-- Trash -->
                        <li class="nav-item">
                            <a id="underdevBtnTras" class="nav-link text-light mb-2" href="#">
                                <i class="bi bi-trash me-2"></i>  Trash
                            </a>
                        </li>
                        <!-- Progress Size -->
                        <div class="progress" role="progressbar" style="height: 5px;" aria-label="Storage Capacity" aria-valuenow="<?php echo $storage_percentage; ?>" aria-valuemin="0" aria-valuemax="100">
                                <div class="progress-bar text-dark text-bg-success " role="progressbar" style="width: <?php echo $storage_percentage; ?>%" aria-valuenow="<?php echo $storage_percentage; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>

                        <!-- Total Size -->
                        <li class="nav-item">
                            <span class="text-end text-light" style="font-weight: bold; font-family: sans-serif; font-size: 10px;">
                                <?php echo formatSizeUnits($used_storage) ?> / 20 GB
                            </span>
                        </li>

                        <!-- Get More Storage -->
                        <li class="nav-item " style="font-family: monospace; font-weight: bold;">
                            <a id="underdevBtnMore" class="nav-link text-center text-light border border-success" style="margin-top: 10px;" href="#">
                                <i class="bi bi-cloud-plus me-1"></i>  Get More Storage
                            </a>
                        </li>

			            <!-- Trial Ends Label -->
                        <li class="nav-item px-2 mt-2">
                            <span class="text-danger" style="font-size: 12px;">Trial ends in <?php echo $days_remaining; ?> days (<?php echo date('Y-m-d', $trial_end_date); ?>)</span>
                        </li>

			            <!-- Version Label -->
                        <li class="nav-item px-2 position-absolute bottom-0 start-0">
                            <span class="text-light" style="font-size: 12px;">Version 0.5
                        </li>

                    </ul>
                </div>
            </nav>
        </div>
    </div>

	<!-- Content Area -->
    <main role="main" style="margin-top: 70px; margin-left: 250px;">

		<div class="border border-start-0 rounded-end border-secondary d-flex mb-6 text-light" style="width:96%; margin-right: 40px;">
			<span class="me-auto fw-bold p-2 " >My Files</span>
			<span class="vr"></span>
			<button type="button" title="Upload File" class=" text-light bi bi-file-earmark-arrow-up p-2" href="#" id="uploadCBtn"></button>

			<button id="uploadFBtnC" title="Upload Folder" class="bi bi-folder-plus p-2" ></button>
			<span class="vr"></span>
			<button id="uploadNBtnC" class="bi bi-folder p-2" title="Create New Folders"></button>
			<span class="vr"></span>
			<button id="uploadSBtnC" class="bi bi-link-45deg p-2" title="Shared"></button>
			<button id="uploadOBtnC" class="bi bi-view-list p-2" title="Options"></button>
		</div>


			<!-- Table Files -->
                <table class="table table-dark border-secondary border-end table-hover " style="width: 95%; border-color: grey;">
                  <thead>
                    <tr>
                      <th style="padding-left: 50px; font-weight: bold; padding-right: 350px;" scope="col">Name</th>
                      <th class="fw-bold" scope="col">Modified</th>
                      <th class="fw-bold" scope="col">Size</th>
		      <th class="fw-bold" scope="col"></th>
                    </tr>
                  </thead>
                  <tbody>
		     <?php if ($result->num_rows > 0): ?>
			<?php while ($row = $result->fetch_assoc()): ?>
            			<tr>
                			<td style="padding-left: 50px;" title="Thumbnail">
					   <?php if (in_array(strtolower(pathinfo($row['filename'], PATHINFO_EXTENSION)), array('jpg', 'jpeg', 'png', 'gif'))): ?>
						<img  class="rounded me-2 img" style="width: 25px; height: 25px;" src="<?= $row['filepath'] ?>">
					   <?php else: ?>
						<i style="padding-left: 4px;" class="<?= getFileIcon($row['filename']) ?> fs-5 me-2"></i>
					   <?php endif; ?>
					<?= $row['filename'] ?>
					</td>
                			<td><?= $row['upload_time'] ?></td>
                			<td><?= formatSizeUnits($row['file_size']) ?></td>
					<td>
					<a type="button" title="Download" href="<?php echo $row['filepath']; ?>" download  class="text-light bi bi-file-earmark-arrow-down" style="width: 25px; height: 25px;"></a>
					<a type="button" href="delete.php?filepath=<?php echo urlencode($row['filepath']); ?>"  title="Delete" class="text-light bi bi-trash" style="width: 25px; height: 25px;"></a>
					</td>

            			</tr>
			<?php endwhile; ?>
		     <?php endif; ?>
                  </tbody>
                </table>


    </main>


<!-- Modal Upload File -->
<div class="modal fade" id="uploadModal" tabindex="-1" role="dialog" aria-labelledby="uploadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadModalLabel">Upload File</h5>
            </div>

            <form action="upload.php" method="post" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="form-group">
                            <input type="file" class="form-control-file" id="fileUpload" name="file" >
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="submitFile" class="btn btn-dark">Upload</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                    </div>
             </form>

        </div>
    </div>
</div>

<!-- Modal Upload Folder -->
<div class="modal fade" id="uploadFModal" tabindex="-1" role="dialog" aria-labelledby="uploadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadModalLabel">Upload Folder</h5>
            </div>

            <form action="upload.php" method="post" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="form-group">
				<input type="file" class="form-control-file" id="folderUpload" name="folder" webkitdirectory="true">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="submitFile" class="btn btn-dark">Upload</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                    </div>
             </form>

        </div>
    </div>
</div>

<!-- Modal Under Development -->
<div class="modal fade" id="underdevModal" tabindex="-1" role="dialog" aria-labelledby="underdevModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="underdevModalLabel">Under Development</h5>
            </div>

            <form action="" method="post" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="form-group">
				Sorry this feature is under development.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="submitFile" class="btn btn-dark">Oke</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                    </div>
             </form>

        </div>
    </div>
</div>

<!-- Modal Settings -->
<div class="modal fade" id="settingsModal" tabindex="-1" role="dialog" aria-labelledby="settingsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="settingsModalLabel">Settings</h5>
            </div>

            <form action="" method="post" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="form-group">
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="submitFile" class="btn btn-dark">Delete Account</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    </div>
             </form>

        </div>
    </div>
</div>



<script> // Modal Function

    // Munculkan modal saat tombol "New folder Content" ditekan
    document.getElementById("uploadOBtnC").addEventListener("click", function() {
        $('#underdevModal').modal('show');
    });

    // Munculkan modal saat tombol "New folder Content" ditekan
    document.getElementById("uploadSBtnC").addEventListener("click", function() {
        $('#underdevModal').modal('show');
    });

    // Munculkan modal saat tombol "New folder Content" ditekan
    document.getElementById("uploadNBtnC").addEventListener("click", function() {
        $('#underdevModal').modal('show');
    });

    // Munculkan modal saat tombol "Create folder" ditekan
    document.getElementById("underdevBtnCrf").addEventListener("click", function() {
        $('#underdevModal').modal('show');
    });

    // Munculkan modal saat tombol "Computers" ditekan
    document.getElementById("underdevBtnComp").addEventListener("click", function() {
        $('#underdevModal').modal('show');
    });

    // Munculkan modal saat tombol "Photos" ditekan
    document.getElementById("underdevBtnPho").addEventListener("click", function() {
        $('#underdevModal').modal('show');
    });

    // Munculkan modal saat tombol "Shared" ditekan
    document.getElementById("underdevBtnShar").addEventListener("click", function() {
        $('#underdevModal').modal('show');
    });

    // Munculkan modal saat tombol "Trash" ditekan
    document.getElementById("underdevBtnTras").addEventListener("click", function() {
        $('#underdevModal').modal('show');
    });

    // Munculkan modal saat tombol "Get More" ditekan
    document.getElementById("underdevBtnMore").addEventListener("click", function() {
        $('#underdevModal').modal('show');
    });

    // Munculkan modal saat tombol "Upload Folder Content" ditekan
    document.getElementById("uploadFBtnC").addEventListener("click", function() {
        $('#uploadFModal').modal('show');
    });

    // Munculkan modal saat tombol "Upload Folder" ditekan
    document.getElementById("uploadFBtn").addEventListener("click", function() {
        $('#uploadFModal').modal('show');
    });

	// Munculkan modal saat tombol "Upload File Content" ditekan
    document.getElementById("uploadCBtn").addEventListener("click", function() {
        $('#uploadModal').modal('show');
    });

	// Munculkan modal saat tombol "Upload File" ditekan
    document.getElementById("uploadBtn").addEventListener("click", function() {
        $('#uploadModal').modal('show');
    });

        // Munculkan modal saat tombol "settings" ditekan
    document.getElementById("settingsBtn").addEventListener("click", function() {
        $('#settingsModal').modal('show');
    });


</script>


</body>
</html>

<?php
function formatSizeUnits($bytes) {
    if ($bytes >= 1073741824) {
        $bytes = number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        $bytes = number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        $bytes = number_format($bytes / 1024, 2) . ' KB';
    } elseif ($bytes > 1) {
        $bytes = $bytes . ' bytes';
    } elseif ($bytes == 1) {
        $bytes = $bytes . ' byte';
    } else {
        $bytes = '0 bytes';
    }

    return $bytes;
}
?>

<?php
function getFileIcon($filename) {
    $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    switch ($extension) {
        case 'pdf':
            return 'bi bi-filetype-pdf';
        case 'doc':
        case 'docx':
            return 'bi bi-file-earmark-word';
        case 'xls':
        case 'xlsx':
            return 'bi bi-file-earmark-excel';
        case 'ppt':
        case 'pptx':
            return 'bi bi-filetype-ppt';
        case 'zip':
        case 'rar':
            return 'bi bi-file-zip';
        case 'txt':
            return 'bi bi-file-text';
        default:
            return 'bi bi-file-earmark';
    }
}



?>
