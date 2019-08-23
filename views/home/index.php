<?php include('views/elements/header.php');?>
 <!-- Content Wrapper -->
 <div id="content-wrapper" class="d-flex flex-column">

<!-- Main Content -->
<div id="content">
<br><br> <!-- maintain space since i removed the top nav bar from the template as it is unnecessary for this project -->
  <!-- Begin Page Content -->
  <div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
      <h1 class="h3 mb-0 text-gray-800">Main</h1>
    </div>


    <!-- Content Row -->

    <div class="row">

      <!-- Area Chart -->
      <div class="col-xl-12">
      <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Data from CSV</h6>
            </div>
            <div class="card-body">
                <span> Export Buttons </span>
              <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <th>First Name</th>
                        <th>Last Name </th>
                        <th>Email</th>
                        <th>Gender</th>
                        <th>IP Address</th>
                    </thead>
                </table>
                <script>
                    //Normally I would never include a script here, but I feel it would make more sense to easily see how this script sets up the data table
                    var dataSet = [<?php 
                    //loop through the rows in php and assign to JS array
                    foreach($data['csv'] as $row)
                        echo '["'.$row[1].'","'.$row[2].'","'.$row[3].'","'.$row[4].'", "'.$row[5].'"],';
                    
                    ?>];

                    $(document).ready(function() {


                        $('#dataTable').DataTable( {
                            dom: 'Bfrtip',
                            data: dataSet, //load the data from the PHP set JS var
                            columns: [
                                { title: "First Name" },
                                { title: "Last Name" },
                                { title: "Email" },
                                { title: "Gender" },
                                { title: "IP Address" }
                            ],
                            "order": [[ 1, "asc" ], [ 0, "asc" ]], //sort the last name asc, then first name asc
                            buttons: [ 
                                'csv',
                                {
                                    text: 'JSON',
                                    action: function ( e, dt, button, config ) {
                                        var data = dt.buttons.exportData();
                    
                                        $.fn.dataTable.fileSave(
                                            new Blob( [ JSON.stringify( data ) ] ),
                                            'Export.json'
                                        );
                                    }
                                },

                                {
                                    text: 'XML',
                                    action: function ( e, dt, button, config ) {
                                        var data = dt.buttons.exportData();
                    
                                        $.fn.dataTable.fileSave(
                                            new Blob( [ json_to_xml(JSON.stringify(data)) ]),
                                            'Export.xml'
                                        );
                                    }
                                },

                                {
                                    text: 'HTML',
                                    action: function ( e, dt, button, config ) {
                                        var data = dt.buttons.exportData();
                    
                                        $.fn.dataTable.fileSave(
                                            new Blob( [ json_to_html(JSON.stringify(data)) ]),
                                            'Export.html'
                                        );
                                    }
                                }
                            ]
                        } );
                    } );
                </script>
              </div>
            </div>
          </div>
      </div>

    
    </div>

    

  </div>
  <!-- /.container-fluid -->

</div>
<!-- End of Main Content -->
<?php include('views/elements/footer.php');?>