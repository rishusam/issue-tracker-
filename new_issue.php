<?php
require_once("DBConnection.php");

?>
<div class="container-fluid">
    <form action="" id="issue-form">
        <input type="hidden" name="id" value="<?php echo isset($rowid) ? $rowid : '' ?>">
        <div class="form-group">
            <label for="title" class="control-label">Title</label>
            <input type="text" name="title" id="title" required class="form-control form-control-sm rounded-0" value="<?php echo isset($title) ? $title : '' ?>">
        </div>
        <div class="form-group">
            <label for="department_id" class="control-label">Assign to Department</label>
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
            <label for="description" class="control-label">Description</label>
            <textarea name="description" id="description" cols="30" rows="4" class="form-control rounded-0"></textarea>
        </div>
    </form>
</div>

<script>
    $(function(){
        $('#issue-form').submit(function(e){
            e.preventDefault();
            $('.pop_msg').remove()
            var _this = $(this)
            var _el = $('<div>')
                _el.addClass('pop_msg')
            $('#uni_modal button').attr('disabled',true)
            $('#uni_modal button[type="submit"]').text('submitting form...')
            $.ajax({
                url:'Actions.php?a=save_issue',
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