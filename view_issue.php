<?php 
$sql = "SELECT i.*,u.fullname, d.name from issue_list i inner join department_list d on i.department_id = d.rowid inner join user_list u on i.user_id = u.user_id where md5(i.issue_id) = '{$_GET['id']}'";
$res = $conn->query($sql)->fetchArray();
?>
<div class="row justify-content-end px-3 mb-2">
    <?php if(($_SESSION['type'] == 1 || $_SESSION['department_id'] == $res['department_id']) && $res['status'] == 0): ?>
        <button class="btn btn-sm btn-primary me-2 close_issue rounded-0 col-auto" type="button" data-id= '<?php echo $res['issue_id'] ?>' >Close Issue</button>
    <?php endif; ?>
    <?php if($_SESSION['type'] == 1 || $_SESSION['user_id'] == $res['user_id']): ?>
        <button class="btn btn-sm btn-danger delete_data rounded-0 col-auto" type="button" data-id= '<?php echo $res['issue_id'] ?>' >Delete Issue</button>
    <?php endif; ?>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body pb-4">
                <h4><b>Issue: <?php echo $res['title'] ?></b></h4>
                <div class="row">
                    <div class="col-sm-6"> 
                        <span><small>Posted by: <b><?php echo  $res['fullname'] ?></b></small></span><br>
                        <span><small>Posted for: <?php echo  $res['name'] ?></small></span> 
                    </div>
                    <div class="col-sm-6">
                        <span><small>Status:&nbsp;
                            <?php
                            if($res['status'] == 0 ){
                                echo "<span class='badge bg-success'>Open</span>";
                            }else{
                                echo "<span class='badge bg-danger'>Closed</span>";
                            }
                            ?>
                        </small></span><br>
                        <span><small>Date Posted: <?php echo  date("Y-m-d H:i",strtotime($res['date_created'])) ?></small></span> 
                    </div>
                </div>
               

            <hr class="border-light">
            <?php echo str_replace("\n\r","<br>",$res['description']) ?>
            </div>
        </div>
    </div>
</div>
<div class="py-3">
    <div class="row mb-2">
        <div class="col-md-8">
            <h5><b>Comment/s:</b></h5>
            <hr>
            <ul class="list-group">
                <?php 
                $com_qry = $conn->query("SELECT c.*,u.fullname FROM comment_list c inner join user_list u on c.user_id = u.user_id where md5(c.issue_id) = '{$_GET['id']}' ORDER BY strftime('%s', c.date_created) asc");
                while($row= $com_qry->fetchArray()):
                ?>
                <li class='list-group-item'>
                    <div class="w-100 border-bottom border-dark">
                        <p class="m-0"><?php echo $row['fullname'] ?></p>
                        <small><?php echo date("Y-m-d H:i",strtotime($row['date_created'])) ?></small>
                    </div>
                    <div class="w-100 pl-4 py-3 comment-field">
                        <?php echo $row['comment'] ?>
                    </div>
                    <div class="row justify-content-end px-3">
                        <?php if($_SESSION['type'] == 1 || $_SESSION['user_id'] == $row['user_id']): ?>
                            <button class="btn btn-sm btn-primary edit_comment rounded-0 col-auto me-2" type="button" data-id= '<?php echo $row['comment_id'] ?>' >Edit</button>
                            <button class="btn btn-sm btn-danger delete_comment rounded-0 col-auto" type="button" data-id= '<?php echo $row['comment_id'] ?>' >Delete</button>
                        <?php endif; ?>
                    </div>
                </li>
                <?php endwhile; ?>
                <?php if(!$com_qry->fetchArray()): ?>
                    <li class="text-center list-group-item">No comment listed yet.</li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
    <div class="row">
        <div class="col-md-8">
            <form action="" id="comment-form">
                <div class="form-group">
                    <input type="hidden" name="issue_id" value="<?php echo $res['issue_id'] ?>">
                    <input type="hidden" name="id">
                    <textarea name="comment" id="comment" rows="4" class="form-control rounded-0" required placeholder="Write your comment here."></textarea>  
                </div> 
                <div class="form-group row justify-content-end py-2 px-3">
                    <button class="btn btn-sm rounded-0 btn-primary col-auto me-2">Save</button>
                    <button class="btn btn-sm rounded-0 btn-secondary col-auto" type="button" onclick = "$('#comment-form').get(0).reset()">Cancel</button>
                </div> 
            </form>
        </div>
    </div>
