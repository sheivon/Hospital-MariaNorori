<?php // expose current user to JS for chat
if (!empty($_SESSION['user'])): ?>
<script>
  window.CURRENT_USER = {
    id: <?= (int)$_SESSION['user']['id'] ?>,
    username: <?= json_encode($_SESSION['user']['username']) ?>
  };
</script>
<?php endif; ?></main></div>

<!-- ✅ Load order matters -->
<script src="../assets/js/jquery-3.6.0.min.js"></script>
<script src="../assets/js/jquery.dataTables.min.js"></script>
<script src="../assets/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/sweetalert.min.js"></script>

<?php
// Load local DataTables Buttons and export libs if present (copied by build.ps1)
$vendorBase = $_SERVER['DOCUMENT_ROOT'] . '/assets/vendor/datatables';
if (file_exists($vendorBase . '/dataTables.buttons.min.js')) {
  echo "<script src=\"/assets/vendor/datatables/dataTables.buttons.min.js\"></script>\n";
}
if (file_exists($vendorBase . '/buttons.html5.min.js')) {
  echo "<script src=\"/assets/vendor/datatables/buttons.html5.min.js\"></script>\n";
}
if (file_exists($vendorBase . '/buttons.print.min.js')) {
  echo "<script src=\"/assets/vendor/datatables/buttons.print.min.js\"></script>\n";
}
if (file_exists($vendorBase . '/jszip.min.js')) {
  echo "<script src=\"/assets/vendor/datatables/jszip.min.js\"></script>\n";
}
if (file_exists($vendorBase . '/pdfmake.min.js')) {
  echo "<script src=\"/assets/vendor/datatables/pdfmake.min.js\"></script>\n";
}
if (file_exists($vendorBase . '/vfs_fonts.js')) {
  echo "<script src=\"/assets/vendor/datatables/vfs_fonts.js\"></script>\n";
}
?>

<!-- ✅ Your app scripts -->
<script src="/assets/js/i18n.js"></script>
<script src="/assets/js/app.js"></script>
<script src="/assets/js/patients.js"></script>
<script src="/assets/js/users.js"></script>
<script src="/assets/js/adolescent_history.js"></script>

</body>
</html>
