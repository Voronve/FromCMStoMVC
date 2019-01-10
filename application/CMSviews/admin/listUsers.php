<h1>Users</h1>

    <?php if ( isset( $results['errorMessage'] ) ) { ?>
            <div class="errorMessage"><?php echo $results['errorMessage'] ?></div>
    <?php } ?>


    <?php if ( isset( $results['statusMessage'] ) ) { ?>
            <div class="statusMessage"><?php echo $results['statusMessage'] ?></div>
    <?php } ?>

          <table>
            <tr>
              <th>Имя пользователя</th>
              <th>Активность</th>
            </tr>
            
    <?php foreach ( $results['users'] as $user ) { ?>

            <tr onclick="location='admin.php?action=editUser&amp;userId=<?php echo $user->id?>'">
              <td>
                <?php echo $user->name?>
              </td>
           
			  <td>
			  <?php 
				if($user->active){
					echo 'Активен';
				}else{
					echo 'Неактивен';
				}  	  
			  ?>
			  </td>
            </tr>

    <?php } ?>

          </table>

          <p><?php echo $results['totalRows']?> user<?php echo ( $results['totalRows'] != 1 ) ? 's' : '' ?> in total.</p>

          <p><a href="admin.php?action=newUser">Add a New User</a></p>
