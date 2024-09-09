<td>
    <?php 
    if (isset($project['id'])) {
        $project_id = $project['id'];
        $current_status = isset($project['status']) ? $project['status'] : 'pending';

        if (in_array($current_status, ['manager_approved', 'studio_done', 'workshop_done', 'accounts_done'])) {
            echo '<span class="badge badge-success">Approved</span>';
        } else {
            ?>
            <form method="POST" action="">
                <input type="hidden" name="approve_jobcard" value="<?php echo $project_id; ?>">
                <button type="submit" class="btn btn-success btn-sm">Approve</button>
            </form>
            <?php
        }
    } else {
        echo '<span class="badge badge-warning">ID Missing</span>';
    }
    ?>
</td>
