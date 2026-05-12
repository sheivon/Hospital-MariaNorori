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
<script src="/assets/js/jquery-3.6.0.min.js"></script>
<script src="/assets/js/jquery.dataTables.min.js"></script>
<script src="/assets/js/bootstrap.bundle.min.js"></script>
<script src="/assets/js/sweetalert.min.js"></script>

<script src="/assets/vendor/datatables/dataTables.buttons.min.js"></script>
<script src="/assets/vendor/datatables/jszip.min.js"></script>
<script src="/assets/vendor/datatables/pdfmake.min.js"></script>
<script src="/assets/vendor/datatables/vfs_fonts.js"></script>
<script src="/assets/vendor/datatables/buttons.html5.min.js"></script>
<script src="/assets/vendor/datatables/buttons.print.min.js"></script>

<!-- ✅ Local DataTables Spanish language pack for offline dev -->
<script>
  if (window.jQuery && $.fn.dataTable) {
    $.extend(true, $.fn.dataTable.defaults, {
      language: {
        url: '/assets/vendor/datatables/Spanish.json'
      }
    });
  }
</script>

<!-- ✅ Your app scripts -->
<script src="/assets/js/i18n.js"></script>
<script src="/assets/js/app.js"></script>
<script src="/assets/js/patients.js"></script>
<script src="/assets/js/users.js"></script>
<script src="/assets/js/tests.js"></script>
<script src="/assets/js/adolescent_history.js"></script>
<script src="/assets/js/child_followups.js"></script>

</body>
</html>
