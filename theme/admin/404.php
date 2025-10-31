<?php
session_start();
require_once('db.php');

// Check admin authentication
if (!isset($_SESSION['loggedin']) || $_SESSION['usertype'] !== 'admin_user') {
    header("Location: userlogin");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<?php include("head.php")?>
<body class="app sidebar-mini">
    <!-- Navbar-->
    <?php include("header.php")?>
    <!-- Sidebar menu-->
    <div class="app-sidebar__overlay" data-toggle="sidebar"></div>
    <?php include("aside.php")?>
    <main class="app-content">
      <div class="page-error tile">
        <h1 class="text-danger"><i class="bi bi-exclamation-circle"></i> Error 404: Page not found</h1>
        <p>The page you have requested is not found.</p>
        <p><a class="btn btn-primary" href="javascript:window.history.back();">Go Back</a></p>
      </div>
    </main>
    
    <!-- Essential javascripts for application to work-->
    <script src="../js/jquery-3.7.0.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>
    <script src="../js/main.js"></script>
    
    <script>
        // Initialize CKEditor for answer textarea
        ClassicEditor
            .create(document.querySelector('#answer'), {
                toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote'],
                heading: {
                    options: [
                        { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                        { model: 'heading2', view: 'h2', title: 'Heading', class: 'ck-heading_heading2' }
                    ]
                }
            })
            .catch(error => {
                console.error(error);
            });

        // Show modal if editing a FAQ
        <?php if ($currentFaq): ?>
        $(document).ready(function() {
            $('#faqModal').modal('show');
        });
        <?php endif; ?>
    </script>
</body>
</html>