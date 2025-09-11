<!DOCTYPE html>
<html lang="en">

<head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>Inquiry List</title>
     <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
     <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>


<body class="bg-light">
     <div class="container mt-4">
          <div class="row">
               <div class="col-12">
                    <div class="card shadow">
                         <div class="card-header bg-primary text-white">
                              <h4 class="mb-0">
                                   <i class="fas fa-question-circle me-2"></i>
                                   Inquiry List
                              </h4>
                         </div>
                         <div class="card-body">
                              <div class="table-responsive">
                                   <table class="table table-striped table-hover">
                                        <thead class="table-head">
                                             <tr>
                                                  <th scope="col">#</th>
                                                  <th scope="col">Type</th>
                                                  <th scope="col">Name</th>
                                                  <th scope="col">Phone</th>
                                                  <th scope="col">Departure Place</th>
                                                  <th scope="col">Arrival Place</th>
                                                  <th scope="col">Departure Date</th>
                                                  <th scope="col">Arrival Date</th>
                                                  <!-- <th scope="col">Created At</th> -->
                                             </tr>
                                        </thead>
                                        <tbody>
                                             <?php if (!empty($inquiries)): foreach ($inquiries as $item): ?>
                                                       <tr>
                                                            <th scope="row"><?php echo $item['id']; ?></th>
                                                            <td><?php echo $item['type']; ?></td>
                                                            <td><?php echo $item['full_name']; ?></td>
                                                            <td><?php echo $item['phone']; ?></td>
                                                            <td><?php echo $item['departure_place']; ?></td>
                                                            <td><?php echo $item['arrival_place']; ?></td>
                                                            <td><?php echo $item['departure_date']; ?></td>
                                                            <td><?php echo $item['arrival_date']; ?></td>
                                                            <!-- <td><?php echo date('Y-m-d H:i', strtotime($item['created_at'])); ?></td> -->
                                                            <!-- <td>john.smith@email.com</td>
                                                            <td>Product Information</td>
                                                            <td class="text-truncate" style="max-width: 200px;">
                                                                 I would like to know more about your product features and pricing...
                                                            </td>
                                                            <td>2024-01-15</td>

                                                            <td>
                                                                 <button class="btn btn-sm btn-outline-primary me-1" title="View">
                                                                      <i class="fas fa-eye"></i>
                                                                 </button>
                                                                 <button class="btn btn-sm btn-outline-success me-1" title="Reply">
                                                                      <i class="fas fa-reply"></i>
                                                                 </button>
                                                                 <button class="btn btn-sm btn-outline-danger" title="Delete">
                                                                      <i class="fas fa-trash"></i>
                                                                 </button>
                                                            </td> -->
                                                       </tr>
                                                  <?php endforeach; ?>
                                                  <div class="col-8 offset-4">
                                                       <?= $this->pagination->create_links(); ?>
                                                  </div>
                                             <?php else : ?>
                                                  <tr>
                                                       <td colspan="7" class="text-center">No inquiries found.</td>
                                                  </tr>

                                             <?php endif ?>
                                        </tbody>
                                   </table>
                              </div>

                              <!-- Pagination -->
                              <nav aria-label="Inquiry pagination">
                                   <ul class="pagination justify-content-center mt-4">
                                        <li class="page-item disabled">
                                             <a class="page-link" href="#" tabindex="-1">Previous</a>
                                        </li>
                                        <li class="page-item active">
                                             <a class="page-link" href="#">1</a>
                                        </li>
                                        <li class="page-item">
                                             <a class="page-link" href="#">2</a>
                                        </li>
                                        <li class="page-item">
                                             <a class="page-link" href="#">3</a>
                                        </li>
                                        <li class="page-item">
                                             <a class="page-link" href="#">Next</a>
                                        </li>
                                   </ul>
                              </nav>

                              <!-- Summary Statistics -->
                              <!-- <div class="row mt-4">
                                   <div class="col-md-3">
                                        <div class="card text-center border-warning">
                                             <div class="card-body">
                                                  <h5 class="card-title text-warning">2</h5>
                                                  <p class="card-text">Pending</p>
                                             </div>
                                        </div>
                                   </div>
                                   <div class="col-md-3">
                                        <div class="card text-center border-info">
                                             <div class="card-body">
                                                  <h5 class="card-title text-info">1</h5>
                                                  <p class="card-text">In Progress</p>
                                             </div>
                                        </div>
                                   </div>
                                   <div class="col-md-3">
                                        <div class="card text-center border-success">
                                             <div class="card-body">
                                                  <h5 class="card-title text-success">1</h5>
                                                  <p class="card-text">Resolved</p>
                                             </div>
                                        </div>
                                   </div>
                                   <div class="col-md-3">
                                        <div class="card text-center border-danger">
                                             <div class="card-body">
                                                  <h5 class="card-title text-danger">1</h5>
                                                  <p class="card-text">Urgent</p>
                                             </div>
                                        </div>
                                   </div>
                              </div> -->
                         </div>
                    </div>
               </div>
          </div>
     </div>

     <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
     <script>
          // Add click handlers for action buttons
          document.addEventListener('DOMContentLoaded', function() {
               // View buttons
               document.querySelectorAll('.btn-outline-primary').forEach(button => {
                    button.addEventListener('click', function() {
                         const row = this.closest('tr');
                         const id = row.querySelector('th').textContent;
                         alert('Viewing inquiry #' + id);
                    });
               });

               // Reply buttons
               document.querySelectorAll('.btn-outline-success').forEach(button => {
                    button.addEventListener('click', function() {
                         const row = this.closest('tr');
                         const id = row.querySelector('th').textContent;
                         alert('Replying to inquiry #' + id);
                    });
               });

               // Delete buttons
               document.querySelectorAll('.btn-outline-danger').forEach(button => {
                    button.addEventListener('click', function() {
                         const row = this.closest('tr');
                         const id = row.querySelector('th').textContent;
                         if (confirm('Are you sure you want to delete inquiry #' + id + '?')) {
                              row.remove();
                              alert('Inquiry #' + id + ' deleted');
                         }
                    });
               });
          });
     </script>
</body>

</html>