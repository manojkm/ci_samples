		<div class="row">
            <div class="col-md-6">
              <div class="box box-primary">
				<div class="box-header with-border">
         <h3 class="box-title">View Customer Details</h3>
         <div class="box-tools pull-right">
            <button class="btn btn-info btn-sm" title="" data-toggle="tooltip" data-widget="collapse" data-original-title="Collapse">
            <i class="fa fa-minus"></i>
            </button>
         </div>
      </div>
                <div class="box-body">
                  <dl>
                    <dt>UserName</dt>
                    <dd><?php echo $data->username; ?></dd>               
                    <dt>First Name</dt>
                    <dd><?php echo $data->first_name; ?></dd>
                    <dt>Last Name</dt>
                    <dd><?php echo $data->last_name; ?></dd>
                    <dt>Email</dt>
                    <dd><?php echo $data->email; ?></dd>
					<dt>Phone Number</dt>
                    <dd><?php echo $data->phone_number; ?></dd>                                    
                  </dl>
                </div><!-- /.box-body -->
              </div><!-- /.box -->
            </div><!-- ./col -->
            
            <div class="col-md-6">
              <div class="box box-primary">
               <div class="box-header with-border">
         <h3 class="box-title">View Customer Details</h3>
         <div class="box-tools pull-right">
            <button class="btn btn-info btn-sm" title="" data-toggle="tooltip" data-widget="collapse" data-original-title="Collapse">
            <i class="fa fa-minus"></i>
            </button>
         </div>
      </div>
                <div class="box-body">
                  <dl>
                    <dt>created_by</dt>
                    <dd><?php echo $data->first_name; ?></dd>				  
                    <dt>City</dt>
                    <dd><?php echo $data->city; ?></dd>
					<dt>Country</dt>
                    <dd><?php echo $data->country; ?></dd>            			
					<dt>Profile Picture</dt>
                    <img src="<?php echo $data->profile_picture; ?>" width="100px" height="100px" alt="Picture Not Found" />
                  </dl>
                </div><!-- /.box-body -->
              </div><!-- /.box -->
            </div><!-- ./col -->
          </div>  
			
