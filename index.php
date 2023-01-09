<?php
session_start();
if(!isset($_SESSION['user_id'])){
    header("Location:./login.php");
    exit;
}
require_once('DBConnection.php');
$page = isset($_GET['page']) ? $_GET['page'] : 'home';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Issue tracker</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <script src="js/jquery-3.6.0.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/script.js"></script>
    <style>
        .truncate-1 {
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
        }
        .truncate-3 {
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
        }
    </style>
</head>
<body>
    <main>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark bg-gradient">
        <div class="container">
            <a class="navbar-brand" href="#">
                Simple Issue Tracker
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($page == 'home')? 'active' : '' ?>" aria-current="page" href="./">Home</a>
                    </li>
                    <?php if($_SESSION['type'] == 1): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($page == 'department')? 'active' : '' ?>" aria-current="page" href="./?page=department">Department</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($page == 'users')? 'active' : '' ?>" aria-current="page" href="./?page=users">Users</a>
                    </li>
                    <?php endif; ?>
                    <li class="nav-item">
                    <a class="nav-link" href="./?page=issues">Issues</a>
                    </li>
                    <?php if($_SESSION['type'] != 1): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="./?page=tickets">Tickets</a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
            <div>
            <div class="dropdown">
                <button class="btn btn-secondary dropdown-toggle bg-transparent  text-light border-0" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                    Hello <?php echo $_SESSION['fullname'] ?>
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                    <li><a class="dropdown-item" href="./?page=manage_account">Manage Account</a></li>
                    <li><a class="dropdown-item" href="Actions.php?a=logout">Logout</a></li>
                </ul>
            </div>
            </div>
        </div>
    </nav>
    <div class="container py-5">
        <?php 
            if(isset($_SESSION['flashdata'])):
        ?>
        <div class="alert alert-<?php echo $_SESSION['flashdata']['type'] ?>">
            <?php echo $_SESSION['flashdata']['msg'] ?>
        </div>
        <?php unset($_SESSION['flashdata']) ?>
        <?php endif; ?>
        <?php
            include $page.'.php';
        ?>
    </div>
    </main>
    <div class="modal fade" id="uni_modal" role='dialog' data-bs-backdrop="static">
        <div class="modal-dialog modal-md modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title"></h5>
        </div>
        <div class="modal-body">
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary" id='submit' onclick="$('#uni_modal form').submit()">Save</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
        </div>
        </div>
    </div>
    <div class="modal fade" id="confirm_modal" role='dialog'>
        <div class="modal-dialog modal-md modal-dialog-centered" role="document">
        <div class="modal-content rounded-0">
            <div class="modal-header py-2">
            <h5 class="modal-title">Confirmation</h5>
        </div>
        <div class="modal-body">
            <div id="delete_content"></div>
        </div>
        <div class="modal-footer py-1">
            <button type="button" class="btn btn-primary btn-sm rounded-0" id='confirm' onclick="">Continue</button>
            <button type="button" class="btn btn-secondary btn-sm rounded-0" data-bs-dismiss="modal">Close</button>
        </div>
        </div>
        </div>
    </div>
</body>
</html>