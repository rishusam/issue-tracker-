<?php
require_once("DBConnection.php");
if(isset($_GET['id'])){
$qry = $conn->query("SELECT *,rowid FROM `user_list` where rowid = '{$_GET['id']}'");
    foreach($qry->fetchArray() as $k => $v){
        $$k = $v;
    }
}
?>
<div class="container-fluid">
    <form action="" id="user-form">
        <input type="hidden" name="id" value="<?php echo isset($user_id) ? $user_id : '' ?>">
        <div class="form-group">
            <label for="fullname" class="control-label">Full Name</label>
            <input type="text" name="fullname" id="fullname" required class="form-control form-control-sm rounded-0" value="<?php echo isset($fullname) ? $fullname : '' ?>">
        </div>
        <div class="form-group">
            <label for="email" class="control-label">Email</label>
            <input type="email" name="email" id="email" required class="form-control form-control-sm rounded-0" value="<?php echo isset($email) ? $email : '' ?>">
        </div>
        <div class="form-group">
            <label for="contact" class="control-label">Contact</label>
            <input type="text" name="contact" id="contact" required class="form-control form-control-sm rounded-0" value="<?php echo isset($contact) ? $contact : '' ?>">
        </div>
        <div class="form-group">
            <label for="username" class="control-label">Username</label>
            <input type="text" name="username" id="username" required class="form-control form-control-sm rounded-0" value="<?php echo isset($username) ? $username : '' ?>">
        </div>
        <div class="form-group">
            <label for="department_id" class="control-label">Department</label>
            <select name="department_id" id="department_id" class="form-control form-control" required>
                <option value="" disabled <?php echo !isset($department_id) ? 'selected' : '' ?>>Please select here</option>
                <?php 
                    $dept = $conn->query("SELECT *,rowid FROM department_list order by `name` asc");
                    while($row=$dept->fetchArray()):
                ?>
                <option value="<?php echo $row['rowid'] ?>" <?php echo isset($department_id) && $row['rowid'] == $department_id ? 'selected' : '' ?>><?php echo $row['name'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="designation" class="control-label">Designation</label>
            <input type="text" name="designation" id="designation" required class="form-control form-control-sm rounded-0" value="<?php echo isset($designation) ? $designation : '' ?>">
        </div>
        <div class="form-group">
            <label for="type" class="control-label">Type</label>
            <select name="type" id="type" class="form-control form-control" required>
                <option value="1" <?php echo isset($type) && $type == 1 ? 'selected' : '' ?>>Administrator</option>
                <option value="2" <?php echo isset($type) && $type == 2 ? 'selected' : '' ?>>Employee</option>
            </select>
        </div>
    </form>
</div>

<script>
    $(function(){
        $('#user-form').submit(function(e){
            e.preventDefault();
            $('.pop_msg').remove()
            var _this = $(this)
            var _el = $('<div>')
                _el.addClass('pop_msg')
            $('#uni_modal button').attr('disabled',true)
            $('#uni_modal button[type="submit"]').text('submitting form...')
            $.ajax({
                url:'Actions.php?a=save_user',
                method:'POST',
                data:$(this).serialize(),
                dataType:'JSON',
                error:err=>{
                    console.log(err)
                    _el.addClass('alert alert-danger')
                    _el.text("An error occurred.")
                    _this.prepend(_el)
                    _el.show('slow')
                     $('#uni_modal button').attr('disabled',false)
                     $('#uni_modal button[type="submit"]').text('Save')
                },
                success:function(resp){
                    if(resp.status == 'success'){
                        _el.addClass('alert alert-success')
                        $('#uni_modal').on('hide.bs.modal',function(){
                            location.reload()
                        })
                        _this.get(0).reset();
                    }else{
                        _el.addClass('alert alert-danger')
                    }
                    _el.text(resp.msg)

                    _el.hide()
                    _this.prepend(_el)
                    _el.show('slow')
                     $('#uni_modal button').attr('disabled',false)
                     $('#uni_modal button[type="submit"]').text('Save')
                }
            })
        })
    })
</script>