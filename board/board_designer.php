<?php require('../view/header.php'); ?>

<script type="text/javascript" src="../view/js/jquery-3.2.1.min.js"></script>
<script type="text/javascript" src="../view/js/jquery.boardDesigner.js"></script>

<p><a href='../'>&LT;&LT; back to main</a></p>

<form method="post" action="./">
    
    <input type="hidden" name="action" value="add_board">
    
    <div>
        board name <input type="text" name="boardName" id="board-name">
    </div>

    <div>
        rows 
        <input type="number" name="rowCount" class="board-control" id="rowCount">
        columns 
        <input type="number" name="colCount" class="board-control" id="colCount">
    </div>
    
    <div>
        home col
        <input type="number" name="homeCol" class="board-control" accept=""id="homeCol">
    </div>

    <table id="board">

    </table>
    
    <input type="submit" value="save board">
    
</form>