<?php
// Public demo page: shows a read-only sample patients table for demo purposes.
include __DIR__ . '/../templates/header.php';
?>
<div class="py-5">
  <div class="container">
    <h2 class="mb-3">Demo — Sample Patients (read-only)</h2>
    <p class="text-muted">This is a public, read-only demo view so you can explore the UI without signing in.</p>

    <table class="table table-striped" id="demoPatientsTable">
      <thead>
        <tr><th>ID</th><th>Name</th><th>DOB</th><th>Phone</th><th>Notes</th></tr>
      </thead>
      <tbody>
        <tr><td>1</td><td>María López</td><td>1980-04-12</td><td>+505 2222-1111</td><td>Allergic to penicillin</td></tr>
        <tr><td>2</td><td>Carlos Martínez</td><td>1990-09-03</td><td>+505 3333-2222</td><td>Diabetic</td></tr>
        <tr><td>3</td><td>Ana González</td><td>1975-12-30</td><td>+505 4444-3333</td><td>Requires wheelchair access</td></tr>
      </tbody>
    </table>

    <div class="mt-4">
      <a href="/" class="btn btn-link">Back to home</a>
      <a href="/login.php" class="btn btn-primary">Sign in</a>
    </div>
  </div>
</div>
<script type="module">
    import DataTable from 'datatables.net-dt';
    $(document).ready(function() {
        $('#demoPatientsTable').DataTable();
    });
</script>
<?php include __DIR__ . '/../templates/footer.php';


