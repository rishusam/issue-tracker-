<h3>Issue Ticket List</h3>
<small>Below are the list of issues posted into your department</small>
<hr>
<div class="row gx-3 row-cols-4">
    <?php 
        $sql = "SELECT i.*,u.fullname, d.name from issue_list i inner join department_list d on i.department_id = d.rowid inner join user_list u on i.user_id = u.user_id where i.department_id = '{$_SESSION['department_id']}' ORDER BY i.`status` asc, strftime('%s', i.date_created) desc";
        $qry = $conn->query($sql);
        while($row = $qry->fetchArray()):
    ?>
    <div class="col">
        <div class="w-100 bg-dark text-light bg-gradient opcaity-70 py-3 px-2">
            <h5 class="truncate-1 border-bottom border-light" title="<?php echo $row['title'] ?>"><b><?php echo $row['title'] ?></b></h5>
            <small class="truncate-3">
            <?php 
                echo $row['description'];
            ?>
            </small>
            <?php if($_SESSION['type'] == 1): ?>
                <small>By: <?php echo $row['fullname'] ?></small>
                <small>To: <?php echo $row['name'] ?></small>
            <?php endif ?>
            
            <div class="w-100 d-flex justify-content-between align-items-middle mt-3">
                <?php
                if($row['status'] == 0 ){
                    echo "<span class='w-auto badge bg-success'>Open</span>";
                }else{
                    echo "<span class='w-auto badge bg-danger'>Closed</span>";
                }
                ?>
                <a class="btn btn-sm btn-primary col-auto py-0 rounded-0" href="./?page=view_issue&id=<?php echo md5($row['issue_id']) ?>">View</a>
            </div>
        </div>
    </div>
    <?php endwhile; ?>
</div>
<?php if(!$qry->fetchArray()): ?>
<div class="w-100 text-center">No issue listed yet.</div>
<?php endif; ?>

<script>
$(function(){
    $('#create_new').click(function(){
        uni_modal('Add New Issue',"new_issue.php")
    })
    $('.edit_data').click(function(){
        uni_modal('Edit Issue Details',"manage_user.php?id="+$(this).attr('data-id'))
    })
    $('.delete_data').click(function(){
        _conf("Are you sure to delete <b>"+$(this).attr('data-name')+"</b> from list?",'delete_data',[$(this).attr('data-id')])
    })
})
</script>