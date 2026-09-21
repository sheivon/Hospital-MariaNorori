<?php // expose current user to JS for chat
if (!empty($_SESSION['user'])): ?>
<script>
  window.CURRENT_USER = {
    id: <?= (int)$_SESSION['user']['id'] ?>,
    username: <?= json_encode($_SESSION['user']['username']) ?>
  };
</script>
<?php endif; ?></main></div>

<?php
$pageScripts = $GLOBALS['PAGE_SCRIPTS'] ?? [];
$needsDataTables = in_array('datatables', $pageScripts, true);
?>

<!-- Common libraries -->
<script src="/assets/js/jquery-3.6.0.min.js"></script>
<script src="/assets/js/bootstrap.bundle.min.js" defer></script>
<script src="/assets/js/sweetalert.min.js" defer></script>

<!-- DataTables + export buttons (loaded on demand) -->
<?php if ($needsDataTables): ?>
<script src="/assets/js/jquery.dataTables.min.js" defer></script>
<script src="/assets/vendor/datatables/dataTables.buttons.min.js" defer></script>
<script src="/assets/vendor/datatables/buttons.html5.min.js" defer></script>
<script src="/assets/vendor/datatables/buttons.print.min.js" defer></script>
<script>
window._lazyLoadScripts = (function() {
  var cache = {};
  function load(urls) {
    var pending = urls.filter(function(u) { return !cache[u]; });
    if (!pending.length) return Promise.resolve();
    return Promise.all(pending.map(function(url) {
      cache[url] = new Promise(function(resolve, reject) {
        var s = document.createElement('script');
        s.src = url; s.onload = resolve; s.onerror = reject;
        document.body.appendChild(s);
      });
      return cache[url];
    }));
  }
  return {
    jszip: function() { return load(['/assets/vendor/datatables/jszip.min.js']); },
    pdfmake: function() { return load(['/assets/vendor/datatables/pdfmake.min.js', '/assets/vendor/datatables/vfs_fonts.js']); }
  };
})();
document.addEventListener('DOMContentLoaded', function() {
  if (window.jQuery && $.fn.dataTable) {
    $.extend(true, $.fn.dataTable.defaults, {
      language: { url: '/assets/vendor/datatables/Spanish.json' }
    });
    // FontAwesome icons on export buttons
    $.extend(true, $.fn.dataTable.ext.buttons, {
      copyHtml5:  { text: '<i class="fa-solid fa-copy me-1"></i>Copy' },
      csvHtml5:   { text: '<i class="fa-solid fa-file-csv me-1"></i>CSV' },
      excelHtml5:  { text: '<i class="fa-solid fa-file-excel me-1"></i>Excel' },
      pdfHtml5:   { text: '<i class="fa-solid fa-file-pdf me-1"></i>PDF' },
      print:      { text: '<i class="fa-solid fa-print me-1"></i>Print' },
      copy:       { text: '<i class="fa-solid fa-copy me-1"></i>Copy' },
      csv:        { text: '<i class="fa-solid fa-file-csv me-1"></i>CSV' },
      excel:      { text: '<i class="fa-solid fa-file-excel me-1"></i>Excel' },
      pdf:        { text: '<i class="fa-solid fa-file-pdf me-1"></i>PDF' },
      colVis:     { text: '<i class="fa-solid fa-eye me-1"></i>Columns' }
    });
    var origBtnAction = {};
    ['csvHtml5','excelHtml5','pdfHtml5'].forEach(function(key) {
      if ($.fn.dataTable.ext.buttons[key]) {
        origBtnAction[key] = $.fn.dataTable.ext.buttons[key].action;
        $.fn.dataTable.ext.buttons[key].action = function(e, dt, node, config) {
          var self = this, args = arguments;
          var loader = (key === 'pdfHtml5')
            ? Promise.all([window._lazyLoadScripts.jszip(), window._lazyLoadScripts.pdfmake()])
            : window._lazyLoadScripts.jszip();
          loader.then(function() { origBtnAction[key].apply(self, args); });
        };
      }
    });
  }
});
</script>
<?php endif; ?>

<!-- Core app scripts -->
<script src="/assets/js/i18n.js" defer></script>
<script src="/assets/js/app.js" defer></script>

<!-- Page-specific scripts -->
<?php
$pageScripts = array_values(array_filter($pageScripts, static fn(string $s): bool => $s !== 'datatables'));
if (!empty($pageScripts)): ?>
  <?php foreach ($pageScripts as $script): ?>
    <script src="/assets/js/<?= htmlspecialchars($script) ?>.js" defer></script>
  <?php endforeach; ?>
<?php endif; ?>

</body>
</html>