</div>

<script>
    $(function(){
        $('.edit_comment').click(function(){
            $('[name="id"]').val($(this).attr('data-id'))
            $('[name="comment"]').val($(this).parent().siblings('.comment-field').text().trim())
            $('[name="comment"]').focus()
            $('html,body').animate({scrollTop: document.height}, 'fast')
        })
        $('.delete_data').click(function(){
            _conf("Are you sure to delete this issue?",'delete_data',[$(this).attr('data-id')])
        })
        $('.close_issue').click(function(){
            _conf("Are you sure to close this issue?",'close_issue',[$(this).attr('data-id')])
        })
        $('.delete_comment').click(function(){
            _conf("Are you sure to delete the selected comment?",'delete_comment',[$(this).attr('data-id')])
        })
        $('#comment-form').on('reset',function(){
            $('[name="id"]').val('')
        })
        $('#comment-form').submit(function(e){
            e.preventDefault();
            $('.pop_msg').remove()
            var _this = $(this)
            var _el = $('<div>')
                _el.addClass('pop_msg')
            _this.find('button').attr('disabled',true)
            _this.find('button[type="submit"]').text('Saving...')
            $.ajax({
                url:'Actions.php?a=save_comment',
                method:'POST',
                data:$(this).serialize(),
                dataType:'JSON',
                error:err=>{
                    console.log(err)
                    _el.addClass('alert alert-danger')
                    _el.text("An error occurred.")
                    _this.prepend(_el)
                    _el.show('slow')
                     _this.find('button').attr('disabled',false)
                     _this.find('button[type="submit"]').text('Save')
                },
                success:function(resp){
                    if(resp.status == 'success'){
                        _el.addClass('alert alert-success')
                        setTimeout(() => {
                            location.reload()
                        }, 1500);
                        _this.get(0).reset();
                    }else{
                        _el.addClass('alert alert-danger')
                    }
                    _el.text(resp.msg)

                    _el.hide()
                    _this.prepend(_el)
                    _el.show('slow')
                     _this.find('button').attr('disabled',false)
                     _this.find('button[type="submit"]').text('Save')
                }
            })
        })
    })
    function delete_data($id){
        $('#confirm_modal button').attr('disabled',true)
        $.ajax({
            url:'Actions.php?a=delete_issue',
            method:'POST',
            data:{id:$id},
            dataType:'JSON',
            error:err=>{
                consolre.log(err)
                alert("An error occurred.")
                $('#confirm_modal button').attr('disabled',false)
            },
            success:function(resp){
                if(resp.status == 'success'){
                    location.replace('./?page=issues')
                }else{
                    alert("An error occurred.")
                    $('#confirm_modal button').attr('disabled',false)
                }
            }
        })
    }
    function close_issue($id){
        $('#confirm_modal button').attr('disabled',true)
        $.ajax({
            url:'Actions.php?a=close_issue',
            method:'POST',
            data:{id:$id},
            dataType:'JSON',
            error:err=>{
                consolre.log(err)
                alert("An error occurred.")
                $('#confirm_modal button').attr('disabled',false)
            },
            success:function(resp){
                if(resp.status == 'success'){
                    location.reload()
                }else{
                    alert("An error occurred.")
                    $('#confirm_modal button').attr('disabled',false)
                }
            }
        })
    }
    function delete_comment($id){
        $('#confirm_modal button').attr('disabled',true)
        $.ajax({
            url:'Actions.php?a=delete_comment',
            method:'POST',
            data:{id:$id},
            dataType:'JSON',
            error:err=>{
                consolre.log(err)
                alert("An error occurred.")
                $('#confirm_modal button').attr('disabled',false)
            },
            success:function(resp){
                if(resp.status == 'success'){
                    location.reload()
                }else{
                    alert("An error occurred.")
                    $('#confirm_modal button').attr('disabled',false)
                }
            }
        })
    }
</script>