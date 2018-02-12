<?php require('../view/header.php'); ?>

<p><a href='../'>&LT;&LT; back to main</a></p>

<h2>List matches</h2>

<table>
    
    <tr>
        <th>id</th>
        <th>name</th>
        <th>board_id</th>
        <th>white_user_id</th>
        <th>black_user_id</th>
        <th>status</th>
    </tr>
    
    <?php
    
    //print_r($matches);
    
    foreach ($matches as $match) {
        
        echo '<tr>';
        
        echo "<td>{$match['match_id']}</td>";
        echo "<td>{$match['match_name']}</td>";
        echo "<td>{$match['board_id']}</td>";
        echo "<td>{$match['match_white_user_id']}</td>";
        echo "<td>{$match['match_black_user_id']}</td>";
        echo "<td>{$match_status_enum[$match['match_status']]}</td>";
        
        echo '</tr>';
        
    }
    
    ?>
    
</table>