<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reports</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
  <div class="container mt-5">
    <h1 class="text-center">Generate Reports</h1>
    <form id="reportForm" class="mt-4">
      <div class="row mb-3">
        <div class="col-md-5">
          <label for="startDate" class="form-label">Start Date</label>
          <input type="date" class="form-control" id="startDate" name="start_date" required>
        </div>
        <div class="col-md-5">
          <label for="endDate" class="form-label">End Date</label>
          <input type="date" class="form-control" id="endDate" name="end_date" required>
        </div>
        <div class="col-md-2">
          <label for="format" class="form-label">Format</label>
          <select id="format" class="form-select" name="format" required>
            <option value="excel">Excel</option>
            <option value="pdf">PDF</option>
            <option value="word">Word</option>
          </select>
        </div>
      </div>
      <button type="submit" class="btn btn-primary">Generate Report</button>
    </form>
    <div id="errorMessage" class="text-danger mt-3" style="display: none;"></div>
  </div>

  <script>
    $('#reportForm').on('submit', function(e) {
      e.preventDefault();
      const formData = $(this).serialize();
      $.ajax({
        type: 'POST',
        url: 'reports.php',
        data: formData,
        success: function(response) {
          if (response.error) {
            $('#errorMessage').text(response.error).show();
          } else {
            $('#errorMessage').hide();
          }
        },
        error: function() {
          $('#errorMessage').text('An unexpected error occurred. Please try again.').show();
        }
      });
    });
  </script>
</body>

</html>